<?php
# системные данные
# порядок
# отправка только через форму

# getModxInstance
define('MODX_API_MODE', true);
include_once("../../index.php");
global $modx;
$modx->db->connect();
if (empty($modx->config)) {
 	$modx->getSettings();
}


class formSender {

	# Дефолтный конфиг
	private $config=array(
		'subject'=>'Заявка с сайта',
		
		'template'=>"
			<table>
			<tr>
				<td>ФИО</td>
				<td>[+n+]</td>
			</tr>
			<tr>
				<td>Телефон</td>
				<td>[+t+]</td>
			</tr>
			<tr>
				<td>Сообщение</td>
				<td>[+m+]</td>
			</tr>
		</table>
		Запрос отправлен [+sysDate+] [+sysTime+] с [+sysIp+]",
		'isAjax'=>true,
		'emptyFieldName'=>'name',
		'noEmptyFieldName'=>'email',
		'noEmptyFieldValue'=>'19X84',
		'cookieName'=>'',
		'debug'=>true,
		'files'=>array()  #полные пути к прикрепляемым файлам
	);
	var $modx;
	private $attachments;


	public function __construct() {
		$this->sanitizeData();
		$this->loadConfig();
		$this->config['template']=$this->parseTemplate($this->config['template'], $_REQUEST);
		$this->config['template'].='<p>Запрос отправлен '.date('d.m.Y H:i').' с IP '.$this->getIp().'</p>';


		if ($this->isSpam()){
			 if($this->config['debug']){
				die("Спам");
			 } else {
				$this->echoJSON('success');
			 }
		}

		#var_dump($this->mailToConfig()); die();
		if ($this->sendEmail($this->mailToConfig())){
			$this->echoJSON('success');
		} else {
			$this->echoJSON('error');
		}
		
	}


	public function sanitizeData(){
		foreach ($_REQUEST as $k => $v) {
			$_REQUEST[$k] = strip_tags($_REQUEST[$k]);
			$_REQUEST[$k] = htmlspecialchars($_REQUEST[$k]);
			$_REQUEST[$k] = trim($_REQUEST[$k]);
		}
	}

	private function loadConfig(){
		include_once('config.php');
		if(!$_REQUEST){die();}
		if (isset($_REQUEST['form']) && $_REQUEST['form'] && is_array($form[$_REQUEST['form']])) {
			$this->config = array_merge($this->config, $form[$_REQUEST['form']]);
			return true;
		} else {
			if ($this->config['debug']){
				die('Ошибка в конфиге формы ');
			}
		}
	}

	public function parseTemplate($template, $vars){
		return  preg_replace_callback('/\[\+([a-z0-9]+)\+\]\w*/is', function (array $matches) use ($vars) {
			if(isset($vars[$matches[1]])){
				return $vars[$matches[1]];
			}
		}, $template);
	}

	public function getIp()	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}


	public function isSpam(){
		if($this->config['isAjax'] && !$this->isAjax()){
			return true;
		} else if (!$this->checkSpecialFields()){
			return true;
		} else if (!$this->checkSpecialCookie()){
			return true;
		} else {
			return false;
		}
	}
	public function isAjax(){
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	public function checkSpecialFields(){
		if ($this->config['emptyFieldName'] && !empty($_REQUEST[$this->config['emptyFieldName']])) {
			return false;
		} else if ($this->config['noEmptyFieldName'] && $_REQUEST[$this->config['noEmptyFieldName']]!=$this->config['noEmptyFieldValue']){
			return false;
		} else {
			return true;
		}		
	}

	public function checkSpecialCookie(){
		if ($this->config['cookieName'] && $_COOKIE[$this->config['cookieName']]){
			return false;
		} else {
			return true;
		}
	}

	public function mailToConfig(){
		global $modx;
		return array(
			'from'		=>$modx->config['email_method']=='mail' ? $modx->config['emailsender']: $modx->config['smtp_username'],
			'fromName'	=>$modx->config['site_name'],
			'sender'	=>$modx->config['email_method']=='mail' ? $modx->config['emailsender']: $modx->config['smtp_username'],
			'subject'	=>$this->config['subject'],
			'body'		=>$this->config['template'],
			'to'		=>$this->config['to']?$this->config['to']:($modx->config['email_method']=='mail' ? $modx->config['emailsender']: $modx->config['smtp_username']),
			'replyTo'	=>trim($this->config['replyTo'])
		);
	}

	public function sendEmail($mailConfig) {
		global $modx;
		global $attachments;
		$attachments=array();
	

		$modx->loadExtension('MODxMailer');
		$modx->mail->IsHTML(true);

		$modx->mail->From		= $mailConfig['from'];
		$modx->mail->Sender 	= $mailConfig['sender'];
		$modx->mail->FromName	= $mailConfig['fromName'];
		$modx->mail->Subject	= $mailConfig['subject'];
		$modx->mail->Body		= $mailConfig['body'];
		
		$mailConfig['to']=explode(',',$mailConfig['to']);
		foreach ($mailConfig['to'] as $to) {
			$modx->mail->addAddress(trim($to));	
		}
		
		if ($mailConfig['replyTo']){
			$modx->mail->AddReplyTo($mailConfig['replyTo']);
		}
		
		$this->getFormAttachment();
		
		$this->AttachFilesToMailer($modx->mail,$attachments);

		$res = $modx->mail->send();
		$modx->mail->ClearAllRecipients();
		$modx->mail->ClearAttachments();
		if (!$res && $this->config['debug']){
			die ($modx->mail->ErrorInfo);
		}
		return $res;

	}

            
            

	// Attach Files to Mailer
	public function AttachFilesToMailer(&$mail,&$attachFiles) {
		if(count($attachFiles)>0){
			foreach($attachFiles as $attachFile){
				if(!is_file($attachFile)) continue;
				$FileName = $attachFile;
				$contentType = "application/octetstream";
				if (is_uploaded_file($attachFile)){
					foreach($_FILES as $n => $v){
						if($_FILES[$n]['tmp_name']==$attachFile) {
							$FileName = $_FILES[$n]['name'];
							$contentType = $_FILES[$n]['type'];
						}
					}
				}
				$patharray = explode(((strpos($FileName,"/")===false)? "\\":"/"), $FileName);
				$FileName = $patharray[count($patharray)-1];
				$mail->AddAttachment($attachFile,$FileName,"base64",$contentType);
			}
		}
	}

	public function getFormAttachment(){
		global $attachments;
		if (isset($_FILES) &&  is_array($_FILES)){
			foreach ($_FILES as $key => $value) {
				$attachments[count($attachments)] = $_FILES[$key]['tmp_name'];
			}

		}
		if (isset($this->config['files']) && is_array($this->config['files'])) {
			foreach ($this->config['files'] as $key => $value) {
				$attachments[count($attachments)] = $value;
			}
		}
	}





	public function echoJSON($status){
			header('Content-Type: application/json');
			die(json_encode(array('status'=>$status,)));
	}

}

$foo = new formSender();



?>