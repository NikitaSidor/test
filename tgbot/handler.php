<?php

// install web-hook:
// https://api.telegram.org/bot[token]/setWebhook?url=[handler_path]

namespace mmaurice\tgbot\app;

use \Exception;
use \mmaurice\tgbot\Application;

// Для скрытия нотификаций SleekDB
error_reporting(E_ALL & ~E_USER_DEPRECATED);

require_once realpath(dirname(__FILE__) . '/vendor/autoload.php');

$_SERVER['SCRIPT_FILENAME'] = realpath(__FILE__);

try {
    $config = include_once realpath(dirname(__FILE__) . '/configs/config.php');

    $application = new Application($config);

    $application->importSource(__NAMESPACE__);

    $application->run();
} catch (\Exception $exception) {
    $application->error($exception);
}
