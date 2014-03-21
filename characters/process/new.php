<?
	checkLogin();
	
	if (isset($_POST['create'])) {
		$userID = intval($_SESSION['userID']);
		$systemID = intval($_POST['system']);
		$errors = '?';
		$systemShort = $mysql->query('SELECT shortName FROM systems WHERE systemID = '.$systemID);
		if ($systemShort->rowCount() == 0) $errors .= 'invalidType=1&';
		else $systemShort = $systemShort->fetchColumn();
		if (strcmp(filterString($_POST['label']), $_POST['label']) || $_POST['label'] == '') $errors .= 'invalidLabel=1&';

		if ($errors != '?') {
			header('Location: '.SITEROOT.'/characters/my/'.substr($errors, 0, -1));
		} else {
			$addCharacter = $mysql->prepare('INSERT INTO characters (userID, label, mob, systemID) VALUES (:userID, :label, :mob, :systemID)');
			$addCharacter->bindValue(':userID', $userID);
			$addCharacter->bindValue(':label', $_POST['label']);
			$addCharacter->bindValue(':type', $_POST['type'] == 1?1:0);
			$addCharacter->bindValue(':systemID', $systemID);
			$addCharacter->execute();
			$characterID = $mysql->lastInsertId();

			$mysql->query('INSERT INTO '.$systemShort.'_characters (characterID) VALUES ('.$characterID.')');
			addCharacterHistory($characterID, 'created', $userID, 'NOW()', $systemID);

			header('Location: '.SITEROOT.'/characters/'.$systemShort.'/'.$characterID.'/edit/new');
		}
	} else {
		header('Location: '.SITEROOT.'/403');
	}
?>