<?php
$settings['display'] = 'vertical';
$settings['fields'] = array(
    'video' => array(
        'caption' => 'Ссылка youtube',
        'type' => 'text'
    ),
    'title' => array(
        'caption' => 'Название',
        'type' => 'text'
    )    
    
);
$settings['templates'] = array(
    'outerTpl' => '[+wrapper+]',
    'rowTpl' => '<div class="partners__item"><div class="partners__figure"><img src="[[phpthumb? &input=`[+image+]` &options=`h=55,q=84`]]" alt="[+title+]" class="partners__img"></div></div>'
);

$settings['configuration'] = array(
    'enablePaste' => false,
    'enableClear' => false
);

