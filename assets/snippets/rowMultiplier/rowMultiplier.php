<?php
$num = intval($num);
$limit = intval($limit);
$declensionArray = explode(',', $declension);
$html = '';
if ($num > 0 && !empty($tpl)) {
    $start = 1;
    while ($start <= $num) {
        $row = $tpl;
        $row = str_replace('{num}', $start, $row);
        if (!empty($declensionArray)) {
            if (count($declensionArray) == 3) {
                $d = $modx->runSnippet('declension', array(
                    'num' => $start,
                    'words' => $declension
                ));
                $row = str_replace('{declension}', $d, $row);
            } else {
                $row = str_replace('{declension}', '', $row);
            }
        }

        $html .= $row;
        if (isset($limit) && $limit == $start)
            break;
        $start++;
    }
}
return $html;
