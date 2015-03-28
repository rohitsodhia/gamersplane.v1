<?
	require_once(FILEROOT.'/includes/packages/dnd4Character.package.php');
	if ($character = new dnd4Character($characterID)) 
		$character->attackEditFormat($_POST['attackNum']);
?>