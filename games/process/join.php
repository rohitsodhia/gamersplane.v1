<?
	if (isset($_POST['apply'])) {
		$gameID = intval($_POST['gameID']);
		
		$numPlayers = $mysql->query("SELECT numPlayers FROM games WHERE gameID = $gameID");
		$numPlayers = $numPlayers->fetchColumn();
		$numApprovedPlayers = $mysql->query("SELECT u.userID FROM users u, players p WHERE p.gameID = $gameID AND u.userID = p.userID AND p.approved = 1 ORDER BY u.username ASC");
		$numApprovedPlayers = $numApprovedPlayers->rowCount() - 1;
		
		if ($numApprovedPlayers < $numPlayers) {
			$mysql->query("INSERT INTO players (gameID, userID) VALUES ($gameID, {$currentUser->userID})");
			addGameHistory($gameID, 'playerApplied');

			foreach ($gmEmails as $key => $gmID) {
				$gmMail = $mysql->query("SELECT metaValue FROM usermeta WHERE userID = {$gmID} AND metaKey = 'gmMail'")->fetchColumn();
				if ($gmMail != 1) 
					unset($gmEmails[$key]);
			}
			if (sizeof($gmEmails)) {
				$emailDetails = new stdClass();
				$emailDetails->action = 'User Applied';
				$emailDetails->gameInfo = $mysql->query("SELECT gameID, title, system FROM games WHERE gameID = {$gameID}")->fetch(PDO::FETCH_OBJ);
				$emailDetails->message = "<a href=\"http://gamersplane.com/user/{$currentUser->userID}/\" class=\"username\">{$currentUser->username}</a> has applied to your game.";
				ob_start();
				include('gmEmail.php');
				$email = ob_get_contents();
				ob_end_clean();
				mail(implode(', ', $gmEmails), "Game Activity: {$emailDetails->action}", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>");
			}
		}
		header("Location: /games/{$gameID}");
	} else 
		header('Location: /403');
?>