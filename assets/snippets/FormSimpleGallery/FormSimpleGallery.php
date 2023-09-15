<?php
/*
&id - документ для вывода
&rowTpl - Шаблон картинки. Понимает @CODE: и принимает имя чанка в качестве параметра.
&phpthumb - передаем опции phpthumb, в шаблоне используем плейсхолдер [+phpthumb+] как значение атрибута src
&display - кол-во слайдов
&thumbTpl - шаблон миниатюры, работает в связке с &thumbsContainer
&phpthumbThumb - параметры phpthumb для миниатюр, в шаблоне rowTpl и thumbTpl  использовать [+phpthumbThumb+]
&thumbsContainer - селектор jQuery куда перенести картинки
Поддерживает плейсхолдер [+FormPSSGalleryThumbs+] и [+FormPSSGalleryThumbs464+], где 464 - &id
Пример использования
[[FormPSSGallery 
&id=`1`
&singleTpl=`@CODE:
<img src="/[+phpthumb+]" alt="[+alt+]">
`
&singleTplThumbs=`true`
&rowTpl=`@CODE:
<div class="item">
    <a group="fancybox" rel="hotel" href="/[+image+]" data-thumb="/[+phpthumbThumb+]">
        <img src="/[+phpthumb+]" alt="[+alt+]">
    </a>
</div>
`
&thumbTpl=`@CODE:<div class="item"><img src="/[+phpthumbThumb+]" alt="[+alt+]"></div>`
&phpthumb=`w=848,h=565,zc=C`
&phpthumbThumb=`w=125,h=84,zc=C`
&thumbsContainer=`#hotel-gallery-mini`
]]							
*/
global $modx;
$id = (isset($id) ? $id : $modx->documentObject['id']);
$rowTpl = (isset($rowTpl) ? $rowTpl : '@CODE:<a href="/[+sg_image+]"  title="[+images.image.title+]" data-fancybox="gallery-' . $id . '" class="fancy"><img alt="[+e.sg_add+]"  title="[+e.sg_description+]" src="/[+thumb.sg_image+]"/></a>');
$singleTpl = isset($singleTpl) ? $singleTpl : $rowTpl;
$thumbTpl = (isset($thumbTpl) ? $thumbTpl : false);
$display = (isset($display) ? $display : 0);

$galleryElement = isset($galleryElement) ? $galleryElement : '.hotel-gallery';
$thumbsElement = isset($thumbsElement) ? $thumbsElement : '.hotel-gallery-mini';

if (!is_numeric($id) || empty($rowTpl))
    return false;

// Изображение одно?
$result = $modx->db->query('SELECT count(*) FROM ' . $modx->getFullTableName('sg_images') . ' WHERE sg_rid=' . $id);
$galleryCount = $modx->db->getValue($result);

if ($galleryCount == 1) {
    echo $modx->runSnippet('sgLister', array(
        'parents' => $id,
        'display' => $display,
        'idType' => 'parents',
        'thumbSnippet' => 'phpthumb',
        'thumbOptions' =>  $phpthumb,
        'tpl' => $singleTpl,
    ));
} else {
    // Выводим изображения
    echo $modx->runSnippet('sgLister', array(
        'parents' => $id,
        'display' => $display,
        'idType' => 'parents',
        'thumbSnippet' => 'phpthumb',
        'thumbOptions' =>  $phpthumb,
        'tpl' => $rowTpl,
    ));

    // Подготавливаем миниатюры
    if (!empty($thumbTpl)) {
        $thumbHtml = $modx->runSnippet('sgLister', array(
            'parents' => $id,
            'display' => $display,
            'idType' => 'parents',
            'thumbSnippet' => 'phpthumb',
            'thumbOptions' =>  $phpthumbThumb,
            'tpl' => $thumbTpl
        ));

        $modx->setPlaceholder('SimpleGalleryThumbs' . $id, $thumbHtml);
        $modx->setPlaceholder('SimpleGalleryThumbs', $thumbHtml);
        if (!empty($thumbsContainer)) {
            $src = "<script>$('$thumbsContainer').append('$thumbHtml');</script>";
            $modx->regClientScript($src);
        }
    }

    $js = <<<JS
<script>
if ($('{$thumbsElement}').children().length > 6)
{
    $('{$galleryElement}').slick({
        lazyLoad: 'ondemand',
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        fade: true,
        asNavFor: '{$thumbsElement}'
    });
    $('{$thumbsElement}').slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        asNavFor: '{$galleryElement}',
        dots: false,
        centerMode: false,
        infinite: true,
        focusOnSelect: true
    });
    $('{$thumbsElement} [data-lazy]').each(function(){
        $(this).attr('src',$(this).data('lazy'));
    });
}
else
{
    $('{$galleryElement}').slick({
        lazyLoad: 'ondemand',
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        fade: true,
    });
    $('{$thumbsElement}').addClass('d-flex').children().each(function(){
        $(this).addClass('p-1');
        let img = $(this).find('img');
        img.attr('src',img.data('lazy'));
        img.removeAttr('data-lazy');
    });
    $('{$thumbsElement}').children().click(function(e){
        $('{$galleryElement}').slick('slickGoTo', parseInt($(this).index()));
    });
}
</script>
JS;
    $modx->regClientScript('/template/js/slick.min.js');
    $modx->regClientScript($js);
}
