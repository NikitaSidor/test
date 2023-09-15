<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

class Provider
{
	const CACHE_FILE = 'cache/toursdata-{id}.json';
	const CACHE_LIFETIME = 86400;
	const API_URL = 'http://api.tourhotel24.ru/sletat';
	const API_METHOD = 'getminprice';
	const API_TOKEN = '9620bf9c6db77abbedc96995ec61f9d3';

	public $fromCity; // город отправления
	public $resorts; // курорты
	public $curResorts=''; // курорты ресурса
	public $curCountries=''; // страны ресурса
	public $resortNames;

	protected $parentRid = 83; // родитель лендингов стран

	private $modx = null;
	private $cache_file_path = '';
	private $apiLogin = '';
	private $apiToken;
	
	public function __construct(&$modx=false)
	{
		if ($modx) {
			$this->modx = $modx;
			// для разделения кэш-файлов для разных лэндингов мы добавили суффикс идентификатора страницы лэндинга в имени кеш-файла.
            $this->cache_file_path = __DIR__ . '/' . str_replace('{id}', $this->modx->documentIdentifier, self::CACHE_FILE);
            
			if(!file_exists($this->cache_file_path))
				$this->setCache('');
			$this->cache_file_path = realpath($this->cache_file_path);
			if (empty($this->apiLogin))
				$this->apiLogin = $_SERVER['SERVER_NAME'];
			$this->apiToken = $this->getInfoData('token');
			// var_dump($this->getInfoData('toCities'));
			if (!is_numeric($this->apiToken))
				return false;
        }
        
        
	}

	// Получение данных с фронта
	public function getCards($params=Array()) {
		if (empty($params['resort']) && empty($params['country']) && empty($params['rid'])) {
			echo "Укажите Курорты, Страну или используемый Ресурс для вывода данных";
			return false;
		} 
		if (is_numeric($params['rid']) && (empty($params['resort']) && empty($params['country']))) { 
			// получим используемые курорты данного ресурса
			$this->curResorts = array_filter(explode(',', $this->getResorts($params['rid'])));
			// if (empty($this->curResorts)){
			// 	$this->curCountries = explode(',', $this->getCountries($params['rid']));
            // }
           
		} else {
			if (isset($params['resort']))
				$this->curResorts = explode(',',$params['resort']);
			if (isset($params['country']))
				$this->curCountries = explode(',',$params['country']);
		}
		$this->getFromCity(); // город отправления
 
		// Получайм данные кеша или таймштамп кэша, если просрочен
		$data = $this->getCache();

		// Файл кэша не пуст?
		if (!empty($data)) {
			// Актуален файл кеша?
			if (is_numeric($data)) { // timestamp если кеш просрочен, иначе объект
				$answer = $this->getMinPrices($data); // Делаем запрос
				#var_dump($answer);
				if (isset($answer->code) && $answer->code==200) { // удачный запрос - обновляем данные
					$data = $answer;
					// и записываем их в кэш
					$this->setCache($data);
				} else { // берем старые данные
					// $now = new DateTime();
					// $cacheDate = new DateTime();
					// $cacheDate->setTimestamp($data);
					// $dayDiff = $now->diff($cacheDate)->days;

					// if ($dayDiff < 2) // если 2 дня назад - не выводить
						$data = $this->getCache(true);
				}
			}
		} else {
            $answer = $this->getMinPrices(); // Делаем запрос
          
			if (isset($answer->code) && $answer->code==200) { // удачный запрос - обновляем данные
				$data = $answer;
				// и записываем их в кэш
				$this->setCache($data);
			}
		}

       
        if (is_object($data) && !empty($data)) { // Делаем выборку
            
			$cards = $resortIds = $resortImgs = Array();

			// Получим картинки используемых курортов
			foreach ($data->data as $d) {
				$resortIds[] = $d->to->id;
            }
            
			if (!empty($resortIds)) {
				// Какие картинки курортов загрузил клиент?
				$imgDB = $this->modx->db->select('id,cid,img',$this->modx->getFullTableName('cl_resorts'), "id IN (".implode(',',$resortIds).")");
				while( $row = $this->modx->db->getRow( $imgDB ) ) {
					if (isset($row['img'])) {
						$resortImgs[$row['id']] = $row['img'];
					}
				}
            }
           
			foreach ($data->data as $d) {
				if ($d->from->id == $this->fromCity
					&& in_array($d->to->id, $this->curResorts)) {
					// echo {$d->to->id}.PHP_EOL;
					if (isset($resortImgs[$d->to->id]) && !empty($resortImgs[$d->to->id])) {
						$resImg = $resortImgs[$d->to->id];
					}
					else {
						$resImg = "http://tursite.org/images/resorts-cards/292-195/{$d->to->id}.jpg";
						// echo 'resImg - '.$resImg.PHP_EOL;
						if (!$this->checkRemoteFile($resImg, 'image')) { // нет курорта - смотрим страну
							if (empty($this->curCountries)) {
								include('config.php');
								foreach($resorts as $cId => $res) {
									foreach ($res as $rId => $rName) {
										if ($d->to->id == $rId) {
											$this->curCountries = $cId;
											break;
										}
									}
									if (!empty($this->curCountries))
										break;
								}
							}
							if (is_numeric($this->curCountries)) {
								$resImg = "http://tursite.org/images/countries-cards/292-195/{$this->curCountries}.jpg";
								if (!$this->checkRemoteFile($resImg, 'image'))
									$resImg = ''; // placeholder
							} else
								$resImg = '';
                            // echo 'couImg - '.$resImg.PHP_EOL;
                            
						}
					}
					// echo $resImg.PHP_EOL;
					$c = Array(
						'rid' => $d->to->id,
						'rname' => $d->to->name,
						'img' => $resImg
                    );
                   
					foreach ($d->hotels as $h) {
						$c['hotels'][$h->stars] = $h->price;
					}
					$cards[] = $c;
				}
				$this->resortNames[] = $d->to->name;
			}

			if (!empty($this->resortNames)) {
				$this->resortNames = array_unique($this->resortNames);
				natsort($this->resortNames);
            }
            
			return $cards;
		} else {
			return 'Ошибка приема данных по ценам отеля';
		}
	}

	// Получить сохраненные текстовые значения курортов (напр. getCards)
	public function getStoredResortsName($string=false) {
		if (!empty($this->resortNames)) {
			if ($string)
				return implode(',',$this->resortNames);
			else
				return $this->resortNames;
		} else {
			return false;
		}
	}
	
	# Метод получения данных
	# Ответ:
	#	json-данные из ветки data
	public function getData($from_cities='', $to_country='', $to_cities = array())
	{
		$data = $this->getCache();
		if(is_integer($data))
		{
			$data = $this->getFullQuery($data, $from_cities, $to_country, $to_cities);
		}
		return $data->data;
	}
	
	# Получаем кэш
	# Ответ:
	#	timestamp - если кэш не актуален
	#	json-данные - если кэш ещё актуален
	private function getCache($getContent=false)
	{
		if(file_exists($this->cache_file_path))
		{
			$file_content = file_get_contents($this->cache_file_path);
			if(strlen($file_content) > 0)
			{
				$file_json = json_decode($file_content);
				if(!is_null($file_json))
				{
					if(isset($file_json->code) and !empty($file_json->code) and ($file_json->code === 200))
					{
						$current_time = time();
						if(isset($file_json->last_update) and !empty($file_json->last_update) and ($file_json->last_update > 0))
						{
							if( (self::CACHE_LIFETIME > ($current_time - $file_json->last_update) && date('H') > 6) || $getContent)
							{
								return $file_json;
							}
							return $file_json->last_update;
						}
					}
				}
			}
		}
		// return time();
		return false;
	}

	# Записываем кэш-файлов
	private function setCache($json)
	{
		$json = json_encode($json);
		$file = fopen($this->cache_file_path, "w");
		fwrite($file, $json);
		fclose($file);	
		return true;
	}

	# Записываем кэш-файлов
	public function clearCache($id=false)
	{
		if (is_numeric($id)) {
			$files[] = __DIR__ . "/cache/toursdata-$id.json";
		} elseif($id===false) {
			$files = glob(__DIR__ . '/cache/toursdata*');
		}
		if ($files) {
			foreach($files as $file){
			  if(is_file($file))
			    unlink($file);
			}
			return true;
		} else {
			return false;
		}
	}
	
	# Формируем запрос на API-сервер
	# Ответ:
	#	null - если кэш не актуален
	#	json-данные - если кэш ещё актуален
	private function getFullQuery($timestamp, $from_cities, $to_country, $to_cities = array())
	{
		$json = '{"lt":' . $timestamp . ',"q":{';
		$json .= '"fr":' . $from_cities . ',';
		$json .= '"to":[{';
		$json .= '"cid":' . $to_country;
		if(!empty($to_cities) and is_array($to_cities))
		{
			$json .= ',"ctid":[';
			$sep = '';
			foreach($to_cities as $to_cities_key => $to_cities_value)
			{
				$json .= $sep . $to_cities_value;
				$sep = ',';
			}
			$json .= ']';
		}
		$json .= '}]}}';
		$result = $this->makeCurl($json);
		file_put_contents($this->cache_file_path, $result);
		$result_json = json_decode($result);
		return $result_json->data;
	}

	// Получение справочных данных
	public function getInfoData($type=false,$data=Array()) {
		switch($type) {
			case 'token':
				// $url = 'http://api.tourhotel24.ru/sletat/gentoken/gFuY2o9N31/';
				// $data = json_decode($this->makeCurl(array(),$url));
				$url = "http://api.tourhotel24.ru/sletat/v1.1/auth/user=$this->apiLogin";
                $data = json_decode($this->makeCurl(array(),$url));
                
				return $data->data->token;
			case 'countries':
				$url = "http://api.tourhotel24.ru/sletat/v1.1/gettocountries/token=".$this->apiToken;
				break;
			case 'fromCities':
				$url = "http://api.tourhotel24.ru/sletat/v1.1/getfromcities/token=".$this->apiToken;
				break;
            case 'toCities':
             
				$countries = $this->getInfoData('countries');
				$to = Array();
				foreach ($countries as $key => $value) {
					$url = "http://api.tourhotel24.ru/sletat/v1.1/gettocities/token={$this->apiToken}&country=$key";
					$data = json_decode($this->makeCurl(array(),$url));
					if ($data->data) {
						$data = $this->parseToCityData($data);
						$to["$key-$value"] = $data;
					}
				}
				return $to;
				break;
			// case 'resort':
			// 	$url = 'http://api.tourhotel24.ru/sletat/gentoken/gFuY2o9N31/';
			// 	$data = json_decode($this->makeCurl(array(),$url));
			// 	return $data->data->token;
			default:
				return 'Тип обращения ошибочен';
		}
		if ($url) {
			$data = json_decode($this->makeCurl(array(),$url));
			$data = $this->parseToCityData($data);
			return $data;
		}
	}

	// Вспомогательная функция для вывода справочной информации
	public function parseToCityData($data) {
		if ($data->data) {
			$data = $data->data;
			if (
				isset($data[0]->id) 
				&& isset($data[0]->name)) {
				$array = Array();
				foreach ($data as $d) {
					$array[$d->id] = $d->name;
				}
				return $array;
			}
		}
		return $data;
	}

	# Курлим на API-сервер своим сформированным запросом
	private function makeCurl($query=Array(), $url=false)
	{
		if (empty($url) && $this->apiToken) {
			$url = self::API_URL . '/' . self::API_METHOD . '/' . $this->apiToken . '/' . urlencode($query);
		} else if (empty($url)) {
			return false;
		}

		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,$url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl_handle);
        curl_close($curl_handle);
        
		return $result;
	}

	/* Подключение MODX */
	private function includeModx() {
		if (!is_null($this->modx))
			return true;
		// // initialize the variables prior to grabbing the config file
		// $database_type = '';
		// $database_server = '';
		// $database_user = '';
		// $database_password = '';
		// $dbase = '';
		// $table_prefix = '';
		// $base_url = '';
		// $base_path = '';
		// // get the required includes
		// $rt = @include($_SERVER['DOCUMENT_ROOT'].'/admin/includes/config.inc.php');
		// // Be sure config.inc.php is there and that it contains some important values
		// if(!$rt || !$database_type || !$database_server || !$database_user || !$dbase) {
		//     echo "Ошибка с файлом конфигурации";
		//     exit;
		// }
		// define('MODX_API_MODE', true);
		// // initiate a new document parser
		// include_once(MODX_MANAGER_PATH.'includes/document.parser.class.inc.php');
		// $this->modx = new DocumentParser;
        // $this->modx->getSettings();
        
        # getModxInstance
        define('MODX_API_MODE', true);
        include_once("../../index.php");
        global $modx;
        $modx->db->connect();
        if (empty($modx->config)) {
            $modx->getSettings();
        }
	}

	// Город отправления из админки
	public function getFromCity()
	{

        global $modx;
        $this->includeModx();
        if (empty($this->fromCity)) {
			$this->fromCity = $modx->getConfig('client_departure');
        }
  
        return $this->fromCity;
	}

	// Используемые курорты
	public function getResorts($id=false) {
		$this->includeModx();
		// full version
		// $resortsDB = $this->modx->db->query("
		// 	SELECT c.id,
		// 	GROUP_CONCAT(tv.value SEPARATOR '|') AS tvv,
		// 	GROUP_CONCAT(tvn.name SEPARATOR '|') AS tvn 
		// 	FROM {$this->modx->db->config['table_prefix']}site_content AS c
		// 	LEFT JOIN {$this->modx->db->config['table_prefix']}site_tmplvar_contentvalues AS tv ON tv.contentid = c.id
		// 	LEFT JOIN {$this->modx->db->config['table_prefix']}site_tmplvars AS tvn ON tvn.id = tv.tmplvarid  
		// 	WHERE parent = {$this->parentRid} AND tvn.name='cl-resorts'
		// 	GROUP BY c.id 
		// ");
		if (is_numeric($id)) {
			$where = "c.id = '$id'";
		} else {
			if (!empty($this->resorts))
				return $this->resorts;
			$where = "parent = {$this->parentRid}";
		}
		$resortsDB = $this->modx->db->query("
			SELECT tv.value
			FROM {$this->modx->db->config['table_prefix']}site_content AS c
			LEFT JOIN {$this->modx->db->config['table_prefix']}site_tmplvar_contentvalues AS tv ON tv.contentid = c.id
			LEFT JOIN {$this->modx->db->config['table_prefix']}site_tmplvars AS tvn ON tvn.id = tv.tmplvarid  
			WHERE $where AND tvn.name='cl-resorts'
			GROUP BY c.id 
		");

		$resorts=Array();
		while( $row = $this->modx->db->getRow( $resortsDB ) ) {  
			$resorts[] = $row['value'];
		}
		if (empty($resorts))
			return false;
		
		$resorts = implode(',',$resorts);
		if (!is_numeric($id))
			$this->resorts = $resorts;

		return $resorts;
	}

	// Используемые страны
	// public function getCountries($id=false) {
	// 	$this->includeModx();
	// 	if (is_numeric($id)) {
	// 		$where = "c.id = '$id'";
	// 	} else {
	// 		if (!empty($this->countries))
	// 			return $this->countries;
	// 		$where = "parent = {$this->parentRid}";
	// 	}
	// 	$resortsDB = $this->modx->db->query("
	// 		SELECT tv.value
	// 		FROM {$this->modx->db->config['table_prefix']}site_content AS c
	// 		LEFT JOIN {$this->modx->db->config['table_prefix']}site_tmplvar_contentvalues AS tv ON tv.contentid = c.id
	// 		LEFT JOIN {$this->modx->db->config['table_prefix']}site_tmplvars AS tvn ON tvn.id = tv.tmplvarid  
	// 		WHERE $where AND tvn.name='cl-resorts'
	// 		GROUP BY c.id 
	// 	");

	// 	$resorts=Array();
	// 	while( $row = $this->modx->db->getRow( $resortsDB ) ) {  
	// 		$resorts[] = $row['value'];
	// 	}
	// 	if (empty($resorts))
	// 		return false;
		
	// 	$resorts = implode(',',$resorts);
	// 	if (!is_numeric($id))
	// 		$this->resorts = $resorts;

	// 	return $resorts;
	// }

	private function getMinPrices($timestamp=1) {
		$apiUrl = "http://api.tourhotel24.ru/sletat/v1.1/getminprice/token=".$this->apiToken."&last_time=$timestamp&from={$this->fromCity}";
		if (is_array($this->curResorts)) {
			$apiUrl .= "&rid=".implode(',',$this->curResorts);
		} else if (!empty($this->curResorts)) {
			$apiUrl .= "&rid=".$this->curResorts;
		}
		if (is_array($this->curCountries)) {
			$apiUrl .= "&cid=".implode(',',$this->curCountries);
		} else if (!empty($this->curCountries)) {
			$apiUrl .= "&cid=".$this->curCountries;
		}
		// echo $apiUrl;
		$ch = curl_init();  
	    curl_setopt($ch,CURLOPT_URL,$apiUrl);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	    $answer=curl_exec($ch);
	    curl_close($ch);
	   
		// var_dump($answer);
		$result = json_decode($answer);
// print_r($apiUrl);
// print_r($result);
		// if (isset($answer->code) && $answer->code==200)
		// 	file_put_contents($this->cache_file_path, $answer);
		return $result;
	}

	public function checkRemoteFile($url, $type=false)
	{
	    $ch = curl_init($url);
	    // curl_setopt($ch, CURLOPT_URL,$url);
	    // don't download content
	    curl_setopt($ch, CURLOPT_NOBODY, true);
	    curl_setopt($ch, CURLOPT_FAILONERROR, true);
	    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_exec($ch);
	    $ret = curl_getinfo($ch);
	    curl_close($ch);
	    if ($ret['http_code']=='200') {
	    	if ($type!=='false') {
	    		switch ($type) {
	    			case 'image':
	    				if ($ret['content_type'] != 'image/jpeg')
	    					return false;
	    				break;
	    		}
	    	}
	    	return true;
	    } else {
	    	return false;
	    }
	}
}