<?
function createdon(&$object,$params){
	global $modx,$validateFields;
	if (isset($_POST['createdon']) && !empty($_POST['createdon'])) {
		// $object->config['validate'] .= ",createdon:Формат даты должен быть ДД.ММ.ГГГГ:date";
		$object->config["form"]["validation"]['createdon'] = Array(
			0=>Array(
				"validation" => "date",
				"msg" => "Формат даты должен быть ДД.ММ.ГГГГ"
			)
		); 
	}
}