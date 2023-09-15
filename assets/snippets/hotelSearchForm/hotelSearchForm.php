<?php
global $modx;
require_once MODX_BASE_PATH . '/assets/helpers/hotelHelper.php';
require_once MODX_BASE_PATH . '/assets/helpers/htmlFormHelper.php';

$categoriesData = HotelHelper::getHotelTypesDecorated();
?>
<div class="mb-4">
    <form class="b-search js-hotel-search-form" action="<?= (isset($hotelSearchFormUrl) && !empty($hotelSearchFormUrl)) ? $hotelSearchFormUrl : ''; ?>" method="get">
        <div class="js-search-form --bg-base">
            <div class="row">
                <?php
                echo HtmlFormHelper::drawSelectList('Страна', 'country', HotelHelper::getCountriesDecorated(), 'col-12 col-sm-6 col-xl my-1 hotelSearch-geo');
                echo HtmlFormHelper::drawSelectList('Регион', 'region', HotelHelper::getRegionsDecorated(), 'col-12 col-sm-6 col-xl my-1 hotelSearch-geo');
                echo HtmlFormHelper::drawSelectList('Курорт', 'resort', HotelHelper::getResortsDecorated(), 'col-12 col-sm-6 col-xl my-1 hotelSearch-geo');
                echo HtmlFormHelper::drawSelectList('Город', 'city', HotelHelper::getCitiesDecorated(), 'col-12 col-sm-6 col-xl my-1 hotelSearch-geo');
                echo HtmlFormHelper::drawSelectList('Звёздность', 'stars', HotelHelper::getStarsDecorated(), 'col-12 col-sm-6 col-xl my-1');

                if (count($categoriesData)) {
                    foreach ($categoriesData as $categoryData) {
                        if (!empty($categoryData['children'])) {
                            echo HtmlFormHelper::drawMultySelectList($categoryData['pagetitle'], 'filter', $categoryData['children'], 'col-12 col-sm-6' . ((count($categoriesData) < 4) ? ' col-xl' : ' col-xl') . ' my-1');
                        }
                    }
                }
                echo HtmlFormHelper::drawSearchButton('Найти', 'hotels', 'col-auto ml-auto');
                ?>
            </div>
        </div>
        <div class="b-search-word --bg-baseDarken" method="get">
            <div class="row">
                <label class="b-search-word-label col-12 col-md-auto --color-font-inverse">Поиск по названию отеля</label>
                <div class="b-search-word-input-wr col-12 col-md">
                    <div class="position-relative">
                        <input type="text" name="text" placeholder="Поиск по названию отеля" class="b-search-word-input" value="<?= $_GET['text'] ?>" />
                        <button type="submit" class="b-search-word-submit"></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
$js = <<<'JS'
<script>   
let geoSelects = document.querySelectorAll('.js-hotel-search-form .hotelSearch-geo select');
if (geoSelects.length) {
    geoSelects.forEach(function(select) {
        select.addEventListener('change', hotelSearch_updateGeoControls_onChange);
    });
}           

function hotelSearch_updateGeoControls_onChange(event) {
    let option = event.target.options[event.target.selectedIndex]
    hotelSearch_updateGeoControls_children(option)
}

var hotelSearch_queryGeo = {}
function hotelSearch_updateGeoControls_byQuery() {
  	let urlParams = new URLSearchParams(window.location.search)
	hotelSearch_queryGeo = {
		"country": urlParams.get('country'),
		"region": urlParams.get('region'),
		"resort": urlParams.get('resort'),
		"city": urlParams.get('city')
	}
	hotelSearch_setGeoControls(hotelSearch_queryGeo)
}
function hotelSearch_setGeoControls(geoParams) {
	let option = false
	if (geoParams.city*1 > 0) {
		option = document.querySelector(`.hotelSearch-geo [name="city"] [value="${geoParams.city}"]`)
	} else if (geoParams.resort*1 > 0) {
		option = document.querySelector(`.hotelSearch-geo [name="resort"] [value="${geoParams.resort}"]`)
	} else if (geoParams.region*1 > 0) {
		option = document.querySelector(`.hotelSearch-geo [name="region"] [value="${geoParams.region}"]`)
	} else if (geoParams.country*1 > 0) {
		option = document.querySelector(`.hotelSearch-geo [name="country"] [value="${geoParams.country}"]`)
	}
	console.log("option", option)
	if (option)
		hotelSearch_updateGeoControls_children(option)
}
hotelSearch_updateGeoControls_byQuery()

function hotelSearch_updateGeoControls_parents(option) {
    for (var key in option.dataset) {
        if (key.includes('_id')) {
            let select = document.querySelector('.js-hotel-search-form .hotelSearch-geo select[name="' + key.replace('_id', '') + '"]')
            if (!select) continue
            select.value = 0
            if (select.querySelector('option[value="' + option.dataset[key] + '"]'))
                select.value = option.dataset[key]
        }
    }
}

function hotelSearch_updateGeoControls_children(parentOption) {
    let geoType = parentOption.parentNode.name
    document.querySelectorAll('.js-hotel-search-form .hotelSearch-geo option').forEach(function(option) {
        option.style.display = 'block'
        let attr = geoType+'_id'
        if (
            parentOption.value != 0 
            && (option.dataset[attr] || option.dataset[attr] == '')
        ) {
            option.style.display = 'none'
            if (option.dataset[attr] == parentOption.value)
                option.style.display = 'block'
        }
        if (
            option.selected == true 
            && (typeof option.dataset[attr] != 'undefined' && option.dataset[attr] != parentOption.value)
        ) {
            option.parentNode.value = 0;
        }
    })
}
</script>
JS;
$modx->regClientScript($js, true);
return;
