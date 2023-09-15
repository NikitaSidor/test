<?php

class Countries {
    var $modx = null;
    var $fileRegister = array();
    
    function __construct(&$modx) {
        $this->modx = $modx;
    }

    function getFileContents($file) {
    	if (empty($file)) {
    		return false;
    	} else {
	    	$file = realpath(__DIR__).'/templates/'.$file;
	    	if(array_key_exists($file, $this->fileRegister)) {
	    		return $this->fileRegister[$file];
	    	} else {
	    		$contents = file_get_contents($file);
	    		$this->fileRegister[$file] = $contents;
	    		return $contents;
	    	}
	    }
    }
    
    function loadTemplates() {
    	$this->fileGetContents('main.tpl');
    }

	function showPage($page=false) {
        global $modx;
        $showDefaultHeaderFooter = true;
		switch ($page) {
			// case 'occupancy':
            default:
                $contentTpl = $this->showPageIndex();
        }
        if ($showDefaultHeaderFooter)
            include_once(MODX_MANAGER_PATH."/includes/header.inc.php");
        include(__DIR__.'/templates/actions.php');
        echo $contentTpl;
        if ($showDefaultHeaderFooter)
		    include_once(MODX_MANAGER_PATH."/includes/footer.inc.php");
		exit;
	}

    function parseTemplate($tpl, $values = null) {
        global $modx;
		// if (is_null($values))
		// 	$values = $this->ph;
    	$tpl = rtrim($tpl,'.tpl').'.tpl';
		$tpl = array_key_exists($tpl, $this->fileRegister) ? $this->fileRegister[$tpl] : $this->getFileContents($tpl);
    	if($tpl) {
    		// if(!isset($this->modx->config['mgr_jquery_path']))  $this->modx->config['mgr_jquery_path'] = 'media/script/jquery/jquery.min.js';
    		// $tpl = $this->modx->mergeSettingsContent($tpl);
    		if (!empty($values)) {
				foreach ($values as $key => $value) {
					$tpl = str_replace('[+'.$key.'+]', $value, $tpl); 
				}
			}
    		$tpl = preg_replace('/(\[\+.*?\+\])/' ,'', $tpl);
    		return $tpl;
    	} else {
    		return '';
    	}
    }

	function doAction($action) {
        if (method_exists($this,$action))
            $this->$action();
		exit;
    }
    
    function answer($msg='success', $success=false) {
		header('Content-Type: application/json');
		die(json_encode(array(
			'msg'=>$msg,
			'status'=>$success?'success':'error'
		)));
    }
    
    /*
     * Страницы
     */
    function showPageIndex() {
        // Получим данные по странам, которые заведены в админке
        $cli = $this->getCountriesListInfo();
        foreach ($cli as &$cliItem)
            $cliItem['displayTemperatureRow'] = false;
        // var_dump($cli);
        $countriesJson = json_encode($cli);
        // var_dump($countriesJson);
		return $this->parseTemplate('index',array(
            'countriesJson' => $countriesJson
        ));
    }

    /*
     * Действия
     */
    // Меняет статус Виза / Безвиз
    protected function changeCountryVisaState() {
        if (!isset($_POST['data']['countryCode']) && !isset($_POST['data']['visaState']))
            die('Wrong request');
		$result = $this->modx->db->query("
			UPDATE ".$this->modx->getFullTableName('countries_info')."
			SET visa = {$_POST['data']['visaState']}
  			WHERE code = '{$_POST['data']['countryCode']}'
        ");
		if( $result )
			die('success');
		else
			$this->answer('Ошибка при сохранении данных');
    }

    // Меняет Цена тура от
    protected function changeCountryPriceFrom() {
        if (!isset($_POST['data']['countryCode']) && !isset($_POST['data']['priceFrom']))
            die('Wrong request');
        if ($_POST['data']['priceFrom'] == '')
            $_POST['data']['priceFrom'] = 'NULL';
		$result = $this->modx->db->query("
			UPDATE ".$this->modx->getFullTableName('countries_info')."
			SET price_from = {$_POST['data']['priceFrom']}
  			WHERE code = '{$_POST['data']['countryCode']}'
        ");
		if( $result )
			die('success');
		else
			$this->answer('Ошибка при сохранении данных');
    }

    // Меняет температуру по месяцу и типу - вода/воздух
    protected function changeCountryTemperature() {
        if (!isset($_POST['data']['countryCode']))
            die('Wrong request');
        if (!is_numeric($_POST['data']['temperature']) || $_POST['data']['temperature'] == '')
            $_POST['data']['temperature'] = 'NULL';
		$result = $this->modx->db->query("
			UPDATE ".$this->modx->getFullTableName('countries_temperature')."
			SET {$_POST['data']['type']}_{$_POST['data']['month']} = {$_POST['data']['temperature']}
  			WHERE code = '{$_POST['data']['countryCode']}'
        ");
        echo "
        UPDATE ".$this->modx->getFullTableName('countries_temperature')."
        SET {$_POST['data']['type']}_{$_POST['data']['month']} = {$_POST['data']['temperature']}
          WHERE code = '{$_POST['data']['countryCode']}'
    ";
		if( $result )
			die('success');
		else
			$this->answer('Ошибка при сохранении данных');
    }

    /*
     * Для сниппетов
     */
    function getDepartureCountriesCalendarData() {
        $data = $this->getCountriesListInfo();
        // foreach ($data as &$d) {
        //     $d['id'] = $d['contentid'];
        //     unset($d['contentid']);
        //     $d['code'] = $d['value'];
        //     unset($d['value']);
        //     $d['title'] = $d['pagetitle'];
        //     unset($d['pagetitle']);
        // }
        return $data;
    }
    
    /*
     * Получение данных
     */
    protected function getCountriesListInfo($countries = 'used') {
        // Получим список актуальных стран, на основе папки "Страны"
        $ccList = $this->getContentCountriesList();
        $ccIds = $ccCodes = $ccNames = $ccInfo = $list = Array();
        foreach ($ccList as $cc) {
            // $ccIds[$cc['value']] = $cc['contentid']; // ID стран
            // $ccNames[$cc['contentid']] = $cc['pagetitle']; // Названия стран
            $ccCodes[$cc['contentid']] = $cc['code']; // Коды ISO
            $list[$cc['code']] = $cc;
        }
        // Получим данные по погоде
        $countriesTemperature = $this->getCountriesTemperature($ccCodes);
        // var_dump($countriesTemperature);
        foreach($countriesTemperature as $code => $ctData) {
            foreach ($ctData as $monthCode => $mcData) {
                $list[$code]['temperature'][$monthCode] = $mcData;
            }
        }
        // Получим доп. данные про страны
        $ccInfoDb = $this->getCountriesInfo($ccCodes);
        foreach ($ccInfoDb as $cci) {
            $code = $cci['code'];
            unset($cci['code']);
            $list[$code]['info'] = $cci;
        }
        return $list;
    }
    protected function getContentCountriesList($keys=false) {
        $usedCountriesCodes = Array();
        $result = $this->modx->db->query("SELECT sc.pagetitle, tcv.contentid, tcv.value as code FROM ".$this->modx->getFullTableName('site_tmplvar_contentvalues')." AS tcv 
        LEFT JOIN ".$this->modx->getFullTableName('site_content')." AS sc ON sc.id = tcv.contentid
        WHERE tcv.tmplvarid = 12");
        switch ($keys) {
            case 'value':
                while($row = $this->modx->db->getRow($result))
                    $usedCountriesCodes[] = $row['code'];
                break;
            case 'id':
            case 'cid':
            case 'contentid':
                while($row = $this->modx->db->getRow($result))
                    $usedCountriesCodes[] = $row['contentid'];
                break;
            default:
                while($row = $this->modx->db->getRow($result))
                    $usedCountriesCodes[] = $row;
        }
        return $usedCountriesCodes;
    }
    protected function getCountriesTemperature($codes=array(), $raw = false) {
        $ct = Array();
        if (is_array($codes) && !empty($codes))
            $result = $this->modx->db->query("SELECT * FROM ".$this->modx->getFullTableName('countries_temperature')." WHERE code IN ('"
            .implode("', '",$codes)
            ."')");
        else
            $result = $this->modx->db->query("SELECT * FROM ".$this->modx->getFullTableName('countries_temperature'));
        
        if ($raw) // поля все подряд
            while($row = $this->modx->db->getRow($result))
                $ct[] = $row;
        else // сортировка по месяцу
            while($row = $this->modx->db->getRow($result)) 
                foreach ($row as $k=>$v)
                    if ($k != 'code') {
                        $kk = explode('_',$k);
                        $ct[$row['code']][$kk[1]][$kk[0]] = $v;
                        $ct[$row['code']][$kk[1]]['ruShortName'] = $this->getMonthName($kk[1]);
                    }
        return $ct;
    }
    protected function getCountriesInfo($codes=array()) {
        $ci = Array();
        if (is_array($codes) && !empty($codes))
            $result = $this->modx->db->query("SELECT * FROM ".$this->modx->getFullTableName('countries_info')." WHERE code IN ('"
            .implode("', '",$codes)
            ."')");
        else
            $result = $this->modx->db->query("SELECT * FROM ".$this->modx->getFullTableName('countries_info'));
        while($row = $this->modx->db->getRow($result))
            $ci[] = $row;
        return $ci;
    }

    function getMonthsName($full=false) {
        if ($full)
            return Array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
        else 
            return Array('Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек');
    }
    function getMonthName($key) {
        $names = $this->getMonthsName();
        if (is_numeric($key)) {
            if (isset($names[$key-1]))
                return $names[$key-1];
        } else {
            $monthNamesEngShort = Array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
            $index = array_search($key,$monthNamesEngShort);
            return $names[$index];
        }
    }
}