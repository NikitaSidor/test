<?php

define('MODX_API_MODE', true);

include_once realpath(dirname(__FILE__) . "/../../index.php");

global $modx;

$modx->db->connect();

if (empty($modx->config)) {
 	$modx->getSettings();
}

$token = $modx->getConfig('client_telegramApi');

/**
 * Инициализация базовых констант
 */
define('TOKEN', $token);

/**
 * Массив key => value
 */
return [
    // markdown, html
    'parserMode' => 'markdown',
];
