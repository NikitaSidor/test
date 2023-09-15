<?php
global $tmplvars;

$e = &$modx->Event;
$table = $modx->getFullTableName("filters_to_content");

if ($e->name == "OnDocFormSave") {
	$modx->db->query("
   		DELETE FROM $table WHERE contentid = $id;
	");

	$tvValue = explode('||', $tmplvars[36][1]);
	$values = [];
	foreach ($tvValue as $tvv)
	{
		if (!empty($tvv))
			$values[] = "($id,$tvv)";
	}
	if (!empty($values))
	{
		$values = implode(',',$values);
		$modx->db->query("
			INSERT INTO $table
			(contentid,filterid)
			VALUES
			$values
		");

	}
}