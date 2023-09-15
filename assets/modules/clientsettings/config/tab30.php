<?php

return [
    'caption' => 'Оформление',
    'introtext' => 'Оформление',
    'settings' => [
        'logo' => [
            'caption' => 'Лого',
            'type'  => 'image',
        ],
        'title' => [
            'caption' => 'Надпись',
            'type'  => 'text',
            'note'  => '',
            'default_text' => '',
        ],
        'subtitle' => [
            'caption' => 'Подпись',
            'type'  => 'text',
            'note'  => '',
            'default_text' => '',
        ],
        'formbg' => [
            'caption' => 'Фон формы быстрого заказа',
            'type'  => 'image',
        ],
        'clients' => [
            'caption' => 'Довольных туристов',
            'type'  => 'text',
            'note'  => 'для главной',
            'default_text' => '1175',
        ],
        'theme' => [
            'caption' => 'Цветовая схема',
            'type'  => 'dropdown',
            'elements' => 'Море (blue-green)==sea||Графит (grey-green)==dark_matter||Пудинг(red-maroon)==pudding||Конфетка (pinky) ==pinky_girly||Брокколи (green-yellow)==broccoli||Мечта (blue-yellow)==dream||Волна (green-greensea)==toad||Водоросли (black-green)==algae||Смокинг (grey-blue)==tuxedo||Коктейль (blue-red)==cocktail',
            'default_text' => 'sea', 
        ],
        'cookie-notification' => [
            'caption' => 'Уведомление о файлах Cookie',
            'type'  => 'checkbox',
            'elements' => 'Да==1',
        ],
       
    ],
];
