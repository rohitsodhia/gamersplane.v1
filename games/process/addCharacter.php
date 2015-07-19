<?
	if (isset($_POST['submitCharacter'])) {
		$gameID = intval($_POST['gameID']);
		$characterID = intval($_POST['characterID']);

		$charInfo = $mysql->query("SELECT gameID, label FROM characters WHERE retired IS NULL AND characterID = {$characterID} AND userID = {$currentUser->userID}")->fetch();
		
		if (is_int($charInfo['gameID'])) 
			header('Location: /403/');
		elseif ($charInfo['gameID'] == 0) {
			$mysql->query("UPDATE characters SET gameID = {$gameID} WHERE characterID = {$characterID}");
			addCharacterHistory($characterID, 'charApplied', $currentUser->userID, 'NOW()', $gameID);
			addGameHistory($gameID, 'charApplied', $currentUser->userID, 'NOW()', 'character', $characterID);

			foreach ($gmEmails as $key => $gmID) {
				$gmMail = $mysql->query("SELECT metaValue FROM usermeta WHERE userID = {$gmID} AND metaKey = 'gmMail'")->fetchColumn();
				if ($gmMail != 1) 
					unset($gmEmails[$key]);
			}
			if (sizeof($gmEmails)) {
				$charDetails = $mongo->characters->findOne(array('characterID' => $characterID), array('name' => 1));
				$emailDetails = new stdClass();
				$emailDetails->action = 'Character Added';
				$emailDetails->gameInfo = $mysql->query("SELECT gameID, title, system FROM games WHERE gameID = {$gameID}")->fetch(PDO::FETCH_OBJ);
				$charLabel = strlen($charDetails['name'])?$charDetails['name']:$charInfo['label'];
				$emailDetails->message = "<a href=\"http://gamersplane.com/user/{$currentUser->userID}/\" class=\"username\">{$currentUser->username}</a> applied a new character to your game: <a href=\"http://gamersplane.com/characters/{$characterID}/\">{$charLabel}</a>.";
				ob_start();
				include('gmEmail.php');
				$email = ob_get_contents();
				ob_end_clean();
				$gmEmails = $mysql->query("SELECT u.email FROM users u INNER JOIN players p ON u.userID = p.userID AND p.isGM = 1 WHERE g.gameID = {$gameID}")->fetchAll(PDO::FETCH_COLUMN);
				mail(implode(', ', $gmEmails), "Game Activity: {$emailDetails->action}", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>");
			}

			header("Location: /games/{$gameID}/");
		} else 
			header("Location: /games/{$gameID}/");
	} else 
		header('Location: /403');
?>