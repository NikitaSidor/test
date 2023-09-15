<?php
$settings['display'] = 'horizontal';
$settings['fields'] = array(
    'param' => array(
        'caption' => 'Get-параметр',
        'type' => 'text',
        'default' => 'ya'
    ),
    'pvalue' => array(
        'caption' => 'Значение параметра',
        'type' => 'text'
    ),
    'value' => array(
        'caption' => 'Значение элемента лендинга',
        'type' => 'text'
    ),
);
$settings['templates'] = array(
    'outerTpl' => '<ul>[+wrapper+]</ul>',
    'rowTpl' => '<li>[+text+], [+image+], [+thumb+], [+textarea+], [+date+], [+dropdown+], [+listbox+], [+listbox-multiple+], [+checkbox+], [+option+]</li>'
);
$settings['configuration'] = array(
    'enablePaste' => true,
    'enableClear' => true,
    'csvseparator' => ','
);
