<?
	define('SYSTEM', $_POST['system']);
	if ($systems->getSystemID(SYSTEM)) {
		require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
		$charClass = SYSTEM.'Character';
		if ($character = new $charClass($characterID)) {
			$character->armorEditFormat($_POST['armorNum']);
		}
	}
?>