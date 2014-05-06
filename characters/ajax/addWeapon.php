<?
	define('SYSTEM', $_POST['system']);
	if ($systems->getSystemID(SYSTEM)) {
		includeSystemInfo(SYSTEM);
		$charClass = SYSTEM.'Character';
		if ($character = new $charClass($characterID)) {
			$character->weaponEditFormat($_POST['weaponNum']);
		}
	}
?>