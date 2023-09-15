<?php

require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

class SearchHelper
{
    const ATTRIBUTES_ROOT = 182;

    public static function declOfNum($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);

        $format = $titles[((($number % 100) > 4) and (($number % 100) < 20)) ? 2 : $cases[min(($number % 10), 5)]];

        return sprintf($format, $number);
    }

    protected static function matchSelectedFromGet($get, $dest, $pattern = 'checked')
    {
        $match = (array_key_exists($get, $_GET) ? $_GET[$get] : 0);

        return ($match == $dest ? " {$pattern}=\"{$pattern}\"" : self::matchSelectedFromCurrentDoc($dest, $pattern));
    }

    protected static function matchSelectedFromGetArray($get, $dest, $pattern = 'checked')
    {
        $match = (is_array($_GET[$get]) ? $_GET[$get] : []);

        return (in_array($dest, $match) ? " {$pattern}=\"{$pattern}\"" : self::matchSelectedFromCurrentDoc($dest, $pattern));
    }

    protected static function matchSelectedFromCurrentDoc($value, $pattern = 'checked', $params = [])
    {
        global $modx;
        $pattern = " {$pattern}=\"{$pattern}\"";

        if ($modx->documentObject['id'] == $value)
            return $pattern;

        return '';
    }

    protected static function selectBySql($sql)
    {
        global $modx;

        $results = [];

        $resource = $modx->db->query($sql);

        while ($row = $modx->db->getRow($resource)) {
            $results[(array_key_exists('id', $row) ? intval($row['id']) : count($results))] = $row['value'];
        }

        return $results;
    }

    protected static function selectRowBySql($sql)
    {
        global $modx;

        $results = [];

        $resource = $modx->db->query($sql);

        while ($row = $modx->db->getRow($resource)) {
            $results[(array_key_exists('id', $row) ? intval($row['id']) : count($results))] = $row;
        }

        return $results;
    }

    public static function decorateOptions(array $array, $selectedFromAttr = null)
    {
        $html = '';
        if (is_array($array) and !empty($array)) {
            array_filter($array, function ($value, $id) use (&$html, $selectedFromAttr) {
                $dataAttributes = '';
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        if ($k == 'id' || $k == 'value')
                            continue;
                        $dataAttributes .= "data-$k=\"$v\"";
                    }
                    $value = $value['value'];
                }

                $html .= "<option $dataAttributes value=\"{$id}\"" . (!is_null($selectedFromAttr) ? self::matchSelectedFromGet($selectedFromAttr, $id, 'selected') : '') . ">{$value}</option>" . PHP_EOL;

                return true;
            }, ARRAY_FILTER_USE_BOTH);
        }

        return $html;
    }

    public static function decorateCheckbox(array $array, $selectedFromAttr = null)
    {
        $html = '';

        if (is_array($array) and !empty($array)) {
            array_filter($array, function ($value, $key) use (&$html, $selectedFromAttr) {
                $html .= "<li class=\"checkbox-list__item\">"
                    . "<label class=\"checkbox-list__title\">"
                    . "<input type=\"checkbox\" name=\"" . (!is_null($selectedFromAttr) ? "{$selectedFromAttr}[]" : '') . "\" value=\"{$key}\" class=\"checkbox-list__checkbox\"" . (!is_null($selectedFromAttr) ? self::matchSelectedFromGetArray($selectedFromAttr, $key) : '') . '/>'
                    . $value
                    . "</label>"
                    . "</li>";

                return true;
            }, ARRAY_FILTER_USE_BOTH);
        }

        return $html;
    }
}
