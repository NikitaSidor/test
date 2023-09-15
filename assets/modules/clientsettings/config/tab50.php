<?php

$code = "
    <div id=\"telegramApiContainer\">
        <style>
            #telegramApiContainer span.link {
                text-decoration: none;
                color: #3481bc;
                cursor: pointer;
                font-weight: bold;
                font-size: 14px;
            }
            #telegramApiContainer span.link:hover {
                text-decoration: underline;
                color: #014c8c;
                text-decoration-style: dashed;
            }
            #telegramApiContainer .accordion {
                border: 1px dotted rgba(0,0,0,.1);
                border-radius: 3px;
                background-color: #fafafa;
                padding: 10px 0;
                color: #333333;
            }
            #telegramApiContainer .accordion ol {
                padding: 0 0 0 30px;
                margin: 0;
            }
            #telegramApiContainer .accordion ol,
            #telegramApiContainer .accordion ol li {
                list-style: decimal;
            }
            #telegramApiContainer .accordion ol li em {
                text-decoration: underline;
                text-decoration-style: dotted;
            }
        </style>
        <script>
            jQuery(document).ready(function () {
                const domain = location.protocol + '//' + location.host;
    
                var link = 'https://api.telegram.org/bot[token]/setWebhook?url=[domain]/tgbot/handler.php';
                var token = '';
                var src = '';
    
                tvtelegramApiRegisterWebHookSrcUpdate(jQuery(document).find('input[name=tvtelegramApi]'));
    
                jQuery(document).find('input[name=tvtelegramApi]').on('keydown', function () {
                    tvtelegramApiRegisterWebHookSrcUpdate(jQuery(this));
                });
    
                function tvtelegramApiRegisterWebHookSrcUpdate(object) {
                    token = object.val();

                    var src = link.replace('[token]', token).replace('[domain]', domain);

                    jQuery(document).find('#telegramApiContainer a#tvtelegramApiRegisterWebHook').attr('href', src);
                };

                jQuery(document).find('#telegramApiContainer .link').on('click', function() {
                    jQuery(document).find('#telegramApiContainer .accordion').slideToggle('fast');
                });
            });
        </script>
        <br />
        <a href=\"/\" target=\"_blank\" id=\"tvtelegramApiRegisterWebHook\" class=\"btn btn-primary\">Зарегистрировать Web-Hook <i class=\"fa fa-angle-right\"></i></a>
        <br />
        <br />
        <p><span class=\"link\">Инструкция для регистрации Телеграм-бота</span></p>
        <div class=\"accordion\" style=\"display: none;\">
            <ol>
                <li>Перейти по ссылке <a href=\"https://telegram.me/BotFather\" target=\"_blank\">BotFather</a> и в открывшемся чате написать боту комунду <em>/start</em>, если ранее этого не делалось</li>
                <li>Написать боту команду <em>/newbot</em> для начала создания нового бота</li>
                <li>Следуя вопросам бота, указать необходимые данные для бота</li>
                <li>В результате будет выдана ссылка на бота и его API-ключ. API-ключ необходимо ввести в текущее поле и сохранить его, а по ссылке на Телеграм-бота стоит перейти и приготовиться к работе с его командами</li>
                <li>После ввода API-ключа в это поле, необходимо зарегистрировать web-hook, нажав на кнопку <strong>\"Зарегистрировать Web-Hook\"</strong>.</li>
                <li>Начав диалог с ботом, необходимо следовать инструкциям, которые он выдаёт в ответ на запросы. В процессе, необходимо будет подписаться на получение заявок с сайта в ТГ.</li>
            </ol>
        </div>
    </div>
";

return [
    'caption' => 'Интеграции',
    'introtext' => '',
    'settings' => [
        'telegramApi' => [
            'caption' => 'Telegram API-token',
            'type'  => 'text',
            'note'  => $code,
            'default_text' => '',
        ],
    ],
];

