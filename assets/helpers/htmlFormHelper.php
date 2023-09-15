<?php

require_once 'assets/helpers/searchHelper.php';

class HtmlFormHelper
{
    public static function drawSelectList($title, $name, array $values = [], $class = '')
    {
        return "<div class=\"search__field {$class}\">"
            . "<div class=\"search__label\">{$title}</div>"
            . "<select name=\"{$name}\" class=\"search__select\">"
            .  SearchHelper::decorateOptions($values, $name)
            . "</select>"
            . "</div>";
    }

    public static function drawMultySelectList($title, $name, array $values = [], $class = '')
    {
        return "<div class=\"search__field {$class}\">"
            . "<div class=\"search__label\">{$title}</div>"
            . "<div class=\"search__group search__choise-link js-choiseToggle\" data-label=\"Не выбрано\">Не выбрано</div>"
            . "<div class=\"checkbox-list-wr\">"
            . "<ul class=\"checkbox-list\">"
            . SearchHelper::decorateCheckbox($values, $name)
            . "</ul>"
            . "<div class=\"checkbox-list__close\"></div>"
            . "</div>"
            . "</div>";
    }

    public static function drawSearchButton($title, $name, $class = '')
    {
        return "<div class=\"search__field1 d-flex align-items-end {$class}\">"
            . "<div class=\"search__group\">"
            . "<input type=\"hidden\" value=\"1\" name=\"run\" />"
            . "<input type=\"hidden\" value=\"{$name}\" name=\"searchType\" />"
            . "<input type=\"submit\" class=\"search__submit\" value=\"{$title}\" />"
            . "</div>"
            . "</div>";
    }

    public static function drawSearchLine($title, $name, $value, $class = '')
    {
        return "<div class=\"{$class}\">"
            . "<input type=\"text\" name=\"{$name}\" placeholder=\"{$title}\" class=\"b-search-word-input\" value=\"{$value}\" />"
            . "<button type=\"submit\" class=\"b-search-word-submit\"></button>"
            . "</div>";
    }

    public static function drawDateRange($title, $name, $value, $class = '')
    {
        return "<div class=\"search__field searchItemDuration search__field_duration search__field {$class}\">"
            . "<div class=\"search__label\">{$title}</div>"
            . "<input name=\"{$name}\" class=\"search__input search__date js-search__date\" value=\"{$value}\" />"
            . "</div>";
    }

    public static function drawPriceRange($title, array $names, array $values = [], $class = '', array $itemClasses = [])
    {
        return "<div class=\"search__field searchItemDuration search__field_duration {$class}\">"
            . "<div class=\"search__label\">{$title}</div>"
            . "<div class=\"search__group\">"
            . (!empty($names) ? implode('', array_map(function ($name, $placeholder) use ($values, $itemClasses) {
                return "<div class=\"search__group-item\">"
                    . "<input name=\"{$name}\" class=\"search__input search__price search__price--" . (array_key_exists($name, $itemClasses) ? $itemClasses[$name] : '') . "\" value=\"" . (array_key_exists($name, $values) ? $values[$name] : '') . "\" placeholder=\"{$placeholder}\">"
                    . "</div>";
            }, array_keys($names), array_values($names))) : '')
            . "</div>"
            . "</div>";
    }
}
