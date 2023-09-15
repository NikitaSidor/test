<?php

if (isset($_GET['action']) && $_GET['action']=='clear-clr-cache') {
	// initialize the variables prior to grabbing the config file
	$database_type = '';
	$database_server = '';
	$database_user = '';
	$database_password = '';
	$dbase = '';
	$table_prefix = '';
	$base_url = '';
	$base_path = '';
	// get the required includes
	$rt = @include($_SERVER['DOCUMENT_ROOT'].'/admin/includes/config.inc.php');
	if(!$rt || !$database_type || !$database_server || !$database_user || !$dbase) {
	    echo "Ошибка с файлом конфигурации";
	    exit;
	}

	startCMSSession();
    if ($_SESSION['mgrValidated']) {
		// Be sure config.inc.php is there and that it contains some important values
		define('MODX_API_MODE', true);
		// initiate a new document parser
		include_once(MODX_MANAGER_PATH.'includes/document.parser.class.inc.php');
		$modx = new DocumentParser;
		// $this->modx->getSettings();

		if (is_numeric($_GET['id'])) {
			$path = __DIR__ . "/../../../scripts/rn-best-prices/cache/toursdata-{$_GET['id']}.json";
			if(file_exists($path)) {
				unlink($path);
				echo 'Кеш очищен';
			} else {
				echo 'Кеш-файл отсутствует';
			}
		}
    }

	die();
}

include_once($_SERVER['DOCUMENT_ROOT'].'/scripts/rn-best-prices/config.php');

$selected = Array();
$selectedCountry = false;
if (isset($field_value) && $field_value != '') {
	$selected = explode(',',$field_value);
}
if (!empty($selected)) {
	$style='display:none;';
	$disabled = '';
	// $scDefault = '';
}
else {
	$style='';
	$disabled = 'disabled';
	// $scDefault = 'selected="selected"';
}

// Страны
$html = "<select name='cl-country' id='cl-country' style='vertical-align:top;margin-bottom:10px;'>
	<option value=''>Выберите страну</option>";
foreach ($countries as $k => $v) {
	// очистим страны с пустыми курортами
	if (isset($resorts[$k])) {
		$sel = '';
		if (isset($selected) && array_key_exists($selected[0], $resorts[$k])) { // если есть значение, выставим страну по умолчанию
			$sel = ' selected="selected"';
		}

		$html .= "<option value='$k'$sel>$v</option>";
	} else {
		unset($countries[$k]);
	}
}
$html .= '</select>';

// Курорты

$html .= "<select size='10' name='cl-resorts' id='cl-resorts' multiple $disabled>";

foreach ($resorts as $countryID => $array) {
	foreach ($array as $k => $v) {
		$sel = '';
		if (!empty($selected) && in_array($k, $selected)) {
			$sel = ' selected="selected"';
			$selectedCountry = $countryID;
		}
		$html .= "<option value='$k' data-country='$countryID' style='$style'$sel>$v</option>";
	}
}
$html .= '</select>';

$html .= '<input type="button" value="Очистить кеш" onclick="clearCLRcache()" style="vertical-align: top;"><span id="clrCacheMsg" style="vertical-align: top;padding-top: 5px;display: inline-block;"></span>';
$html .= <<<CLRCACHE
<script>
function clearCLRcache(id) {
	var xhttp = new XMLHttpRequest();
	xhttp.open("GET", "/assets/tvs/cl-resorts/index.php?id={$content['id']}&action=clear-clr-cache", true);
	xhttp.send();
	xhttp.onreadystatechange = function() {
	    if (xhttp.readyState == XMLHttpRequest.DONE) {
			document.getElementById('clrCacheMsg').innerHTML = xhttp.responseText;
	    }
	}
}
</script>
CLRCACHE;

// Если выбрана страна, отобразим
if ($selectedCountry !== false) {
	$html .= 
"<script>
	$$('#cl-country option').each(function(el){
		if (el.value == $selectedCountry) {
			el.setAttribute('selected', 'selected');
		}
	});
	$$('#cl-resorts option').each(function(el){
		if (el.getProperty('data-country') == $selectedCountry) {
			el.setStyle('display', 'block');
		}
	});
</script>";
}

// скрипт
$html .= <<<SCRIPT
<input type="hidden" id="tv$field_id" name="tv$field_id" value="$field_value">
<script>
$("cl-country").addEvent('change', function(e){
	var selC = document.getElementById("cl-country"),
		cid = selC.options[selC.selectedIndex].value;

	if (cid == '') {
		$("cl-resorts").setAttribute('disabled').set("value", '');
	} else {
		$("cl-resorts").removeAttribute("disabled");
		$$("#cl-resorts option").each(function(el) {
			if (el.getAttribute('data-country') == cid) {
				el.setStyle('display','block');
			} else {
				el.setStyle('display','none');
			}
		});
	}
});
$("cl-resorts").addEvent('change', function(el){
	var resorts = getSelectValues(el.target);
	console.info(resorts);
	$('tv$field_id').value=resorts.join();
});

function getSelectValues(select) {
  var result = [];
  var options = select && select.options;
  var opt;

  for (var i=0, iLen=options.length; i<iLen; i++) {
    opt = options[i];

    if (opt.selected) {
      result.push(opt.value || opt.text);
    }
  }
  return result;
}
</script>
SCRIPT;

echo $html;