<?
	define('SYSTEM', $_POST['system']);
	if ($systems->verifySystem(SYSTEM)) {
		require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
		$charClass = SYSTEM.'Character';
		if ($character = new $charClass($characterID)) {
			$character->armorEditFormat($_POST['armorNum']);
		}
	}
?>