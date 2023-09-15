<?php
$settings['display'] = 'vertical';
$settings['fields'] = array(
    'image' => array(
        'caption' => 'Фото',
        'type' => 'image'
    ),
    'thumb' => array(
        'caption' => 'Thumbnail',
        'type' => 'thumb',
        'thumbof' => 'image'
    ),
    'name' => array(
        'caption' => 'Имя',
        'type' => 'text'
    ),
    'post' => array(
        'caption' => 'Должность',
        'type' => 'text'
    ),
    'phone' => array(
        'caption' => 'Телефон',
        'type' => 'text'
    ),
   
    'text' => array(
        'caption' => 'Текст',
        'type' => 'textarea'
    ),


);

$settings['configuration'] = array(
    'enablePaste' => false,
    'enableClear' => false,
    'csvseparator' => ','
);


$settings['templates'] = array(
    'outerTpl' => '<div class="images">[+wrapper+]</div>',
    'rowTpl' => '<div class="image"><div class="copyrightwrapper"><img src="[+image+]" />[+author:ne=``:then=`<p class="copyright">[+author+]</p>`+]</div>[+legend:ne=``:then=`<p class="legend">[+legend:nl2br+]</p>`+]</div>'
);
