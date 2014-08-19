<?
	checkLogin();
	
	if (isset($_POST['create'])) {
		$userID = intval($_SESSION['userID']);
		$systemID = intval($_POST['system']);
		$errors = '?';
		$systemShort = $systems->getShortName($systemID);
		if ($systemShort == FALSE) $errors .= 'invalidType=1&';
		if (strcmp(filterString($_POST['label']), $_POST['label']) || $_POST['label'] == '') $errors .= 'invalidLabel=1&';

		if ($errors != '?') {
			header('Location: /characters/my/'.substr($errors, 0, -1));
		} else {
			$addCharacter = $mysql->prepare('INSERT INTO characters (userID, label, type, systemID) VALUES (:userID, :label, :type, :systemID)');
			$addCharacter->bindValue(':userID', $userID);
			$addCharacter->bindValue(':label', $_POST['label']);
			$addCharacter->bindValue(':type', $_POST['type']);
			$addCharacter->bindValue(':systemID', $systemID);
			$addCharacter->execute();
			$characterID = $mysql->lastInsertId();

			require_once(FILEROOT.'/includes/packages/'.$systemShort.'Character.package.php');

			$charClass = $systemShort.'Character';
			$newChar = new $charClass($characterID);
			$newChar->setLabel($_POST['label']);
			$newChar->setType($_POST['type']);
			$newChar->save();
			addCharacterHistory($characterID, 'charCreated', $userID, 'NOW()', $systemID);

			header('Location: /characters/'.$systemShort.'/'.$characterID.'/edit/new');
		}
	} else {
		header('Location: /403');
	}
?>