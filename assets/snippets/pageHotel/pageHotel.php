<?php
if (isset($id)) {
    $hotelData = $modx->getTemplateVars([
        'pagetitle',
        'content',
        'address',
        'price',
        'hotelStars',
        'hotelReviews',
        'rating',
        'hotelType',
        'checkInTime',
        'checkOutTime',
        'checkOutNotes',
        'checkInPets',
    ], '*', $id);

    $hotel = [
        'id' => $id
    ];

    foreach ($hotelData as $tv) {
        if (isset($tv['type'])) {
            $hotel[$tv['name']][1] = $tv['value'];
        } else {
            $hotel[$tv['name']] = $tv['value'];
        }
    }
} else {
    $hotel = $modx->documentObject;
}
?>
<main class="b-main">
    <div class="wr">
        <div class="b-breadcrumbs">
            [[DLCrumbsMod? &showCurrent='1' &addWhereList=`c.alias_visible =1`]]
        </div>
        <section class="b-section">
            <div id="content">
                <div id='hotelB'>
                    <div class="hotel-top <?php
                                            if (empty($hotel['price'][1]) || $hotel['price'][1] < 1)
                                                echo ' no-price';
                                            ?>">
                        <div class="hotel-title">
                            <h1 class="h1"><?= $hotel['pagetitle'] ?>
                                <span class="star-rating">
                                    <?php
                                    echo $modx->runSnippet('rowMultiplier', [
                                        'num' => $hotel['hotelStars'][1],
                                        'tpl' => '<i class="icon-star"></i>'
                                    ]);
                                    ?>
                                </span>
                            </h1>
                        </div>
                        <?php if (isset($hotel['rating'][1]) && !empty($hotel['rating'][1])) : ?>
                            <div class="hotel-mark-block">
                                <div class="hotel-mark">
                                    <div class="review-mark">
                                        <div class="how"><?php
                                                            echo $modx->runSnippet('rating2word', [
                                                                'num' => $hotel['rating'][1],
                                                            ]);
                                                            ?></div>
                                        <?php
                                        if ($hotel['hotelReviews'][1] > 0) : ?>
                                            <div class="how-reviews">
                                                <span>
                                                    <?php
                                                    echo $hotel['hotelReviews'][1] . ' ';
                                                    echo $modx->runSnippet('declension', [
                                                        'num' => $hotel['hotelReviews'][1],
                                                        'words' => 'отзыв,отзыва,отзывов',
                                                    ]);
                                                    ?>
                                                </span>
                                            </div>
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="mark"><?= $hotel['rating'][1] ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <?php
                        if (
                            isset($hotel['address'][1]) && !empty($hotel['address'][1])
                            && isset($hotel['hotelType'][1]) && !empty($hotel['hotelType'][1])
                        )
                            $colClasses = 'col-12 col-md-6 ';
                        else
                            $colClasses = 'col-12 ';
                        if (isset($hotel['address'][1]) && !empty($hotel['address'][1]))
                            echo "<div class='$colClasses map-address'><span class='icon-map_pin'></span> {$hotel['address'][1]}</div>";
                        if (isset($hotel['hotelType'][1]) && !empty($hotel['hotelType'][1])) :
                            echo '<div class="$colClasses map-address"><span class="icon-house"></span> Тип отеля:';
                            echo $modx->runSnippet('DocLister', [
                                'documents' => $hotel['hotelType'][1],
                                'idType' => 'documents',
                                'id' => 'ht',
                                'tpl' => '@CODE:<span>[+title+], </span>',
                                'tplLast' => '@CODE:<span>[+title+] </span>'
                            ]);
                            echo '</div>';
                        endif;
                        ?>
                    </div>

                    <div class="hotel-gallery mx-auto">
                        <?php
                        echo $modx->runSnippet('FormSimpleGallery', [
                            'id' => $hotel['id'],
                            'rowTpl' => '@CODE:
						<div class="item">
							<a class="fancybox" data-fancybox="hotel-[+id+]" href="/[+sg_image+]">
								<img data-lazy="/[+thumb.sg_image+]" alt="[+images.image.title+][+sg_title+]">
							</a>
						</div>',
                            'thumbTpl' => '@CODE:
						<div class="item"><img data-lazy="/[+thumb.sg_image+]" alt="[+images.image.title+][+sg_title+]"></div>',
                            'phpthumb' => 'w=848,h=565,zc=C',
                            'phpthumbThumb' => 'w=125,h=84,zc=C'
                        ]);
                        ?>
                    </div>
                    <div class="hotel-gallery-mini mx-auto">[+SimpleGalleryThumbs+]</div>

                    <?php if ($hotel['content']) : ?>
                        <div class="hotel-descr">
                            <h2><span class="icon-house"></span>Описание</h2>
                            <?= $hotel['content'] ?>
                        </div>
                    <?php endif; ?>
                    <div class="hotel-info">
                        <div class="row">
                            <?php
                            if (
                                !empty($hotel['checkInTime'][1])
                                || !empty($hotel['checkOutTime'][1])
                                || !empty($hotel['checkOutNotes'][1])
                            ) :
                            ?>
                                <div class="col-md-6 hotel-conditions">
                                    <h2><span class="icon-people"></span>Условия размещения</h2>
                                    <div class="cond-table">
                                        <table class="table table-striped">
                                            <?php
                                            if (!empty($hotel['checkInTime'][1]))
                                                echo "
                                <tr>
                                    <td>
                                        <span>Время заезда </span>
                                        <span class='cond-time'>{$hotel['checkInTime'][1]}</span>
                                    </td>
                                </tr>
                                ";
                                            if (!empty($hotel['checkOutTime'][1]))
                                                echo "
                                <tr>
                                    <td>
                                        <span>Время отъезда </span>
                                        <span class='cond-time'>{$hotel['checkOutTime'][1]}</span>
                                    </td>
                                </tr>
                                ";
                                            if (!empty($hotel['checkOutNotes'][1])) {
                                                $checkOutNotes = $modx->runSnippet('nl2br', [
                                                    'data' => $hotel['checkOutNotes'][1],
                                                ]);
                                                echo "
                                <tr>
                                    <td>
                                        <div class='cond-hint'>$checkOutNotes</div>
                                    </td>
                                </tr>
                                ";
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <i class="<?php
                                                                if ($hotel['checkInPets'][1] == 1)
                                                                    echo "yes";
                                                                else
                                                                    echo "no";
                                                                ?>Bullet"></i>
                                                    Размещение домашних животных
                                                    <?php
                                                    if ($hotel['checkInPets'][1] != 1)
                                                        echo "НЕ&nbsp;";
                                                    echo "допускается.";
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="vw">
                                                    <div><img src="/template/i/vm.png" alt="" width='110' height='25'></div>
                                                    Для оплаты принимает карты
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php
                            endif;
                            $hotelFacilities = explode('||', $modx->documentObject['hotelFacilities'][1]);
                            $hotelFacilities = array_filter($hotelFacilities);
                            $hotelFacilitiesCount = is_array($hotelFacilities) ? count($hotelFacilities) : 0;
                            if ($hotelFacilitiesCount) :
                            ?>
                                <div class="col-md-6 hotel-comfort">
                                    <h2><span class="icon-star_1"></span>Самые популярные удобства</h2>

                                    <div class="comfort-table">
                                        <div class="row main-icons">
                                            <?php
                                            echo $modx->runSnippet('checkboxes2rows', [
                                                'tv' => 'hotelFacilities',
                                                'rowTpl' => '<div class="col-sm-4 comfort-item"><div class="comf-text">[+name+]</div></div>'
                                            ]);
                                            ?>
                                        </div>
                                        <?php
                                        if ($hotelFacilitiesCount > 12)
                                            echo '<div class="show-all"><button><span class="show-icons">Показать</span><span class="hide-icons">Скрыть</span>все</button></div>';
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                        // $modx->parseChunk('hotelOrderForm', [], '[+', '+]');
                        if (isset($hotel['address'][1]) && !empty($hotel['address'][1]) && $modx->documentObject['coords'][1]) : ?>
                            <div class="hotel-map my-5">
                                <h2><span class="icon-map_pin"></span>Отель на карте</h2>
                                <div class="map-address"><?= $hotel['address'][1] ?></div>
                                <div id="ymap"></div>
                            </div>
                        <?php
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            <?php
         
            ?>

            <div class="block block_lighted block_indent my-5">
                <div class="b-order-country">
                    <div class="block__header">Заявка на размещение в отеле</div>
                    <form method="post" action="/form/" class="b-form form_tour js-form-submit">
                        <input name="form" type="hidden" value="order-country">
                        <input type="text" name='name' class="dn" value="">
                        <input type="text" name='phone' class="dn form__protect" value="">
                        <input type="hidden" name='page_id' value="[*id*]">
                        <input type="hidden" name='pagetitle' value="[+country.name_kuda+]">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form__row clr">
                                    <label for="form-n" class="form__label">Ваше имя</label>
                                    <div class="form__field">
                                        <input type="text" name="n" id="form-n" value="" required class="form__input">
                                    </div>
                                </div>
                                <div class="form__row clr">
                                    <label for="form-t" class="form__label">Ваш телефон</label>
                                    <div class="form__field">
                                        <input type="text" name="t" id="form-t" value="" required class="phonemask form__input">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form__row clr">
                                    <label for="form-m" class="form__label">Пожелания</label>
                                    <div class="form__field">
                                        <textarea name="m" id="form-m" class="form__textarea"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form__row  clr ">
                            <div class="form__field">
                                <label class="form__label agreement__label">Подтверждаю согласие на <a href="" target="_blank">обработку персональных данных</a>
                                    <input type="checkbox" value="1" name="agr" class="form__checkbox agreement__checkbox" checked="checked" required>
                                </label>
                            </div>
                        </div>
                        <div class="form__row clr">
                            <div class="form__field">
                                <button type="submit" name="submit" class="btn form__submit form_tour_submit">Отправить</button>
                            </div>
                        </div>
                    </form>
                    <div class="form__success">Ваша заявка успешно отправлена.<br>Наши менеджеры свяжутся с Вами.</div>
                    <div class="form__error">Произошла ошибка,<br>попробуйте еще раз!</div>
                </div>
            </div>

            [*widgets*]

            <div class="block my-3">
                <div class="block__header">Поделиться в соцсетях</div>
                <div class="text-center">
                    <script src="https://yastatic.net/share2/share.js"></script>
                    <div class="ya-share2" data-curtain data-shape="round" data-limit="5" data-services="vkontakte,facebook,telegram,whatsapp,viber,messenger,moimir,twitter,skype"></div>
                </div>
            </div>
        </section>
    </div>
</main>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<?php
if ($modx->documentObject['coords'][1]) {
    $src = "<script>showYMap('{$modx->documentObject['coords'][1]}', 'ymap');</script>";
    $modx->regClientScript($src);
}
