<?php
$settings['display'] = 'vertical';
$settings['fields'] = array(
    'qwestion' => array(
        'caption' => 'Вопрос',
        'type' => 'text'
    ),
    'answer' => array(
        'caption' => 'Ответ',
        'type' => 'textarea'
    ),  

);
$settings['templates'] = array(
    'outerTpl' => '[+wrapper+]',
    'rowTpl' => ''
);
$settings['configuration'] = array(
    'enablePaste' => true,
    'enableClear' => true,
    'csvseparator' => ','
);
