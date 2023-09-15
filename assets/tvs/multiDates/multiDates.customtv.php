<!--<input type="hidden"  name='tv<?=$field_id?>' id='tv<?=$field_id?>' value='<?=$field_value?>'>-->
<input type="text" name='tv<?=$field_id?>' class="multidates" value="<?=$field_value?>" id='tv<?=$field_id?>' >

<script src="./../assets/tvs/multiDates/flatpickr.js"></script>
<link rel="stylesheet" href="./../assets/tvs/multiDates/flatpickr.min.css"/>
<script>
document.addEventListener('DOMContentLoaded', function(){
	flatpickr("#tv<?=$field_id?>", {
            locale: "ru",
            mode: "multiple",
            dateFormat: "d.m.Y",
            //defaultDate: ["2016-10-20", "2016-11-04"]
        });
});
</script>
