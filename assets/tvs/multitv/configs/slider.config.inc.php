<?php
$settings['display'] = 'vertical';
$settings['fields'] = array(
    'image' => array(
        'caption' => 'Картинка',
        'type' => 'image'
    ),
    'thumb' => array(
        'caption' => 'Thumbnail',
        'type' => 'thumb',
        'thumbof' => 'image'
    ),

    'title' => array(
        'caption' => 'Название',
        'type' => 'text'
    ),
    'legend' => array(
        'caption' => 'Описание',
        'type' => 'textareamini'
    ),
    'author' => array(
        'caption' => 'Туров',
        'type' => 'text'
    ),
    'link' => array(
        'caption' => 'Ссылка<br>(id страницы)',
        'type' => 'text'
    )
);
/*$settings['templates'] = array(
    'outerTpl' => '<div class="images">[+wrapper+]</div>',
    'rowTpl' => '<div class="image"><div class="copyrightwrapper"><img src="[+image+]" alt="[+legend+]" title="[+title+]" />[+author:ne=``:then=`<p class="copyright">[+author+]</p>`+]</div>[+legend:ne=``:then=`<p class="legend">[+legend:nl2br+]</p>`+]</div>'
);
*/
$settings['prepare'] = function($data, $modx, $_multiTV) {
	$data['tag'] = 'div';
	$data['href'] = '';
    if (!empty($data['link'])) {
		$data['tag'] = 'a';
		
		if (is_numeric($data['link'])) {
            $data['link'] = $modx->makeUrl($data['link']);
        } else if (substr($data['link'], 0, 1) === '#') {
            $data['link'] = $modx->makeUrl($modx->documentObject['id']) . $data['link'];
        }
		$data['href'] = 'href="' . $data['link'] . '"';

    }

   return $data;
};


$settings['templates'] = array(
    'outerTpl' => '<div class="images">[+wrapper+]</div>',
    'rowTpl' => '<li>
    <((tag)) ((href)) class="main-slider__item">
		<img class="main-slider__img" data-lazy="[[phpthumb? &input=`((image))` &options=`w=1170,h=370,q=85,zc=1`]]" alt="((title))">
		<div class="main-slider__caption">
			<div class="main-slider__title">
				[[if? &is=`((author)):!empty` &then=`
				<div class="main-slider__subtitle">((author))</div> 
				`]]
				((title))
			</div>
			[[if? &is=`((legend)):!empty` &then=`
			<div class="main-slider__desc">((legend))</div> 
			`]]
		</div>
	</((tag))>
	</li>'
);
