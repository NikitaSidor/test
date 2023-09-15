<input type="hidden" name="tv<?=$field_id ?>" id="tv<?=$field_id ?>" value="<?=$field_value ?>">
<script>
    window.addEventListener('DOMContentLoaded', function() {
        console.info('7vHotelId : <?= (isset($field_value) && !empty($field_value)) ? $field_value : 'Не установлено' ?>');
        var tr = document.getElementById('tv<?=$field_id ?>').closest('tr');
        tr.style.display='none';
        tr.nextElementSibling.style.display='none';
    }, true);
</script>