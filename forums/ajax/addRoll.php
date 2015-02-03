<?
	if (intval($_POST['count']) >= 0) {
		$rollObj = new stdObject();
		$rollObj->type = isset($_POST['type'])?$_POST['type']:'basic';
		rollTR($_POST['count'], $rollObj);
	}
?>