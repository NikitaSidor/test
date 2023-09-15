<?php

/*
$sql = 'SELECT DISTINCT `value` FROM '.$modx->getFullTableName('site_tmplvar_contentvalues').' WHERE tmplvarid = '.$field_id;
$result = $modx->db->query( $sql );  
 
while( $row = $modx->db->getRow( $result ) ) {  
  //  $selected = ($row['value']==$field_value) ?  'selected="selected"' : '';
  //  $output .= "<option value='".$row['value']."' ".$selected.">".$row['value']."</option>";
	$val=$row['value'];
}  
*/
echo '

<link rel="stylesheet" href="/assets/tvs/countryID/flags24flat.css" type="text/css" />

<style type="text/css">
   #flag{
	position: relative;
	top: 4px;
}
</style>
<div id="flag" style="display:inline-block"></div>
<select name="tv'.$field_id.'" id="tv'.$field_id.'" style="width: 278px;" onchange="inpChange'.$field_id.'(this);">
    <option value="">Выбрать вариант</option>
    
	<option value="ABKHAZIA">Абхазия</option>
	<option value="AU">Австралия</option>
	<option value="AT">Австрия</option>
	<option value="AZ">Азербайджан</option>
	<option value="AX">Аландские острова</option>
	<option value="AL">Албания</option>
	<option value="DZ">Алжир</option>
	<option value="VI">Виргинские Острова (США)</option>
	<option value="AS">Американское Самоа</option>
	<option value="AI">Ангилья</option>
	<option value="AO">Ангола</option>
	<option value="AD">Андорра</option>
	<option value="AQ">Антарктида</option>
	<option value="AG">Антигуа и Барбуда</option>
	<option value="AR">Аргентина</option>
	<option value="AM">Армения</option>
	<option value="AW">Аруба</option>
	<option value="AF">Афганистан</option>
	<option value="BS">Багамы</option>
	<option value="BD">Бангладеш</option>
	<option value="BB">Барбадос</option>
	<option value="BH">Бахрейн</option>
	<option value="BZ">Белиз</option>
	<option value="BY">Белоруссия</option>
	<option value="BE">Бельгия</option>
	<option value="BJ">Бенин</option>
	<option value="BM">Бермуды</option>
	<option value="BG">Болгария</option>
	<option value="BO">Боливия</option>
	<option value="BQ">Бонэйр, Синт-Эстатиус и Саба</option>
	<option value="BA">Босния и Герцеговина</option>
	<option value="BW">Ботсвана</option>
	<option value="BR">Бразилия</option>
	<option value="IO">Британская территория в Индийском океане</option>
	<option value="VG">Виргинские Острова (Великобритания)</option>
	<option value="BN">Бруней</option>
	<option value="BF">Буркина-Фасо</option>
	<option value="BI">Бурунди</option>
	<option value="BT">Бутан</option>
	<option value="VU">Вануату</option>
	<option value="VA">Ватикан</option>
	<option value="GB">Великобритания</option>
	<option value="HU">Венгрия</option>
	<option value="VE">Венесуэла</option>
	<option value="UM">Внешние малые острова (США)</option>
	<option value="TL">Восточный Тимор</option>
	<option value="VN">Вьетнам</option>
	<option value="GA">Габон</option>
	<option value="HT">Гаити</option>
	<option value="GY">Гайана</option>
	<option value="GM">Гамбия</option>
	<option value="GH">Гана</option>
	<option value="GP">Гваделупа</option>
	<option value="GT">Гватемала</option>
	<option value="GF">Гвиана</option>
	<option value="GN">Гвинея</option>
	<option value="GW">Гвинея-Бисау</option>
	<option value="DE">Германия</option>
	<option value="GG">Гернси</option>
	<option value="GI">Гибралтар</option>
	<option value="HN">Гондурас</option>
	<option value="HK">Гонконг</option>
	<option value="GD">Гренада</option>
	<option value="GL">Гренландия</option>
	<option value="GR">Греция</option>
	<option value="GE">Грузия</option>
	<option value="GU">Гуам</option>
	<option value="DK">Дания</option>
	<option value="JE">Джерси</option>
	<option value="DJ">Джибути</option>
	<option value="DM">Доминика</option>
	<option value="DO">Доминиканская Республика</option>
	<option value="CD">Демократическая Республика Конго</option>
	<option value="EU">Европейский союз</option>
	<option value="EG">Египет</option>
	<option value="ZM">Замбия</option>
	<option value="EH">САДР</option>
	<option value="ZW">Зимбабве</option>
	<option value="IL">Израиль</option>
	<option value="IN">Индия</option>
	<option value="ID">Индонезия</option>
	<option value="JO">Иордания</option>
	<option value="IQ">Ирак</option>
	<option value="IR">Иран</option>
	<option value="IE">Ирландия</option>
	<option value="IS">Исландия</option>
	<option value="ES">Испания</option>
	<option value="IT">Италия</option>
	<option value="YE">Йемен</option>
	<option value="CV">Кабо-Верде</option>
	<option value="KZ">Казахстан</option>
	<option value="KY">Острова Кайман</option>
	<option value="KH">Камбоджа</option>
	<option value="CM">Камерун</option>
	<option value="CA">Канада</option>
	<option value="QA">Катар</option>
	<option value="KE">Кения</option>
	<option value="CY">Кипр</option>
	<option value="KG">Киргизия</option>
	<option value="KI">Кирибати</option>
	<option value="TW">Китайская Республика</option>
	<option value="KP">КНДР</option>
	<option value="CN">КНР</option>
	<option value="CC">Кокосовые острова</option>
	<option value="CO">Колумбия</option>
	<option value="KM">Коморы</option>
	<option value="CR">Коста-Рика</option>
	<option value="CI">Кот-д’Ивуар</option>
	<option value="CU">Куба</option>
	<option value="KW">Кувейт</option>
	<option value="CW">Кюрасао</option>
	<option value="LA">Лаос</option>
	<option value="LV">Латвия</option>
	<option value="LS">Лесото</option>
	<option value="LR">Либерия</option>
	<option value="LB">Ливан</option>
	<option value="LY">Ливия</option>
	<option value="LT">Литва</option>
	<option value="LI">Лихтенштейн</option>
	<option value="LU">Люксембург</option>
	<option value="MU">Маврикий</option>
	<option value="MR">Мавритания</option>
	<option value="MG">Мадагаскар</option>
	<option value="YT">Майотта</option>
	<option value="MO">Макао</option>
	<option value="MK">Македония</option>
	<option value="MW">Малави</option>
	<option value="MY">Малайзия</option>
	<option value="ML">Мали</option>
	<option value="MV">Мальдивы</option>
	<option value="MT">Мальта</option>
	<option value="MA">Марокко</option>
	<option value="MQ">Мартиника</option>
	<option value="MH">Маршалловы Острова</option>
	<option value="MX">Мексика</option>
	<option value="FM">Микронезия</option>
	<option value="MZ">Мозамбик</option>
	<option value="MD">Молдавия</option>
	<option value="MC">Монако</option>
	<option value="MN">Монголия</option>
	<option value="MS">Монтсеррат</option>
	<option value="MM">Мьянма</option>
	<option value="NA">Намибия</option>
	<option value="NR">Науру</option>
	<option value="NP">Непал</option>
	<option value="NE">Нигер</option>
	<option value="NG">Нигерия</option>
	<option value="NL">Нидерланды</option>
	<option value="NI">Никарагуа</option>
	<option value="NU">Ниуэ</option>
	<option value="NZ">Новая Зеландия</option>
	<option value="NC">Новая Каледония</option>
	<option value="NO">Норвегия</option>
	<option value="AE">ОАЭ</option>
	<option value="OM">Оман</option>
	<option value="BV">Остров Буве</option>
	<option value="IM">Остров Мэн</option>
	<option value="CK">Острова Кука</option>
	<option value="NF">Остров Норфолк</option>
	<option value="CX">Остров Рождества</option>
	<option value="PN">Острова Питкэрн</option>
	<option value="SH">Острова Святой Елены, Вознесения и Тристан-да-Кунья</option>
	<option value="PK">Пакистан</option>
	<option value="PW">Палау</option>
	<option value="PS">Государство Палестина</option>
	<option value="PA">Панама</option>
	<option value="PG">Папуа — Новая Гвинея</option>
	<option value="PY">Парагвай</option>
	<option value="PE">Перу</option>
	<option value="PL">Польша</option>
	<option value="PT">Португалия</option>
	<option value="PR">Пуэрто-Рико</option>
	<option value="CG">Республика Конго</option>
	<option value="KR">Республика Корея</option>
	<option value="RE">Реюньон</option>
	<option value="RU">Россия</option>
	<option value="RW">Руанда</option>
	<option value="RO">Румыния</option>
	<option value="SV">Сальвадор</option>
	<option value="WS">Самоа</option>
	<option value="SM">Сан-Марино</option>
	<option value="ST">Сан-Томе и Принсипи</option>
	<option value="SA">Саудовская Аравия</option>
	<option value="SZ">Свазиленд</option>
	<option value="MP">Северные Марианские Острова</option>
	<option value="SC">Сейшельские Острова</option>
	<option value="BL">Сен-Бартелеми</option>
	<option value="MF">Сен-Мартен</option>
	<option value="PM">Сен-Пьер и Микелон</option>
	<option value="SN">Сенегал</option>
	<option value="VC">Сент-Винсент и Гренадины</option>
	<option value="KN">Сент-Китс и Невис</option>
	<option value="LC">Сент-Люсия</option>
	<option value="RS">Сербия</option>
	<option value="SG">Сингапур</option>
	<option value="SX">Синт-Мартен</option>
	<option value="SY">Сирия</option>
	<option value="SK">Словакия</option>
	<option value="SI">Словения</option>
	<option value="SB">Соломоновы Острова</option>
	<option value="SO">Сомали</option>
	<option value="SD">Судан</option>
	<option value="SU">СССР</option>
	<option value="SR">Суринам</option>
	<option value="US">США</option>
	<option value="SL">Сьерра-Леоне</option>
	<option value="TJ">Таджикистан</option>
	<option value="TH">Таиланд</option>
	<option value="TZ">Танзания</option>
	<option value="TC">Тёркс и Кайкос</option>
	<option value="TG">Того</option>
	<option value="TK">Токелау</option>
	<option value="TO">Тонга</option>
	<option value="TT">Тринидад и Тобаго</option>
	<option value="TV">Тувалу</option>
	<option value="TN">Тунис</option>
	<option value="TM">Туркмения</option>
	<option value="TR">Турция</option>
	<option value="UG">Уганда</option>
	<option value="UZ">Узбекистан</option>
	<option value="UA">Украина</option>
	<option value="WF">Уоллис и Футуна</option>
	<option value="UY">Уругвай</option>
	<option value="FO">Фареры</option>
	<option value="FJ">Фиджи</option>
	<option value="PH">Филиппины</option>
	<option value="FI">Финляндия</option>
	<option value="FK">Фолклендские острова</option>
	<option value="FR">Франция</option>
	<option value="PF">Французская Полинезия</option>
	<option value="TF">Французские Южные и Антарктические Территории</option>
	<option value="HM">Херд и Макдональд</option>
	<option value="HR">Хорватия</option>
	<option value="CF">ЦАР</option>
	<option value="TD">Чад</option>
	<option value="ME">Черногория</option>
	<option value="CZ">Чехия</option>
	<option value="CL">Чили</option>
	<option value="CH">Швейцария</option>
	<option value="SE">Швеция</option>
	<option value="SJ">Шпицберген и Ян-Майен</option>
	<option value="LK">Шри-Ланка</option>
	<option value="EC">Эквадор</option>
	<option value="GQ">Экваториальная Гвинея</option>
	<option value="ER">Эритрея</option>
	<option value="EE">Эстония</option>
	<option value="ET">Эфиопия</option>
	<option value="ZA">ЮАР</option>
	<option value="GS">Южная Георгия и Южные Сандвичевы Острова</option>
	<option value="SS">Южный Судан</option>
	<option value="JM">Ямайка</option>
	<option value="JP">Япония</option>
</select>

<script type="text/javascript">
function inpChange'.$field_id.'(obj){
    var el'.$field_id.', s'.$field_id.', n'.$field_id.', v'.$field_id.';
    el'.$field_id.'=obj.options;
    n'.$field_id.'=el'.$field_id.'.selectedIndex;
    v'.$field_id.'=el'.$field_id.'[n'.$field_id.'].value;
    s'.$field_id.'="tv'.$field_id.'";
	document.getElementById("flag").innerHTML="<span class=\"fl-" +v'.$field_id.'+ "\"></span>";

};
var val="'.$field_value.'";

var sel=document.getElementById("tv'.$field_id.'");
for (i=0;i<sel.options.length;i++ ){
    if (val==sel.options[i].value){
        sel.options[i].setAttribute("selected","selected");
        document.getElementById("flag").innerHTML="<span class=\"fl-" +val+ "\"></span>";
    }
} 

</script>
';
?>