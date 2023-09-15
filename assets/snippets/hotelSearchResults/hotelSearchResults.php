<?php
require_once realpath(MODX_BASE_PATH . '/assets/snippets/excursionSearch/excursionSearch.class.php');

// Запущен поиск?
if ((array_key_exists('run', $_GET) && $_GET['searchType'] == 'hotels') or (isset($run) and $run)) {

    $tpl = <<<HTML
<div class="hotelCatalogCard col-12 col-sm-6 col-md-3 mb-2 p10">
  <a href="/[~[+id+]~]" class="text-decoration-none">
    <div class="img">
      <div class="info">
        <div class="star-rating">
        [[rowMultiplier? &num=`[+tv_hotelStars+]` &tpl=`*` ]]
        </div>
        <div class="resort">[+resort+]</div>
      </div>
      <img
      src="/[[phpthumb? &input=`[[getImgFromGallery? &id=`[+id+]`]]` &options=`w=308,h=200,zc=C`]]"
      alt="[+pagetitle+]"
      />
      <div class="header cf">
        <div>[+pagetitle+]</div>
        <div class="rating">[+tv_rating+]</div>
      </div>
    </div>
    [[if &is=`[+tv_price+]:notempty` &then=`
    <div class="hotelCatalogCard__price-from">
      <div>
        <small>от</small>
        <span>[+tv_price+]</span> 
        [+tv_priceLabel+]<small>/день</small>
      </div>
    </div>
    `]]
  </a>
</div>
HTML;

    $country = (isset($country) ? [$country] : (isset($_GET['country']) ? array_filter(!is_array($_GET['country']) ? [$_GET['country']] : $_GET['country']) : null));
    $city = (isset($city) ? [$city] : (isset($_GET['city']) ? array_filter(!is_array($_GET['city']) ? [$_GET['city']] : $_GET['city']) : null));
    $filter = (isset($filter) ? [$filter] : (isset($_GET['filter']) ? array_filter(!is_array($_GET['filter']) ? [$_GET['filter']] : $_GET['filter']) : null));
    $stars = (isset($stars) ? [$stars] : (isset($_GET['stars']) ? array_filter(!is_array($_GET['stars']) ? [$_GET['stars']] : $_GET['stars']) : null));
    $text = (isset($text) ? [$text] : (isset($_GET['text']) ? $_GET['text'] : null));

    $hotelsData = excursionSearch::getHotels(array_filter([
        'countryIds' => $country,
        'cityIds' => $city,
        'filterIds' => $filter,
        'stars' => $stars,
        'name' => $text,
    ]));

    echo "<div class=\"b-hotels\"><div class=\"hotels__list row\">";
	// getHotels
    $hotels = $modx->runSnippet('DocLister', [
		'api'=>1,
        'id' => 'hotels',
        'tvList' => implode(',', [
            'hotelStars',
            'rating',
            'price',
            'priceLabel',
            'filter',
        ]),
        'idType' => 'documents',
        'documents' => (!empty($hotelsData) ? implode(",", array_values($hotelsData)) : null),
        'showParent' => 0,
        'debug' => $_GET['d'],
        'tpl' => "@CODE:$tpl",
        'orderBy' => (!empty($hotelsData) ? "FIELD(c.id, " . implode(",", array_keys($hotelsData)) . ") ASC" : null),
        'display' => '24',
        'paginate' => '1',
        'showPublishedOnly' => '1',
        'noneTPL' => "@CODE:<div class=\"col-12 search__nofound\">Отели не найдены</div>",
        'TplNextP' => "@CODE: <li class=\"pagination__item pagination__next\"><a class=\"pagination__link\" href=\"[+link+]\">&rarr;</a></li>",
        'TplPrevP' => "@CODE: <li class=\"pagination__item pagination__prev\"><a class=\"pagination__link\" href=\"[+link+]\">&larr;</a></li>",
        'TplPage' => "@CODE: <li class=\"pagination__item\"><a class=\"pagination__link\" href=\"[+link+]\">[+num+]</a></li>",
        'TplCurrentPage' => "@CODE: <li class=\"pagination__item active\"><span class=\"pagination__link active\" href=\"[+link+]\">[+num+]</span></li>",
        'TplWrapPaginate' => "@CODE: <div class=\"pagination\"><ul>[+wrap+]</ul></div>",
    ]);
	$hotels = json_decode($hotels,true);
	/*
	echo '<pre>';
	print_r($hotels);
	echo '</pre>';
	*/
	
	// getHotelGeography
	$ids = [];
	foreach ($hotels as $hotel) {
		$ids[] = $hotel['id'];
	}
	if (!empty($ids)) {
		$result = $modx->db->query("
			SELECT sc.id as hotel_id, r.id, r.pagetitle, r.template
			FROM {$modx->getFullTableName('site_content')} sc
			LEFT JOIN {$modx->getFullTableName('site_content')} r ON sc.parent = r.id
			WHERE sc.id IN (".implode(',',$ids).")
		");   
		$resortsToHotel = [];
		while( $row = $modx->db->getRow( $result ) ) {  
			$resortsToHotel[$row['hotel_id']] = $row;
		}
		foreach ($hotels as $key=>$hotel) {
			$hotels[$key]['resort'] = $resortsToHotel[$hotel['id']]['pagetitle'];
		}
		
		foreach ($hotels as $hotel) {
			echo $modx->parseText($tpl, $hotel, '[+', '+]' );
		}
	} else {
		echo "<div class=\"col-12 search__nofound\">Отели не найдены</div>";
	}
	
	
	
    echo "</div>[+hotels.pages+]</div>";
}
return;
