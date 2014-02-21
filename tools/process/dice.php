<?
	addPackage('tools');
	$roll = RollFactory::getRoll($_POST['rollType']);
	$options = isset($_POST['options']) && is_array($_POST['options'])?$_POST['options']:array();
	$roll->newRoll($_POST['dice'], $options);
	$roll->roll();
	$roll->showHTML();
?>