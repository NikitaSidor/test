<?php

namespace mmaurice\tgbot\app\commands;

use \mmaurice\tgbot\Application;
use \mmaurice\tgbot\app\commands\SubscribeCommand;
use \TelegramBot\Api\Client;
use \TelegramBot\Api\Types\Message;

class UnsubscribeCommand extends SubscribeCommand
{
    static public $description = 'Отписаться от получение заявок в Телеграм';
    static public $alias = 'Отписка';

    public function keyboard($keyboard = [])
    {
        return parent::keyboard($this->commandsAliases(self::TYPE_SYSTEM));
    }

    public function execute($message = '')
    {
        return $this->render('unsubscribe', [
            'message' => $message,
            'status' => $this->apply(),
        ]);
    }

    public function apply()
    {
        $recipients = $this->recipientsStore->findBy(['recipient', '=', $this->message->getChat()->getId()]);

        if (!empty($recipients)) {
            $this->recipientsStore->deleteBy([
                'recipient', '=', $this->message->getChat()->getId(),
            ]);

            $recipients = $this->recipientsStore->findBy(['recipient', '=', $this->message->getChat()->getId()]);
        } else {
            return 'Вы не подписаны!';
        }

        if (empty($recipients)) {
            return 'Вы успешно отписались!';
        }

        return 'Произошла какая-то ошибка!';
    }
}
