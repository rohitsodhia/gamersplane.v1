<?
	checkLogin(0);

	if (isset($_POST['delete'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		define('SYSTEM', getCharacterClass($characterID));
		if (SYSTEM) {
			require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
			$charClass = SYSTEM.'Character';
			if ($character = new $charClass($characterID)) {
				$character->delete();

				if (isset($_POST['modal'])) echo 'deleted';
				else header('Location: /characters/my?delete=1');
			}
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: /403/');
	}
?>