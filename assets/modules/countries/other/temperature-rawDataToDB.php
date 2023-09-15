<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$countries = Array(
    "ABKHAZIA" => "Абхазия",
    "AD" => "Андорра",
    "AE" => "Объединённые Арабские Эмираты",
    "AF" => "Афганистан",
    "AG" => "Антигуа и Барбуда",
    "AI" => "Ангилья",
    "AL" => "Албания",
    "AM" => "Армения",
    "AN" => "Нидерландские Антилы",
    "AO" => "Ангола",
    "AQ" => "Антарктида",
    "AR" => "Аргентина",
    "AS" => "Американское Самоа",
    "AT" => "Австрия",
    "AU" => "Австралия",
    "AW" => "Аруба",
    "AX" => "Эландские острова",
    "AZ" => "Азербайджан",
    "BA" => "Босния и Герцеговина",
    "BB" => "Барбадос",
    "BD" => "Бангладеш",
    "BE" => "Бельгия",
    "BF" => "Буркина Фасо",
    "BG" => "Болгария",
    "BH" => "Бахрейн",
    "BI" => "Бурунди",
    "BJ" => "Бенин",
    "BL" => "Сен-Бартельми",
    "BM" => "Бермуды",
    "BN" => "Бруней",
    "BO" => "Боливия",
    "BR" => "Бразилия",
    "BS" => "Багамы",
    "BT" => "Бутан",
    "BV" => "Остров Буве",
    "BW" => "Ботсвана",
    "BY" => "Беларусь",
    "BZ" => "Белиз",
    "CA" => "Канада",
    "CC" => "Кокосовые (Килинг) острова",
    "CD" => "Конго (Демократическая Республика)",
    "CF" => "Центральноафриканская Республика",
    "CG" => "Конго",
    "CH" => "Швейцария",
    "CI" => "Кот-д’Ивуар",
    "CK" => "Острова Кука",
    "CL" => "Чили",
    "CM" => "Камерун",
    "CN" => "Китай",
    "CO" => "Колумбия",
    "CR" => "Коста-Рика",
    "CU" => "Куба",
    "CV" => "Кабо-Верде",
    "CX" => "Остров Рождества",
    "CY" => "Кипр",
    "CZ" => "Чехия",
    "DE" => "Германия",
    "DJ" => "Джибути",
    "DK" => "Дания",
    "DM" => "Доминика",
    "DO" => "Доминиканская Республика",
    "DZ" => "Алжир",
    "EC" => "Эквадор",
    "EE" => "Эстония",
    "EG" => "Египет",
    "EH" => "Западная Сахара",
    "ER" => "Эритрея",
    "ES-CE" => "Сеута",
    "ES-ML" => "Мельлия",
    "ES" => "Испания",
    "ET" => "Эфиопия",
    "EU" => "Евросоюз",
    "FI" => "Финляндия",
    "FJ" => "Фиджи",
    "FK" => "Фолклендские острова (Мальвинские)",
    "FM" => "Микронезия",
    "FO" => "Фарерские острова",
    "FR" => "Франция",
    "GA" => "Габон",
    "GB" => "Великобритания",
    "GD" => "Гренада",
    "GE" => "Грузия",
    "GF" => "Французская Гвиана",
    "GG" => "Гернси",
    "GH" => "Гана",
    "GI" => "Гибралтар",
    "GL" => "Гренландия",
    "GM" => "Гамбия",
    "GN" => "Гвинея",
    "GP" => "Гваделупа",
    "GQ" => "Экваториальная Гвинея",
    "GR" => "Греция",
    "GS" => "Южная Джорджия и Южные Сандвичевы острова",
    "GT" => "Гватемала",
    "GU" => "Гуам",
    "GW" => "Гвинея-Бисау",
    "GY" => "Гайана",
    "HK" => "Гонконг",
    "HM" => "Остров Херд и острова Макдональд",
    "HN" => "Гондурас",
    "HR" => "Хорватия",
    "HT" => "Гаити",
    "HU" => "Венгрия",
    "IC" => "Канарские острова",
    "ID" => "Индонезия",
    "IE" => "Ирландия",
    "IL" => "Израиль",
    "IM" => "Остров Мэн",
    "IN" => "Индия",
    "IO" => "Британская территория в Индийском океане",
    "IQ" => "Ирак",
    "IR" => "Иран",
    "IS" => "Исландия",
    "IT" => "Италия",
    "JE" => "Джерси",
    "JM" => "Ямайка",
    "JO" => "Иордания",
    "JP" => "Япония",
    "KE" => "Кения",
    "KG" => "Киргизия",
    "KH" => "Камбоджа",
    "KI" => "Кирибати",
    "KM" => "Коморы",
    "KN" => "Сент-Китс и Невис",
    "KOSOVO" => "Косово",
    "KP" => "Северная Корея",
    "KR" => "Южная Корея",
    "KW" => "Кувейт",
    "KY" => "Острова Кайман",
    "KZ" => "Казахстан",
    "LA" => "Лаос",
    "LB" => "Ливан",
    "LC" => "Сент-Люсия",
    "LI" => "Лихтенштейн",
    "LK" => "Шри-Ланка",
    "LR" => "Либерия",
    "LS" => "Лесото",
    "LT" => "Литва",
    "LU" => "Люксембург",
    "LV" => "Латвия",
    "LY" => "Ливия",
    "MA" => "Марокко",
    "MC" => "Монако",
    "MD" => "Молдова",
    "ME" => "Черногория",
    "MF" => "Остров Святого Мартина",
    "MG" => "Мадагаскар",
    "MH" => "Маршалловы острова",
    "MK" => "Македония",
    "ML" => "Мали",
    "MM" => "Мьянма",
    "MN" => "Монголия",
    "MO" => "Макао",
    "MP" => "Северные Марианские острова",
    "MQ" => "Мартиника",
    "MR" => "Мавритания",
    "MS" => "Монтсеррат",
    "MT" => "Мальта",
    "MU" => "Маврикий",
    "MV" => "Мальдивы",
    "MW" => "Малави",
    "MX" => "Мексика",
    "MY" => "Малайзия",
    "MZ" => "Мозамбик",
    "NA" => "Намибия",
    "NC" => "Новая Каледония",
    "NE" => "Нигер",
    "NF" => "Остров Норфолк",
    "NG" => "Нигерия",
    "NI" => "Никарагуа",
    "NKR" => "Нагорно-Карабахская Республика",
    "NL" => "Нидерланды",
    "NO" => "Норвегия",
    "NP" => "Непал",
    "NR" => "Науру",
    "NU" => "Ниуэ",
    "NZ" => "Новая Зеландия",
    "OM" => "Оман",
    "PA" => "Панама",
    "PE" => "Перу",
    "PF" => "Французская Полинезия",
    "PG" => "Папуа-Новая Гвинея",
    "PH" => "Филиппины",
    "PK" => "Пакистан",
    "PL" => "Польша",
    "PM" => "Сен-Пьер и Микелон",
    "PN" => "Питкерн",
    "PR" => "Пуэрто-Рико",
    "PS" => "Палестинская автономия",
    "PT" => "Португалия",
    "PW" => "Палау",
    "PY" => "Парагвай",
    "QA" => "Катар",
    "RE" => "Реюньон",
    "RO" => "Румыния",
    "RS" => "Сербия",
    "RU" => "Россия",
    "RW" => "Руанда",
    "SA" => "Саудовская Аравия",
    "SB" => "Соломоновы острова",
    "SC" => "Сейшелы",
    "SD" => "Судан",
    "SE" => "Швеция",
    "SG" => "Сингапур",
    "SH" => "Святая Елена",
    "SI" => "Словения",
    "SJ" => "Шпицберген и Ян Майен",
    "SK" => "Словакия",
    "SL" => "Сьерра-Леоне",
    "SM" => "Сан-Марино",
    "SN" => "Сенегал",
    "SO" => "Сомали",
    "SOUTH-OSSETIA" => "Южная Осетия",
    "SR" => "Суринам",
    "SS" => "Южный Судан",
    "ST" => "Сан-Томе и Принсипи",
    "SV" => "Эль-Сальвадор",
    "SY" => "Сирийская Арабская Республика",
    "SZ" => "Свазиленд",
    "TC" => "Острова Теркс и Кайкос",
    "TD" => "Чад",
    "TF" => "Французские Южные территории",
    "TG" => "Того",
    "TH" => "Таиланд",
    "TJ" => "Таджикистан",
    "TK" => "Токелау",
    "TL" => "Тимор-Лесте",
    "TM" => "Туркменистан",
    "TN" => "Тунис",
    "TO" => "Тонга",
    "TR" => "Турция",
    "TT" => "Тринидад и Тобаго",
    "TV" => "Тувалу",
    "TW" => "Тайвань",
    "TZ" => "Танзания",
    "UA" => "Украина",
    "UG" => "Уганда",
    "UM" => "Малые Тихоокеанские отдаленные острова Соединенных Штатов",
    "US" => "Соединенные Штаты Америки",
    "UY" => "Уругвай",
    "UZ" => "Узбекистан",
    "VA" => "Папский Престол (Ватикан)",
    "VC" => "Сент-Винсент и Гренадины",
    "VE" => "Венесуэла",
    "VG" => "Виргинские острова (Британские)",
    "VI" => "Виргинские острова (США)",
    "VN" => "Вьетнам",
    "VU" => "Вануату",
    "WF" => "Уоллис и Футуна",
    "WS" => "Самоа",
    "YE" => "Йемен",
    "YT" => "Майотта",
    "ZA" => "Южная Африка",
    "ZM" => "Замбия",
    "ZW" => "Зимбабве",
);
$lostCodes = Array(
    'ОАЭ' => 'AE',
    'Доминикана' => 'DO',
    'ЮАР' => 'ZA',
    'США' => 'US',
);
/*
// http://demo6.tourvisor.ru/
var data = [];
jQuery('.TVCalendarCountyList > div').each(function(){
    data.push({
        "c":jQuery(this).find('.TVCalendarCountryValue').text(),
        "a":jQuery(this).find('.TVCalendarAir').text(),
        "w":jQuery(this).find('.TVCalendarWater').text()
    });
});
data = JSON.stringify(data);
*/
$month = 'dec';
$json = '
[{"c":"Абхазия","a":"+10","w":"+12"},{"c":"Россия","a":"+10","w":"+11"},{"c":"Испания","a":"+13","w":"+16"},{"c":"Эстония","a":"+1","w":"+5"},{"c":"Италия","a":"+9","w":"+12"},{"c":"Китай","a":"+22","w":"+25"},{"c":"ОАЭ","a":"+26","w":"+24"},{"c":"Бахрейн","a":"+21","w":"+21"},{"c":"Иордания","a":"+22","w":"+23"},{"c":"Индонезия","a":"+33","w":"+28"},{"c":"Доминикана","a":"+30","w":"+27"},{"c":"Вьетнам","a":"+27","w":"+24"},{"c":"Мальдивы","a":"+30","w":"+28"},{"c":"Маврикий","a":"+29","w":"+26"},{"c":"Мексика","a":"+27","w":"+26"}]
';
$data = json_decode($json);
foreach ($data as $d) {
    $code = array_search ($d->c, $countries);
    $d->a = str_replace('+','',$d->a);
    $d->w = str_replace('+','',$d->w);
    $d->a = str_replace('--','-',$d->a);
    $d->w = str_replace('--','-',$d->w);
    if ($code) {
        echo "UPDATE evo_countries_temperature SET air_$month = $d->a WHERE code = '$code';";
        echo '<br>';
        echo "UPDATE evo_countries_temperature SET water_$month = $d->w WHERE code = '$code';";
        echo '<br>';
    } else {
        if (isset($lostCodes[$d->c])) {
            $code = $lostCodes[$d->c];
            echo "UPDATE evo_countries_temperature SET air_$month = $d->a WHERE code = '$code';";
            echo '<br>';
            echo "UPDATE evo_countries_temperature SET water_$month = $d->w WHERE code = '$code';";
            echo '<br>';
        } else {
            $lost[] = "UPDATE evo_countries_temperature SET air_$month = $d->a WHERE code = '{$d->c}';";
            $lost[] = "UPDATE evo_countries_temperature SET water_$month = $d->w WHERE code = '{$d->c}';";
        }
    }
}
if (isset($lost) && !empty($lost)) {
    echo '--------------';
    echo '<br>';
    foreach ($lost as $l)
        echo $l.'<br>';
}