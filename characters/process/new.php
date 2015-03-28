<?
	if (isset($_POST['create'])) {
		$system = $_POST['system'];
		$errors = '?';
		if (!$systems->verifySystem($system)) 
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
			$addCharacter->bindValue(':system', $system);
			$addCharacter->execute();
			$characterID = $mysql->lastInsertId();

			require_once(FILEROOT.'/includes/packages/'.$system.'Character.package.php');

			$charClass = $system.'Character';
			$newChar = new $charClass($characterID);
			$newChar->setLabel($_POST['label']);
			$newChar->setCharType($_POST['charType']);
			$newChar->save();
			addCharacterHistory($characterID, 'charCreated', $currentUser->userID, 'NOW()', $system);

			header('Location: /characters/'.$system.'/'.$characterID.'/edit/new/');
		}
	} else 
		header('Location: /403');
?>