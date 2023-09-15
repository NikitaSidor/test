<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
global $modx;
require_once 'Country.class.php';

class hotelInstaller
{
    var $domain = '';
    var $apiKey = '';
    var $apiSecret = '';
    var $log = '';
    var $params;
    var $upload_parent_id = false;
    var $upload_type;
    var $result = [];
    var $warning = [];
    var $hotelsMenu = [];
    var $modx = null;
    var $fileRegister = array();

    function __construct($modx, $params)
    {
        global $modx;
        if (!$modx)
            die('no $modx');
        $this->modx = $modx;
        $this->params = $params;
        if (!file_exists(MODX_BASE_PATH . 'assets/tvs/7vHotelId/7vHotelId.customtv.php'))
            die('У вас не установлен TV 7vHotelId');
        if (empty($this->params['domain']) || empty($this->params['apiKey']) || empty($this->params['apiSecret']))
            die('Настройки аккаунта отсутвтуют. Проверьте домен и ключи в: Модули, Управление модулями, Инсталлятор отелей, Конфигурация.');
    }

    function doAction($action)
    {
        if (method_exists('hotelInstaller', $action))
            $this->$action();
        else if (method_exists('hotelInstaller', 'action_' . $action)) {
            $action = 'action_' . $action;
            $this->$action();
        }
        exit;
    }

    function showPage($page = false)
    {
        global $modx;
        include_once(MODX_MANAGER_PATH . "/includes/header.inc.php");
        switch ($page) {
            default:
                require 'includes/tmplvars.inc.php';
                echo $this->parseTemplate('be_home', array());
        }
        include_once(MODX_MANAGER_PATH . "/includes/footer.inc.php");
        exit;
    }

    function parseTemplate($tpl, $values = null)
    {
        global $modx;

        if (is_null($values))
            $values = $this->ph;

        $tpl = rtrim($tpl, '.tpl') . '.tpl';
        $tpl = array_key_exists($tpl, $this->fileRegister) ? $this->fileRegister[$tpl] : $this->getFileContents($tpl);
        if ($tpl) {
            if (!isset($this->modx->config['mgr_jquery_path']))  $this->modx->config['mgr_jquery_path'] = 'media/script/jquery/jquery.min.js';
            $tpl = $this->modx->mergeSettingsContent($tpl);
            if (!empty($values)) {
                foreach ($values as $key => $value) {
                    $tpl = str_replace('[+' . $key . '+]', $value, $tpl);
                }
            }
            $tpl = preg_replace('/(\[\+.*?\+\])/', '', $tpl);
            return $tpl;
        } else {
            return '';
        }
    }

    function getFileContents($file)
    {
        if (empty($file)) {
            return false;
        } else {
            $file = realpath(__DIR__) . '/templates/' . $file;
            if (array_key_exists($file, $this->fileRegister)) {
                return $this->fileRegister[$file];
            } else {
                $contents = file_get_contents($file);
                $this->fileRegister[$file] = $contents;
                return $contents;
            }
        }
    }

    function answer($msg = 'success', $success = false, $params = array())
    {
        header('Content-Type: application/json');
        $answer = array(
            'msg' => $msg,
            'status' => $success ? 'success' : 'error'
        );
        if (!empty($params) && is_array($params))
            $answer = array_merge($answer, $params);
        die(json_encode($answer));
    }

    function returnAnswer($msg = 'success', $status = false, $params = array())
    {
        if ($status === true)
            $status = 'success';
        else if ($status === false)
            $status = 'error';
        $answer = array(
            'msg' => $msg,
            'status' => $status
        );
        if (!empty($params) && is_array($params))
            $answer = array_merge($answer, $params);
        return $answer;
    }

    function returnJson($data = '')
    {
        echo json_encode($data);
        die();
    }

    // Основной метод-контроллер загрузки отелей
    function action_addHotels()
    {
        // var_dump($_POST);
        // exit;
        $urls = explode(PHP_EOL, $_POST['urls']);
        if (!is_array($urls) || empty($urls)) {
            $this->answer("Не предоставлено URL для обработки");
        }

        // $fLog = MODX_BASE_PATH . '/assets/modules/hotelInstaller/hotelInstaller.log';
        // $this->log = '';
        // $ltime = time();
        // $this->log .= "Получение лимитов" . "\n" . (time() - $ltime) . "\n";

        $limit = $this->getLimit();
        $this->validateLimit($limit);
        $limit_data_used = $limit->data->used;

        if ($_POST['start_operation'] == 1) {
            $_SESSION['processedHotels'] = [];
            $_SESSION['processedErrorHotels'] = [];
            $_SESSION['savedHotelUrls'] = [];
            $_SESSION['validatedHotelUrls'] = [];
            $_SESSION['validatedHotelUrlsCleaned'] = [];
        }

        $answer = $this->getDataByHotelUrls($urls);
        // $this->log .= "Получен ответ сервера о отелях (" . count($) . " штук) " . "\n" . (time() - $ltime) . "\n";
        // var_dump($answer);
        // exit;
        $this->validateAnswerStructure($answer);

        // $this->log .= "НАЧАЛО ОБРАБОТКИ ОТЕЛЕЙ " . "\n" . (time() - $ltime) . "\n";
        // if (count($_SESSION['processedHotels'])) {
        //     $this->log .= "Уже обработанные отели " . "\n" . (time() - $ltime) . "\n";
        // }
        $startTime = time();
        $done = 0;
        $processing = [];
        $result = [
            'status' => 1,
            'message' => 'Success',
            'data' => [
                'total' => count($answer->data)
            ]
        ];



        $this->upload_parent_id = (isset($_POST['parent_id']) && is_numeric($_POST['parent_id'])) ? $_POST['parent_id'] : false;
        $this->upload_type = $_POST['upload_type'];

        foreach ($answer->data as $data) {
            if ($limit_data_used == $limit->data->count) {
                $result['msg'] = $processing['msg'] = 'Вы достигли лимита загрузки отелей: ' . $limit->data->count . '. Обратитесь к администрации компании "7 ветров"';
                $processing['status'] = 'error';
                $result['status'] = 'limit';
                // $this->log .= "ЛИМИТ ДОСТИГНУТ " . "\n" . (time() - $ltime) . "\n";
                break;
            }

            if ($data->message === 'Success') {
                $done++;
                $pendingTime = $_POST['pending_time'] * 1;
                $processing = [];

                if (isset($_SESSION['processedHotels'][$data->data->id])) {
                    $processing = $_SESSION['processedHotels'][$data->data->id];
                    // $this->log .= "Отель уже обработан: {$data->data->url}" . "\n" . (time() - $ltime) . "\n";
                } elseif ((time() - $startTime) < $pendingTime) {
                    // $this->modx->db->disconnect();
                    // $this->modx->db->connect();
                    $processing = $this->saveHotel($data);
                    if (isset($processing['hotelId']) && $processing['hotelId']) {
                        // $this->log .= "Отель сохранен: {$data->data->url}" . "\n" . (time() - $ltime) . "\n";
                        $limit_data_used++;
                        $_SESSION['savedHotelUrls'][] = $data->data->url;
                        $_SESSION['processedHotels'][$data->data->id] = $processing;
                        $processing['msg'] = '<div>Отель сохранен! Вы использовали ' . ($limit->data->used * 1 + 1) . ' из ' . $limit->data->count . ' доступных Вам для загрузки отелей</div><div>Отель добавлен под ID ' . $processing['hotelId'] . '<div class="controls"><a href="' . $url . '" target="_blank">Посмотреть</a>, <a href="#" onclick="parent.main.location.href=\'index.php?a=27&id=' . $processing['hotelId'] . '\'">Редактировать</a>, <a href="index.php?a=27&id=' . $processing['hotelId'] . '" target="_blank">Редактировать в новой вкладке</a></div></div>';
                        if (!empty($processing['warning'])) {
                            $processing['status'] = 'warning';
                            foreach ($processing['warning'] as $wrn) {
                                $processing['msg'] .= "<div>$wrn</div>";
                            }
                        }
                    } else {
                        $_SESSION['processedErrorHotels'][$data->data->id] = $processing;
                    }
                } else {
                    $processing = [
                        'msg' => 'В очереди на сохранение',
                        'status' => 'primary',
                    ];
                    // $this->log .= "Отель пропущен: {$data->data->url}" . "\n" . (time() - $ltime) . "\n";
                }

                $result['data']['links'][] = [
                    'id' => $data->data->id,
                    'url' => $data->data->url,
                    'name' => $data->data->name,
                    'status' => 3,
                    'processing' => $processing
                ];
            } elseif (
                $data->message === 'Url added to waiting list'
                || $data->message === 'Url exists in waiting list'
            ) {
                $result['status'] = 'waiting';
                $result['data']['links'][] = [
                    'url' => $data->data->url,
                    'name' => "Нет данных об отеле на сервере",
                    'status' => 2,
                    'processing' => [
                        'msg' => 'В очереди на обработку данных. Обработка занимает 2-3 минуты',
                        'status' => 'warning',
                    ]
                ];
                // $this->log .= "Отель обрабатывается на сервере: {$data->data->url}" . "\n" . (time() - $ltime) . "\n";
            } else {
                $result['status'] = 'waiting';
                $result['data']['links'][] = [
                    'url' => $data->data->url,
                    'name' => "",
                    'status' => 2,
                    'processing' => [
                        'msg' => $data->message,
                        'status' => 'warning',
                    ]
                ];
                // $this->log .= "Отель обработка: {$data->data->url}" . "\n" . (time() - $ltime) . "\n";
            }
        }

        $result['data']['done'] = $done;
        $result['data']['progress'] = round(($done / $result['data']['total']) * 100, 0);
        $result['processed']['done'] = count($_SESSION['processedHotels']);
        $result['processed']['progress'] = round(($result['processed']['done'] / $result['data']['total']) * 100, 0);
        $result['processedError']['done'] = count($_SESSION['processedErrorHotels']);
        $result['processedError']['progress'] = round(($result['processedError']['done'] / $result['data']['total']) * 100, 0);
        if ($result['processed']['done'] == $result['data']['total']) {
            unset($_SESSION['processedHotels']);
            unset($_SESSION['processedErrorHotels']);
        }

        // $this->log .= "ОТЕЛИ ОБРАБОТАНЫ" . "\n" . (time() - $ltime) . "\n";
        // $this->log .= "======" . "\n" . (time() - $ltime) . "\n";
        // if ($_POST['start_operation'] != 1) {
        //     file_put_contents($fLog, $this->log, FILE_APPEND);
        // } else {
        //     file_put_contents($fLog, $this->log);
        // }

        $this->returnJson($result);
    }

    function validateLimit($limit)
    {
        if ($limit->status != 1)
            $this->answer("Не получены данные о количестве загруженных Вами отелях");
        if ($limit->data->used >= $limit->data->count)
            $this->answer('Вы использовали свой лимит загрузки на ' . $limit->data->used . ' отелей');
    }
    function validateAnswerStructure($answer)
    {
        if (!is_object($answer))
            $this->answer('Не получен объект ответа с сервера');
        if (
            !property_exists($answer, 'status')
            or ((int) $answer->status !== 1)
        )
            $this->answer('Ошибка или необрабатываемый сценарий ответа сервера');
        if (!property_exists($answer, 'data') || !is_array($answer->data) || empty($answer->data))
            $this->answer('Данные отсуствуют');
    }

    function getLimit($show = true)
    {
        $requestParams = array();
        $sign = hash_hmac('sha512', http_build_query($requestParams, '', '&'), $this->params['apiSecret']);
        $requestHeader = array(
            'Key: ' . $this->params['apiKey'],
            'Sign: ' . $sign,
        );
        return $this->query('http://api.7vetrov.com/booking/getLimit/', $requestParams, $requestHeader, false);
    }

    function getDataByHotelUrls($urls)
    {
        $urlsToGet = [];
        $urlsCount = count($urls);
        foreach ($urls as $index => $url) {
            $url = trim($url);
            $uKey = array_search($url, $_SESSION['validatedHotelUrls']);

            if ($uKey === false) {
                $_SESSION['validatedHotelUrls'][] = $url;
                $url = $this->validateHotelUrl($url);
                $_SESSION['validatedHotelUrlsCleaned'][] = $url;
            } else {
                $url = $_SESSION['validatedHotelUrlsCleaned'][$uKey];
            }

            if ($url) {
                $urls[$index] = $url;
                if (!in_array($url, $_SESSION['savedHotelUrls'])) {
                    $urlsToGet[] = $url;
                }
            } else if (empty($url) && ($index + 1) == $urlsCount) {
                unset($urls[$index]);
                continue;
            } else if (!empty(trim($urls[$index]))) {
                $this->answer("Обработка остановлена. На строке " . ($index + 1) . " неверный URL: {$urls[$index]}");
            }
        }
        // Параметры запроса к серверу
        $requestParams = array(
            'urls' => json_encode($urlsToGet), # URLs страниц, которые нужно забрать с booking.com
        );
        // Подпись запроса
        $sign = hash_hmac('sha512', http_build_query($requestParams, '', '&'), $this->params['apiSecret']);
        // Заголовки запроса
        $requestHeader = array(
            'Key: ' . $this->params['apiKey'],
            'Sign: ' . $sign,
        );
        return $this->query('http://api.7vetrov.com/booking/getHotelsList/', $requestParams, $requestHeader, false);
    }

    function saveHotel($answer)
    {
        $isHotelUploaded = $this->isHotelUploaded($answer);
        if ($isHotelUploaded !== false)
            return $isHotelUploaded;

        $parent = $this->checkParent($answer);

        if (is_array($parent))
            return $parent;

        $answer = $this->cleanAnswer($answer);
        $hotelId = $this->saveHotelResource(
            $answer,
            $parent
        );
        $this->saveHotelTVs(
            $answer,
            $hotelId
        );

        $warning = $this->warning;
        $this->warning = [];
        return array(
            "status" => 'success',
            'hotelId' => $hotelId,
            "warning" => $warning,
        );
    }

    /*
    ХЭЛПЕРЫ СОХРАНЕНИЯ
    */
    function isHotelUploaded($answer)
    {
        $result = $this->modx->db->query("
            SELECT sc.id
            FROM " . $this->modx->getFullTableName('site_content') . " AS sc
            LEFT JOIN " . $this->modx->getFullTableName('site_tmplvars') . " as tv ON tv.name = '7vHotelId'
            LEFT JOIN " . $this->modx->getFullTableName('site_tmplvar_contentvalues') . " as tvcv ON tvcv.tmplvarid = tv.id AND tvcv.contentid = sc.id
            WHERE
            tvcv.value = '" . $answer->data->id . "'
        ");
        if ($this->modx->db->getRecordCount($result)) {
            $row = $this->modx->db->getRow($result);
            $url = $this->modx->makeUrl($row['id'], '', '', 'full');
            $parents = [];
            if (is_numeric($this->upload_parent_id))
                $parents["parentHotelsId"] = $this->findParentsId($this->upload_parent_id);
            else
                $parents = $this->findParentsId([
                    'country' => $answer->data->country,
                    'city' => $answer->data->city,
                ]);

            return $this->returnAnswer(
                'Отель уже загружен в систему. ID ресурса: '
                    . $row['id']
                    . '. <div class="controls"><a href="' . $url . '" target="_blank">Посмотреть</a>, <a href="#" onclick="parent.main.location.href=\'index.php?a=27&id=' . $row['id'] . '\'">Редактировать</a>, <a href="index.php?a=27&id=' . $row['id'] . '" target="_blank">Редактировать в новой вкладке</a></div>',
                'info',
                array(
                    'hotelsDocId' => $parents["parentHotelsId"],
                    'hotelDocId' => $row['id'],
                )
            );
        }
        return false;
    }


    function checkParent($answer)
    {
        $method = 'checkParent_' . $this->upload_type;
        if (method_exists($this, $method))
            return $this->$method($answer, $parents);

        return $this->returnAnswer('Метод проверки родительской структуры не найден ' . $method);
    }
    function checkParent_save_to_hotels_country_city($answer)
    {
        global $modx;

        $parents['countryId'] = $this->getCountryId($answer->data->country);
        if ($parents['countryId']) {
            // TODO: строим меню, ищем город
            $parents['cityId'] = $this->getCityIdInMenu($this->buildMenu($parents['countryId']));
            // } else if (!empty($_POST['create_parent_structure'])) {
            // return $this->createParent(tructure($answer, $parents);
        }

        // ЭТО РАБОТАЕТ!!! 14,11,22
        // if (empty($parents) && $_POST['create_parent_structure']) {
        //     $parents = $this->createParentStructure($answer);
        // }

        if (empty($parents)) {
            return array(
                "status" => 'error',
                "msg" => "Отсутствуют папки страны \"{$answer->data->country}\" и города \"{$answer->data->city}\" для загрузки",
            );
        } else if (empty($parents['cityId'])) {
            return array(
                "status" => 'error',
                "msg" => "Отсутствуют папка города \"{$answer->data->city}\" для загрузки",
            );
        }

        return $parents;

        /* OLD CODE */
        // $parents = $this->findParentsId([
        //     'country' => $answer->data->country,
        //     'city' => $answer->data->city,
        // ]);
        // var_dump($parents);
        // var_dump($_POST['create_parent_structure']);
        // exit;
        // if (
        //     $parents === false
        //     || (isset($parents["parentHotelsId"]) && empty($parents["parentHotelsId"]))
        // ) {
        //     if (isset($_POST['create_parent_structure']) && $_POST['create_parent_structure'] == '1') {
        //         if ($this->isLatin($answer->data->city)) {
        //             $this->warning[] = 'Название города пришло в виде латиницы. Отель будет загружен в папку "Отели"';
        //             $parents['hotelParentId'] = $this->params['hotelsParentId'];
        //             return $parents;
        //         }
        //         return $this->createParent(tructure($answer, $parents);
        //     }

        //     return $this->answerParentStructure($answer);
        // } else {
        //     return $parents;
        // }
    }

    function getCountryId($country)
    {
        global $modx;
        $countryId = false;
        $result = $modx->db->query("SELECT id 
            FROM {$this->modx->getFullTableName('site_content')}
            WHERE pagetitle='{$country}'
            AND parent = {$this->params['hotelsParentId']}
            AND template = {$this->params['countryTpl']}
        ");
        $countryId = $this->modx->db->getRow($result);
        return $countryId['id'] ? $countryId['id'] : false;
    }

    function getCityIdInMenu($menu)
    {
        // $menu
    }

    function cleanAnswer($answer)
    {
        // Убираем из текста &quot; и т.п.
        foreach ($answer->data as &$ad) {
            if (is_string($ad))
                $ad = html_entity_decode($ad);
        }
        return $answer;
    }

    function saveHotelResource($answer, $parentId)
    {
        // introtext
        if (!empty($answer->data->description)) {
            preg_match_all('!<p>(.+?)</p>!sim', $answer->data->description, $matches, PREG_PATTERN_ORDER);
            $matches = $matches[0];
            if (empty($matches)) {
                $pos = strpos($answer->data->description, ' ', 400);
                if ($pos) {
                    $introtext = substr($answer->data->description, 0, $pos);
                    if (substr($introtext, -1) != ".")
                        $introtext .= '...';
                } else
                    $introtext = $answer->data->description;
            } else {
                if (strlen($answer->data->description) > 400) {
                    $introtext = $matches[0];
                } else {
                    $introtext = $matches[0] . ' ' . $matches[1];
                }
            }
            $introtext = str_replace('  ', ' ', strip_tags($introtext));
        } else {
            $introtext = '';
        }

        $hotelName = $this->modx->db->escape($answer->data->name);
        $hotelId = $this->modx->db->insert(
            array(
                'pagetitle' => $hotelName,
                'alias' => $this->formAlias($answer->data->name),
                'published' => 1,
                'parent' => $parentId,
                'introtext' => $introtext,
                'content' => $this->modx->db->escape($answer->data->description),
                'template' => (int) $this->params['hotelTpl'],
                'createdon' => time(),
                'editedon' => time(),
                'publishedon' => time(),
            ),
            $this->modx->getFullTableName('site_content')
        );
        return $hotelId;
    }

    function saveHotelTVs($answer, $hotelId)
    {
        // Устанавливаем Tv со значением ID отеля в системе парсинга 
        if (!isset($answer->data->id) || empty($answer->data->id))
            $this->answer('Не получен идентификатор отеля в системе 7 ветров');
        $this->insertTvValueByName('7vHotelId', $hotelId, $answer->data->id);

        $this->insertTvValueByName('title', $hotelId, $this->modx->db->escape("Отель {$answer->data->name} в городе {$answer->data->city}, {$answer->data->country}"));

        $mData = "Отель, ";
        if ($answer->data->stars > 0) {
            switch ($answer->data->stars) {
                case '1':
                    $mData .= "1 звезда, ";
                    break;
                case '5':
                    $mData .= "5 звезд, ";
                    break;
                default:
                    $mData .= "{$answer->data->stars} звезды, ";
            }
        }
        $mData .= "{$answer->data->name}, {$answer->data->city}, {$answer->data->country}";
        $this->insertTvValueByName('keyw', $hotelId, $this->modx->db->escape($mData));

        $desc = strip_tags($answer->data->description);
        if (mb_strlen($desc) > 280) {
            $pos = strpos($desc, '. ', 280);
            if ($pos != false)
                $mData = substr($desc, 0, $pos + 1);
            else {
                $pos = strpos($desc, ' ', 280);
                $mData = substr($desc, 0, $pos);
            }
        } else {
            $mData = $desc;
        }
        $this->insertTvValueByName('desc', $hotelId, $this->modx->db->escape($mData));

        // Флаг страны
        if (isset($answer->data->country) && !empty($answer->data->country)) {
            $this->upload_parent_id = Country::getISO2($answer->data->country);
            if ($this->upload_parent_id)
                $this->insertTvValueByName('countryID', $hotelId, $this->upload_parent_id);
        }

        if (isset($answer->data->city) && !empty($answer->data->city))
            $this->insertTvValueByName('dest-resort', $hotelId, $this->modx->db->escape($answer->data->city));

        if (isset($answer->data->stars) && !empty($answer->data->stars))
            $this->insertTvValueByName('hotelStars', $hotelId, $answer->data->stars);
        else
            $this->insertTvValueByName('hotelStars', $hotelId, 0);

        if (isset($answer->data->rates[0]->value) && $answer->data->rates[0]->value != '')
            $this->insertTvValueByName('rating', $hotelId, $answer->data->rates[0]->value);

        if (isset($answer->data->reviews_total) && !empty($answer->data->reviews_total))
            $this->insertTvValueByName('hotelReviews', $hotelId, $answer->data->reviews_total);

        if (isset($answer->data->address) && !empty($answer->data->address))
            $this->insertTvValueByName('address', $hotelId, $this->modx->db->escape($answer->data->address));

        if (isset($answer->data->policies[0]->value) && !empty($answer->data->policies[0]->value))
            $this->insertTvValueByName('checkInTime', $hotelId, $answer->data->policies[0]->value);

        if (isset($answer->data->policies[1]->value) && !empty($answer->data->policies[1]->value))
            $this->insertTvValueByName('checkOutTime', $hotelId, $answer->data->policies[1]->value);

        if (isset($answer->data->policies[3]->value) && !empty($answer->data->policies[3]->value))
            $this->insertTvValueByName('checkOutNotes', $hotelId, $this->modx->db->escape($answer->data->policies[3]->value));

        if (isset($answer->data->policies[4]->value) && !empty($answer->data->policies[4]->value)) {
            if ($answer->data->policies[4]->value == 'Размещение домашних животных не допускается.')
                $value_checkInPets = 0;
            elseif (strpos($answer->data->policies[4]->value, 'Размещение домашних животных допускается') !== false)
                $value_checkInPets = 1;
            else
                $value_checkInPets = false;

            if ($value_checkInPets !== false)
                $this->insertTvValueByName('checkInPets', $hotelId, $value_checkInPets);
        }

        if (isset($answer->data->important_facilities) && !empty($answer->data->important_facilities)) {
            $value_important_facilities = $this->prepareImportantFacilities($answer->data->important_facilities);
            if (!empty($value_important_facilities))
                $this->insertTvValueByName('hotelFacilities', $hotelId, $this->modx->db->escape($value_important_facilities));
        }

        if (isset($answer->data->lat) && !empty($answer->data->lat) && isset($answer->data->lon) && !empty($answer->data->lon))
            $this->insertTvValueByName('coords', $hotelId, $answer->data->lat . ', ' . $answer->data->lon);

        if (isset($answer->data->photos) && !empty($answer->data->photos)) {
            $pathImg = MODX_BASE_PATH . 'assets/galleries/' . $hotelId . '/';
            if (!file_exists($pathImg))
                mkdir($pathImg, 0777, true);

            require_once(MODX_BASE_PATH . 'assets/plugins/simplegallery/lib/table.class.php');
            $sgData = new \SimpleGallery\sgData($this->modx);
            if (isset($this->params['imagesLimit']) && is_numeric($this->params['imagesLimit']) && $this->params['imagesLimit'] > 0)
                $imgLimit = $this->params['imagesLimit'];
            else
                $imgLimit = count($answer->data->photos);

            foreach ($answer->data->photos as $p) {
                $imgContent = file_get_contents($p->url);
                if ($imgContent !== false) {
                    $pUrl = parse_url($p->url);
                    $filepath = $pathImg . basename($pUrl['path']);
                    file_put_contents($filepath, $imgContent);
                    $sgData->upload($filepath, $hotelId, $this->modx->db->escape($answer->data->name));
                    $imgLimit--;
                    if ($imgLimit == 0)
                        break;
                }
            }
        }
    }

    function insertTvValueByName($tvname, $contentid, $value)
    {
        $value = $this->modx->db->escape($value);
        $res = $this->modx->db->query(
            "INSERT IGNORE INTO {$this->modx->getFullTableName('site_tmplvar_contentvalues')}
            (tmplvarid, contentid, value)
            VALUES (
            (SELECT id FROM evo_site_tmplvars WHERE name = '$tvname'),
            $contentid,
            '$value'
            )
        "
        );

        if ($this->modx->db->getRecordCount($res)) {
            return $this->modx->db->getRow($res);
        } else return false;
    }

    function validateHotelUrl($url, $checkHeaders = true)
    {
        $result = false;
        // $url = preg_replace('/^(.+(?=\/[\w-]+?[\.|\?]*))(\/[\w-]+)\.[\w-]+(\.[\w]+)(.*)/', '$1$2.ru$3', $url);
        $url = preg_replace('/^(.+(?=\/[\w-]+?[\.|\?]*))(\/[^\.]+).*/', '$1$2.ru.html', $url);
        $url = explode('?', $url);
        $url = $url[0];
        $url = preg_replace('/^((?:https|http):\/\/[^\?]+)([\w]{2,3})(\.html)$/i', '${1}ru${3}', $url);

        preg_match('/^((?:https|http):\/\/[^\?]+).*$/i', $url, $matches);
        if (!empty($matches)) {
            array_shift($matches);
            $url = $matches[0];

            if ($checkHeaders) {
                // PHP 7+, ssl problem
                // $context = stream_context_create([
                //     'ssl' => [
                //         'verify_peer' => false,
                //         'verify_peer_name' => false,
                //     ],
                // ]);
                // $head = get_headers($url, 0);
                $head = get_headers($url, 0);
                preg_match('/^(?:https|http)\/[\d\.]+\s+([\d]+)\s(.+)$/i', $head[0], $matches);
                if ($matches) {
                    array_shift($matches);
                    if ((int) $matches[0] === 200) {
                        $result = $url;
                    }
                }
            } else
                $result = $url;
        }

        return $result;
    }

    function formAlias($title)
    {
        $title = trim($title);
        $title = str_replace('–', '-', $title); // ndash
        $title = str_replace('—', '-', $title); // mdash
        $title = str_replace('_', '-', $title);
        $alias = strtolower($this->modx->stripAlias($title));
        return $alias;
    }

    // unused
    function buildMenu($parents = false, $force = false)
    {
        global $modx;
        $parents = $parents ? $parents : $this->params['hotelsParentId'];
        if (!$force && !empty($this->menu[$parents]))
            return $this->menu[$parents];
        $menu = $modx->runSnippet('DLMenu', [
            'parents' => $parents,
            'addWhereList' => "c.template != {$this->params['hotelTpl']}",
            'api' => 1
        ]);
        if ($menu == '[]')
            $menu = [];
        else {
            $menu = json_decode($menu);
            $menu = $menu[0];
        }
        $this->menu[$parents] = $menu;
        return $menu;
    }

    function getCityParents()
    {
    }

    /*
    function findParentsId($geo)
    {
        $method = 'findParentsId_' . $this->upload_type;
        if (method_exists($this, $method))
            return $this->$method($geo);

        return $this->returnAnswer('Метод загрузки не найден ' . $method);
    }

    // Сохраняем в корневую папку Отели / Город
    function findParentsId_save_to_hotels_city($geo)
    {
        $where = [];
        if ($this->upload_parent_id) {
            $where[] = "id = {$this->upload_parent_id}";
        } else {
            $where[] = "template = {$this->params['cityTpl']}";
            $where[] = "pagetitle = '{$geo['city']}'";
        }

        $res = $this->modx->db->query("
            SELECT
            id as hotelParentId, isfolder
            FROM " . $this->modx->getFullTableName('site_content') . "
            WHERE " . implode(' AND ', $where));

        if ($this->modx->db->getRecordCount($res)) {
            $row = $this->modx->db->getRow($res);
            if (!empty($row['hotelParentId']) && $row['isfolder'] == 0)
                $this->modx->db->update(
                    array('isfolder' => 1),
                    $this->modx->getFullTableName('site_content'),
                    "id = {$row['hotelParentId']}"
                );
            return $row;
        }

        return false;
    }
    */

    function createParentStructure($answer, $parents = [])
    {
        $method = 'createParentStructure_' . $this->upload_type;
        if (method_exists($this, $method))
            return $this->$method($answer, $parents);

        return $this->returnAnswer('Метод создания родительской структуры не найден ' . $method);
    }

    // Отели / Город
    /*
    function createParentStructure_save_to_hotels_city($answer, $parents)
    {
        // Новый город
        $parents = [];
        $parentId = $this->modx->db->insert(
            array(
                'pagetitle' => $this->modx->db->escape($answer->data->city),
                'alias' => $this->formAlias($answer->data->city),
                'published' => 1,
                'isfolder' => 1,
                'parent' => $this->params['hotelsParentId'],
                'content' => '',
                'template' => (int) $this->params['cityTpl'],
                'createdon' => time(),
                'editedon' => time(),
                'publishedon' => time(),
            ),
            $this->modx->getFullTableName('site_content')
        );
        if (is_numeric($parentId))
            $warning[] = "Создана папка: {$answer->data->city} <small>($parentId)</small>. Заполните ее информацией.";
        else {
            $warning[] = "Не удалось создать город: {$answer->data->city}. Файл будет загружен в папку \"Отели\"";
            $parentId = $this->params['hotelsParentId'];
        }
        $parents['hotelParentId'] = $parentId;
        return $parents;
    }
    */
    function createParentStructure_save_to_hotels_country_city($answer, $parents)
    {
        if (empty($parents['countryId'])) {
            $parents['countryId'] = $this->createResource([
                'pagetitle' => $this->modx->db->escape($answer->data->country),
                'alias' => $this->formAlias($answer->data->country),
                'parent' => $this->params['hotelsParentId'],
                'template' => (int) $this->params['countryTpl'],
            ]);
        }

        if (empty($parents['cityId'])) {
            $parent = $parents['countryId'];
            if (!$parent) {
                $parent = $this->params['hotelsParentId'];
                $this->warning[] = "Отель будет загружен в папку загрузки {$this->params['hotelsParentId']}</small>";
            }

            $parents['cityId'] = $this->createResource([
                'pagetitle' => $this->modx->db->escape($answer->data->city),
                'alias' => $this->formAlias($answer->data->city),
                'parent' => $parent,
                'template' => (int) $this->params['cityTpl'],
            ]);
        }
        return $parents;
    }

    function createResource($data)
    {
        $id = $this->modx->db->insert(
            array_merge(
                $data,
                [
                    'published' => 1,
                    'isfolder' => 1,
                    'content' => '',
                    'createdon' => time(),
                    'editedon' => time(),
                    'publishedon' => time(),
                ]
            ),
            $this->modx->getFullTableName('site_content')
        );
        if (is_numeric($id))
            $warning[] = "Создан ресурс: {$data['pagetitle']} <small>($id)</small>";
        else {
            $warning[] = "Не удалось создать ресурс: {$data['pagetitle']}";
        }
        return $id;
    }

    function answerParentStructure($answer)
    {
        switch ($this->upload_type) {
            case 'save_to_hotels_city':
                return $this->returnAnswer('Папка-родитель с нужным шаблоном для загрузки отеля не найдена: ' . $answer->data->city, false);
            default:
                return $this->returnAnswer('Папка-родитель с нужным шаблоном для загрузки отеля не найдена', false);
        }
        // if (is_numeric($this->upload_parent_id) && $_POST['save_to_geocities'] != 1)
        //     return $this->returnAnswer('Отсутствует папка "Страны" / Документ ID "' . $this->upload_parent_id . '" / "Отели"', false);
        // else if (isset($_POST['save_to_city_name']) && $_POST['save_to_city_name'] == 1)
        //     return $this->returnAnswer('Отсутствует папка города или неверно указан шаблон: ' . $answer->data->city, false);
        // else if ($answer->data->country)
        //     return $this->returnAnswer('Отсутствует папка "Страны" / "' . $answer->data->country . '" / "Отели"', false);
        // else
        //     return $this->returnAnswer('Папка-родитель для загрузки отеля не найдена', false);
    }

    /*
    МЕТОДЫ СОХРАНЕНИЯ ОТЕЛЕЙ
    */

    function isLatin($string)
    {
        if (preg_match('/^[a-zA-Z0-9-\s]+$/i', $string))
            return 'latin';
        return false;
    }

    function findCityId($countryHotelsId, $city)
    {
        // Проверяем на латиницу
        if (preg_match('/^[a-zA-Z0-9-\s]+$/i', $city)) {
            return 'latin';
        }

        $res = $this->modx->db->query("
            SELECT c.id
            FROM " . $this->modx->getFullTableName('site_content') . " as c
            WHERE c.parent = $countryHotelsId
            AND c.pagetitle = '$city'
        ");
        $row = $this->modx->db->getRow($res);

        if (isset($row['id']) && is_numeric($row['id']))
            return $row['id'];
        else
            return false;
    }

    function createCountryHotelPage($country = false)
    {
        // Проверить есть ли страница данной страны
        // Проверить есть ли страница отеля данной страны
        // Создать страницу Отели и вернуть ее ИД
        return 0;
    }

    function prepareImportantFacilities($fs)
    {
        if (empty($fs))
            return false;
        // Получим список текущих значений в системе
        $res = $this->modx->db->query(
            "SELECT elements FROM {$this->modx->getFullTableName('site_tmplvars')}
            WHERE name = 'hotelFacilities'"
        );
        $curTvHF = $this->modx->db->getValue($res);
        $curTvHFa = explode('||', $curTvHF);
        $valsHF = array();
        foreach ($curTvHFa as $d) {
            $d = explode('==', $d);
            $valsHF[$d[0]] = $d[1];
        }

        $v = '';
        $resetTvValue = false;
        foreach ($fs as $f) {
            // Значения для поля отеля
            if (!empty($v))
                $v .= '||';
            // $v.=$f->key;
            $v .= $this->formAlias($f->value);
            // Общие значения
            if (!isset($valsHF[$f->value]))
                $resetTvValue = true;
        }
        if ($resetTvValue) {
            $fl = $this->getFacilitiesList();
            if ($fl->status == 1) {
                $vals = array();
                foreach ($fl->data as $d)
                    $vals[] = html_entity_decode($d->value) . '==' . $this->formAlias($d->value);
                if (!empty($vals))
                    $this->modx->db->update(
                        array('elements' => implode('||', $vals)),
                        $this->modx->getFullTableName('site_tmplvars'),
                        "name = 'tv_hotelFacilities'"
                    );
            }
        }
        return $v;
    }

    function getFacilitiesList()
    {
        $requestParams = array();
        // Подпись запроса
        $sign = hash_hmac('sha512', http_build_query($requestParams, '', '&'), $this->params['apiSecret']);
        // Заголовки запроса
        $requestHeader = array(
            'Key: ' . $this->params['apiKey'],
            'Sign: ' . $sign,
        );
        return $this->query('http://api.7vetrov.com/booking/getFacilitiesList/', $requestParams, $requestHeader, false);
    }



    /**
     * Метод запроса с помощью CURL
     *
     * @param string $query - ссылка запроса
     * @param array $params - параметры запроса
     * @param array $headers - заголовки запроса
     * @param boolean $asJson - вывод результата как JSON-данные, либо как PHP-объект
     * @return void
     */
    function query($query, $params = array(), $headers = array(), $asJson = true)
    {
        $content = '';
        $queryParams = '';
        if (is_array($params) and !empty($params)) {
            $preParams = array();
            foreach ($params as $paramsItemKey => $paramsItemValue) {
                $preParams[] = $paramsItemKey . '=' . $paramsItemValue;
            }
            $queryParams = implode('&', $preParams);
        }
        $queryLink = (string) $query;
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $queryLink);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; Kreexus PHP client)');
            curl_setopt($curl, CURLOPT_VERBOSE, true);
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            if (is_array($headers) and !empty($headers)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            }
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $queryParams);

            curl_setopt($curl, CURLINFO_HEADER_OUT, true);

            $content = substr(curl_exec($curl), curl_getinfo($curl, CURLINFO_HEADER_SIZE));
            if (!$asJson) {
                $content = json_decode($content);
            }

            // show headers
            // $httpcode = curl_getinfo($curl, CURLINFO_HEADER_OUT );
            // var_dump($httpcode);

            curl_close($curl);
        }
        return $content;
    }
}
