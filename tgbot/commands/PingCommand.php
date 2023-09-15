<?php

namespace mmaurice\tgbot\app\commands;

use \mmaurice\tgbot\core\interfaces\Command;

class PingCommand extends Command
{
    static public $description = 'Ping Pong Test Command';

    public function execute($message = '')
    {
        return $this->render('ping', [
            'content' => 'Pong!',
            'message' => $message,
        ]);
    }
}
