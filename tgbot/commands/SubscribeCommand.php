<?php

namespace mmaurice\tgbot\app\commands;

use \mmaurice\tgbot\Application;
use \mmaurice\tgbot\core\interfaces\Command;
use \TelegramBot\Api\Client;
use \TelegramBot\Api\Types\Message;

class SubscribeCommand extends Command
{
    static public $description = 'Подписаться на получение заявок в Телеграм';
    static public $alias = 'Подписка';

    protected $recipientsStore;

    public function __construct(Client &$client, Message &$message)
    {
        parent::__construct($client, $message);

        $this->recipientsStore = Application::$di->get('store')->init('recipients');
    }

    public function keyboard($keyboard = [])
    {
        return parent::keyboard($this->commandsAliases(self::TYPE_SYSTEM));
    }

    public function execute($message = '')
    {
        return $this->render('subscribe', [
            'message' => $message,
            'status' => $this->apply(),
        ]);
    }

    public function apply()
    {
        $recipients = $this->recipientsStore->findBy(['recipient', '=', $this->message->getChat()->getId()]);

        if (empty($recipients)) {
            $this->recipientsStore->insert([
                'recipient' => $this->message->getChat()->getId(),
            ]);

            $recipients = $this->recipientsStore->findBy(['recipient', '=', $this->message->getChat()->getId()]);
        } else {
            return 'Вы уже подписаны!';
        }

        if (!empty($recipients)) {
            return 'Вы успешно подписались!';
        }

        return 'Произошла какая-то ошибка!';
    }
}
