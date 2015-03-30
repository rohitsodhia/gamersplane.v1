<?
	if (isset($_POST['create'])) {
		define('SYSTEM', $_POST['system']);
		$errors = '?';
		if (!$systems->verifySystem(SYSTEM)) 
			$errors .= 'invalidType=1&';
		if (strcmp(filterString($_POST['label']), $_POST['label']) || $_POST['label'] == '') 
			$errors .= 'invalidLabel=1&';

		if ($errors != '?') 
			header('Location: /characters/my/'.substr($errors, 0, -1));
		else {
			$addCharacter = $mysql->prepare('INSERT INTO characters (userID, label, charType, system) VALUES (:userID, :label, :charType, :system)');
			$addCharacter->bindValue(':userID', $currentUser->userID);
			$addCharacter->bindValue(':label', $_POST['label']);
			$addCharacter->bindValue(':charType', $_POST['charType']);
			$addCharacter->bindValue(':system', SYSTEM);
			$addCharacter->execute();
			$characterID = $mysql->lastInsertId();

			$charClass = $systems->systemClassName(SYSTEM).'Character';
			require_once(FILEROOT."/includes/packages/{$charClass}.package.php");
			$newChar = new $charClass($characterID);
			$newChar->setLabel($_POST['label']);
			$newChar->setCharType($_POST['charType']);
			$newChar->save();
			addCharacterHistory($characterID, 'charCreated', $currentUser->userID, 'NOW()', SYSTEM);

			header('Location: /characters/'.SYSTEM.'/'.$characterID.'/edit/new/');
		}
	} else 
		header('Location: /403');
?>