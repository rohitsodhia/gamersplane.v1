<?
	if (isset($_POST['save']) || isset($_POST['saveAndExit'] )) {
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
					if(isset($_POST['saveAndExit'])){
						header('Location: /characters/'.SYSTEM.'/'.$characterID.'/'); exit;
					} else {
						header('Location: /characters/'.SYSTEM.'/'.$characterID.'/edit/'); exit;
					}
				}
			}
		}
	}

	header('Location: /403/');
?>
