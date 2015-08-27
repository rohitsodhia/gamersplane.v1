<?
	if (isset($_POST['save'])) {
		$characterID = intval($_POST['characterID']);
		define('SYSTEM', $_POST['system']);
		if ($systems->verifySystem(SYSTEM)) {
			require_once(FILEROOT."/includes/packages/".SYSTEM."Character.package.php");
			$charClass = Systems::systemClassName(SYSTEM).'Character';
			if ($character = new $charClass($characterID)) {
				$character->load();
				$charPermissions = $character->checkPermissions($currentUser->userID);
				if ($charPermissions == 'edit') {
					$character->save();
					header('Location: /characters/'.SYSTEM.'/'.$characterID); exit;
				}
			}
		}
	}
	
	header('Location: /403');
?>