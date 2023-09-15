<?php
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

if (empty($_POST)) {
    die();
}
if(!empty($_POST['yourname'])) { // vs spam
	die('success');
}

// Путеводитель
if (isset($_POST['rid'])) {
	if (empty($_POST['rid'])) {
	    die('Aдминистратором не указан rid - id ресурса');
	}
	if (empty($_POST['email'])) {
	    die('Пользователем не указан email');
	}
}
// Подобрать самый лучший тур сейчас
if (isset($_POST['type_feed']) && $_POST['type_feed'] == 1) {
	if (empty($_POST['phone'])) {
		die('Пользователем не заполнены данные');
	}
}
// Подпишитесь на горящие туры 
if (isset($_POST['type_feed']) && $_POST['type_feed'] == 3) {
	if (empty($_POST['email'])) {
		die('Пользователем не заполнены данные');
	}
}
// Заказать звонок
if (isset($_POST['type_feed']) && $_POST['type_feed'] == 4) {
	if (empty($_POST['name']) || empty($_POST['phone'])) {
		die('Пользователем не заполнены данные');
	}
}
// Консультация
if (isset($_POST['type_feed']) && $_POST['type_feed'] == 6) {
	if (empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['email'])) {
		die('Пользователем не заполнены данные');
	}
}
// Тур - Доверьте подбор тура профессионалам!
if (isset($_POST['formtype']) && $_POST['formtype'] == 'tour') {
	if (empty($_POST['name']) || empty($_POST['phone'])) {
		die('Пользователем не заполнены данные');
	}
}

// require('phpmailer/class.phpmailer.php');

/* ----------------------------------------------  Подключение MODX */
// initialize the variables prior to grabbing the config file



# getModxInstance
define('MODX_API_MODE', true);
include_once("../index.php");
global $modx;
$modx->db->connect();
if (empty($modx->config)) {
 	$modx->getSettings();
}


/* ---------------------------------------------- Подключение MODX КОНЕЦ*/

// Настройки из MODX панели админа
$email = $modx->getConfig('emailsender');
$sitename = $modx->getConfig('site_name');
$siteurl = str_replace('scripts/', '', $modx->getConfig('site_url')); // убираем текущую папку из пути



// ---------------------------------------------- Путеводитель
if (isset($_POST['rid'])) {
	// Получаем значения TV
	$tvsDB = $modx->getTemplateVars(
	    array('country-about','guide-file'), 
	    'name',
	    $_POST['rid']*1
	);
	$tvs = Array();
	foreach ($tvsDB as $tvdb) {
	    $tvs[$tvdb['name']] = $tvdb['value'];
	}
	if (empty($tvs['country-about']) || empty($tvs['guide-file'])) {
	    die('Администратором указаны не все параметры страницы');
	}

	// Файл путеводителя
	$filePath = '../'.$tvs['guide-file'];
	if (!file_exists($filePath)) {
	    $filePath = '../'.urldecode($tvs['guide-file']);
	    if (!file_exists($filePath))
	        die("Файл путеводителя не найден");
	}
	// Русскоязычные названия файлов подрезаются до первого ansi символа - делаем вручную
	$fileName = explode('/',$filePath);
	$fileName = end($fileName);

	foreach ($_POST as $k => &$value) {
	    $value = filter_var($value, FILTER_SANITIZE_STRING);
	}
	sendEmail(Array(
		'subject' => "Путеводитель по {$tvs['country-about']}",
		'body' => "
		    <p>PDF-путеводитель по {$tvs['country-about']}</p>
		    <p><strong>Имя:</strong> {$_POST["name"]}</p>
		    <p><strong>Email:</strong> {$_POST["email"]}</p>",
	));
	sendEmail(Array(
		'to' => $_POST['email'],
		'toName' => $_POST['name'],
		'body' => "PDF-путеводитель по {$tvs['country-about']} и другим актуальным пляжным направлениям отдыха",
		'filePath' => $filePath,
		'fileName' => $fileName,
	));
	exit;
}
// ---------------------------------------------- Путеводитель КОНЕЦ

// ---------------------------------------------- Консультация
if (isset($_POST['type_feed']) && $_POST['type_feed'] == 6) {
	foreach ($_POST as $k => &$value) {
	    $value = filter_var($value, FILTER_SANITIZE_STRING);
	}
	sendEmail(Array(
		'subject' => 'Консультация: '.$_POST['subject'],
		'body' => "
		    <p>Тема: {$_POST['subject']}</p>
		    <p><strong>Имя:</strong> {$_POST["name"]}</p>
		    <p><strong>Email:</strong> {$_POST["email"]}</p>
		    <p><strong>Телефон:</strong> {$_POST["phone"]}</p>",
	), true);
	exit;
}
// ---------------------------------------------- Консультация КОНЕЦ

// ---------------------------------------------- Подобрать самый лучший тур сейчас
if (isset($_POST['type_feed']) && $_POST['type_feed'] == 1) {
	foreach ($_POST as $k => &$value) {
	    $value = filter_var($value, FILTER_SANITIZE_STRING);
	}
	sendEmail(Array(
		'subject' => 'Подобрать самый лучший тур сейчас',
		'body' => "
		    <p><strong>Имя:</strong> {$_POST["name"]}</p>
		    <p><strong>Email:</strong> {$_POST["email"]}</p>
		    <p><strong>Телефон:</strong> {$_POST["phone"]}</p>",
	), true);
	exit;
}
// ---------------------------------------------- Подобрать самый лучший тур сейчас КОНЕЦ

// ---------------------------------------------- Подписка на горящие туры
if (isset($_POST['type_feed']) && $_POST['type_feed'] == 3) {
	foreach ($_POST as $k => &$value) {
	    $value = filter_var($value, FILTER_SANITIZE_STRING);
	}
	sendEmail(Array(
		'subject' => 'Подписка на горящие туры',
		'body' => "<p><strong>Email:</strong> {$_POST["email"]}</p><p><strong>Страна:</strong> {$_POST["country"]}</p>"
	), true);
	exit;
}
// ---------------------------------------------- Подписка на горящие туры КОНЕЦ

// ---------------------------------------------- Заказать звонок
if (isset($_POST['type_feed']) && $_POST['type_feed'] == 4) {
	foreach ($_POST as $k => &$value) {
	    $value = filter_var($value, FILTER_SANITIZE_STRING);
	}
	sendEmail(Array(
		'subject' => 'Заказ звонка',
		'body' => "<p><strong>Имя:</strong> {$_POST["name"]}</p><p><strong>Телефон:</strong> {$_POST["phone"]}</p>"
	), true);
	exit;
}
// ---------------------------------------------- Заказать звонок КОНЕЦ

// ---------------------------------------------- Тур
if (isset($_POST['formtype']) && $_POST['formtype'] == 'tour') {
	foreach ($_POST as $k => &$value) {
	    $value = filter_var($value, FILTER_SANITIZE_STRING);
	}
	sendEmail(Array(
		'subject' => 'Подобрать тур',
		'body' => "
		    <p><strong>Имя:</strong> {$_POST["name"]}</p>
		    <p><strong>Телефон:</strong> {$_POST["phone"]}</p>
		    <p><strong>Комментарий:</strong></p>
		    <div>
		    {$_POST["comment"]}
		    </div>
		",
	), true);
	exit;
}
// ---------------------------------------------- Тур КОНЕЦ

// Отправляем
function sendEmail($p, $notify=false) {
	global $modx;

	if (empty($p) || empty($p['body']))
		die('Ошибка при указании параметров отправки письма');
	global $email, $sitename;
	if (empty($p['from'])) { // От 
		if ($modx->config['email_method'] == 'smtp') {
			$p['from'] = $modx->config['smtp_username'];
		} else { // mail
			$p['from'] = $email;
		}
	}
	if (empty($p['to'])) { // по умолчанию админу
		if ($modx->config['email_method'] == 'smtp') {
			$p['to'] = $modx->config['smtp_username'];
		} else { // mail
			$p['to'] = $email;
		}
	}
	if (empty($p['toName']))
		$p['toName'] = $sitename;
	if (empty($p['subject']))
		$p['subject']="Сообщение с сайта";
	
	// $mail = new PHPMailer;
	// $mail->CharSet = 'UTF-8';
	// $mail->isHTML(true);
	// $mail->setFrom($email, $sitename);
	// $mail->addAddress($p['to'], $p['toName']);
	// $mail->Subject = $p['subject'];
	// $mail->Body = $p['body'];
	// if ($p['filePath'] && $p['fileName'])
	// 	$mail->addAttachment($p['filePath'], $p['fileName']);
	// $res = $mail->send();
	
	// From Eform
	$modx->loadExtension('MODxMailer');
	$modx->mail->IsHTML(true);
	$modx->mail->From		= $p['from'];
	$modx->mail->FromName	= $p['toName'];
	$modx->mail->Subject	= $p['subject'];
	$modx->mail->Body		= $p['body'];
	if (isset($p['filePath']) && isset($p['fileName']))
		$modx->mail->addAttachment($p['filePath'], $p['fileName']);
	
	$modx->mail->addAddress($p['to'], $p['toName']);

	$res = $modx->mail->send();
	$modx->mail->ClearAllRecipients();
	$modx->mail->ClearAttachments();

	if ($p['to'] != $email || $notify) {
		if(!$res) {
		    echo 'Ошибка при отправке письма';
		    // echo PHP_EOL;
		    // var_dump($modx->mail->ErrorInfo);
		    // echo PHP_EOL;
		    // var_dump($modx->config);
		    // echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
		    echo 'success';
		}
	}
}


