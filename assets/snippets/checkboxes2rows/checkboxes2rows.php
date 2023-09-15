<?php
/*
&rowTpl - шаблон ряда
Принимает плейсхолдеры
[+num+] - порядковый номер
[+value+] - значение параметра
[+name+] - имя параметра
[+hidden+] - класс hidden для сокрытия
&tv - название TV
&display - кол-во выводимых элементов
&show - кол-во показываемых элементов, остальным задает класс hidden
&count - подсчет кол-ва выбранных значений
Пример вызова
[[checkboxes2rows 
&tv=`hotelFacilities`
&rowTpl=`<div class="col-xs-4 [+num+] [+hidden+] comfort-item"><div class="icon-[+value+]"></div><p class="comf-text">[+name+]</p></div>`
&display=`3`
&show=`2`
]]
[[checkboxes2rows 
&tv=`hotelFacilities`
&count=`1`
]]	
*/
global $modx;

if (!isset($tv) || empty($tv))
    return false;
if (!isset($values) && (!isset($modx->documentObject[$tv]) || empty($modx->documentObject[$tv][1])))
    return false;

if (!isset($values))
    $values = $modx->documentObject[$tv][1];

if (!is_array($values))
    $values = explode('||', $values);

if (isset($count))
    return count($values);

if (!isset($rowTpl) || empty($rowTpl))
    return false;

$display = (isset($display) ? $display : 0);
$show = (isset($show) ? $show : false);

$data = [];
// if (!isset($modx->cache->tv[$tv])) {
$res = $modx->db->select("elements", $modx->getFullTableName('site_tmplvars'), "name='$tv'");
$res = $modx->db->getValue($res);
$res = explode('||', $res);
foreach ($res as $r) {
    $r = explode('==', $r);
    // $modx->cache->tvElements[$tv][$r[1]] = $r[0];
    $data[$r[1]] = $r[0];
}
// }

$html = '';
$index = 0;
foreach ($values as $v) {
    $index++;
    if ($index > 1 && ($index - 1) == $display)
        break;
    $rt = $rowTpl;
    $rt = str_replace('[+num+]', $index, $rt);
    $rt = str_replace('[+value+]', $v, $rt);
    // $rt = str_replace('[+name+]', $modx->cache->tvElements[$tv][$v], $rt);
    $rt = str_replace('[+name+]', $data[$v], $rt);
    if ($show !== false && $index > $show)
        $rt = str_replace('[+hidden+]', 'hidden', $rt);
    $html .= $rt;
}
return $html;
