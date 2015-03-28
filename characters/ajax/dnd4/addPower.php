<?
	require_once(FILEROOT.'/includes/packages/dnd4Character.package.php');
	if (in_array($_POST['type'], array('atwill', 'encounter', 'daily'))) 
		dnd4Character::powerEditFormat($_POST['type']);
?>
