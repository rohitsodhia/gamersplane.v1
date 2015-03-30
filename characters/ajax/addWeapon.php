<?
	define('SYSTEM', $_POST['system']);
	if ($systems->verifySystem(SYSTEM)) {
		$charClass = $systems->systemClassName(SYSTEM).'Character';
		require_once(FILEROOT."/includes/packages/{$charClass}.package.php");
		if ($character = new $charClass($characterID)) 
			$character->weaponEditFormat($_POST['weaponNum']);
	}
?>