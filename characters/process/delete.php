<?
	if (isset($_POST['delete'])) {
		$characterID = intval($_POST['characterID']);
		define('SYSTEM', getCharacterClass($characterID));
		if (SYSTEM) {
			$charClass = $systems->systemClassName(SYSTEM).'Character';
			require_once(FILEROOT."/includes/packages/{$charClass}.package.php");
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