<?php

namespace mmaurice\tgbot\app;

use \Exception;
use \mmaurice\tgbot\Application;

// Для скрытия нотификаций SleekDB
error_reporting(E_ALL & ~E_USER_DEPRECATED);

require_once realpath(dirname(__FILE__) . '/vendor/autoload.php');

$_SERVER['SCRIPT_FILENAME'] = realpath(__FILE__);

try {
    $config = include_once realpath(dirname(__FILE__) . '/configs/config.php');

    $application = new Application($config, Application::MODE_SENDER);

    // Делаем выборку подписчиков
    $recipientsStore = Application::$di->get('store')->init('recipients');

    $recipients = $recipientsStore->findAll();

    if (is_array($recipients) and !empty($recipients)) {
        // Отправляем рассылку
        foreach ($recipients as $recipient) {
            $application::sendMessage($recipient['recipient'], 'Какое-то сообщение для тестирования рассылки');
        }
    }
} catch (\Exception $exception) {
    $application->error($exception);
}
