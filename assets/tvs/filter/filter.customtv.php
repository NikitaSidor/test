<?php
// Plugin Filters - OnDocFormSave

global $id;
global $modx;

$categories = $modx->getDocumentChildren(182, 1, 0, 'id,pagetitle', '', 'menuindex,pagetitle', 'ASC');
$res = $modx->db->query(
    "
    SELECT
        GROUP_CONCAT(filterid SEPARATOR '||') AS filterid
    FROM " . $modx->getFullTableName('filters_to_content') . "
    WHERE
        contentid = " . $id
);
$res = $modx->db->getRow($res);
$currentValues = explode('||', $res['filterid']);
$categoriesData = [];
echo "<div style=\"display:flex;flex-wrap:wrap;\">
<input type=\"hidden\" id=\"tv{$field_id}\" name=\"tv{$field_id}\" value=\"{$field_value}\">";

foreach ($categories as $c) :
    $document = $modx->getDocument($c['id'], 'id,pagetitle,isfolder,deleted,published', 1, 0);

    if ((intval($document['isfolder']) === 1) and (intval($document['deleted']) === 0)) :
        $children = $modx->getDocumentChildren($c['id'], 1, 0, 'id,pagetitle', '', 'menuindex,pagetitle', 'ASC');
?>
        <div style="flex: 1 1 33%; padding-right:15px; min-width:180px;">
            <h4><?= $c['pagetitle'] ?></h4>
            <div>
                <select class="inputBox tour-types-tv" onchange="documentDirty=true;" multiple="multiple" size="10">
                    <option value="0">Не указано</option>
                    <?php if (!empty($children)) : ?>
                        <?php foreach ($children as $child) : ?>
                            <option value="<?= $child['id']; ?>" <?= (in_array((int) $child['id'], $currentValues) ? ' selected="selected"' : ''); ?>>
                                <?= $child['pagetitle']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
<?php
    endif;
endforeach;
?>
</div>
<script>
    jQuery(document).ready(function() {
        jQuery('[name="tv<?= $field_id; ?>"]').attr('name', 'tv<?= $field_id; ?>[]')
        jQuery('.tour-types-tv').change(function() {
            let value = [];
            jQuery('.tour-types-tv').each(function() {
                if (jQuery(this).find('option:selected').val()) {
                    jQuery(this).find('option:selected').each(function() {
                        value.push(jQuery(this).val());
                    });
                }
            });
            value = value.join('||');
            jQuery('[name="tv<?= $field_id ?>[]"]').val(value);
        });
    });
</script>