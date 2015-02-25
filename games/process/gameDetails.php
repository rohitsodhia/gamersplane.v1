<?
	if (isset($_POST['create']) || isset($_POST['save'])) {
		unset($_SESSION['errors']);
		unset($_SESSION['errorVals']);
		unset($_SESSION['errorTime']);
		
		$gameID = intval($_POST['gameID']);
		$details['title'] = $_POST['title'];
		$systemInfo = $systems->getSystemInfo(intval($_POST['system']));
		if ($systemInfo) {
			$details['systemID'] = intval($_POST['system']);
			$details['system'] = $systemInfo['fullName'];
		} else $details['systemID'] = 0;
		$details['postFrequency'] = intval($_POST['timesPer']).'/'.$_POST['perPeriod'];
		$details['numPlayers'] = intval($_POST['numPlayers']);
		$details['charsPerPlayer'] = intval($_POST['charsPerPlayer']);
		$details['description'] = sanitizeString($_POST['description']);
		$details['charGenInfo'] = sanitizeString($_POST['charGenInfo']);
		
		if (strlen($details['title']) == 0) $_SESSION['errors']['invalidTitle'] = TRUE;
		$titleCheck = $mysql->prepare('SELECT gameID FROM games WHERE title = :title'.(isset($_POST['save'])?' AND gameID != '.$gameID:''));
		$titleCheck->execute(array(':title' => $details['title']));
		if ($titleCheck->rowCount()) $_SESSION['errors']['repeatTitle'] = TRUE;
		if ($details['systemID'] == 0 && !isset($_POST['save'])) $_SESSION['errors']['invalidSystem'] = TRUE;
		if (intval($_POST['timesPer']) == 0 || !($_POST['perPeriod'] == 'd' || $_POST['perPeriod'] == 'w')) { $_SESSION['errors']['invalidFreq'] = TRUE; }
		if ($details['numPlayers'] < 2) { $_SESSION['errors']['invalidNumPlayers'] = TRUE; }
		
		if (sizeof($_SESSION['errors'])) {
			$_SESSION['errorVals'] = $_POST;
			$_SESSION['errorTime'] = time() + 300;
			if (isset($_POST['save'])) header('Location: /games/'.$gameID.'/edit?failed=1');
			else header('Location: /games/new?failed=1');
		} elseif (isset($_POST['save'])) {
			$updateGame = $mysql->prepare('UPDATE games SET title = :title, postFrequency = :postFrequency, numPlayers = :numPlayers, charsPerPlayer = :charsPerPlayer, description = :description, charGenInfo = :charGenInfo WHERE gameID = :gameID');
			$updateGame->bindValue(':title', $details['title']);
			$updateGame->bindValue(':postFrequency', $details['postFrequency']);
			$updateGame->bindValue(':numPlayers', $details['numPlayers']);
			$updateGame->bindValue(':charsPerPlayer', $details['charsPerPlayer']);
			$updateGame->bindValue(':description', $details['description']);
			$updateGame->bindValue(':charGenInfo', $details['charGenInfo']);
			$updateGame->bindValue(':gameID', $gameID);
			$updateGame->execute();
			$updateForums = $mysql->prepare('UPDATE forums, forums_groups, games SET forums.title = :title, forums_groups.name = :title WHERE forums.forumID = games.forumID AND forums_groups.groupID = games.groupID AND games.gameID = :gameID');
			$updateForums->bindValue(':title', $details['title']);
			$updateForums->bindValue(':gameID', $gameID);
			$updateForums->execute();
			addGameHistory($gameID, 'editedGame');
			
			header('Location: /games/'.$gameID);
		} else {
			$details['gmID'] = $currentUser->userID;
			$details['created'] = date('Y-m-d H:i:s');
			$details['start'] = $details['created'];

			$system = $details['system'];
			$addGame = $mysql->prepare('INSERT INTO games (title, systemID, gmID, created, start, postFrequency, numPlayers, description, charGenInfo, forumID, groupID) VALUES (:title, :systemID, :gmID, :created, :start, :postFrequency, :numPlayers, :description, :charGenInfo, -1, -1)');
			$addGame->bindParam('title', $details['title']);
			$addGame->bindParam('systemID', $details['systemID']);
			$addGame->bindParam('gmID', $details['gmID']);
			$addGame->bindParam('created', $details['created']);
			$addGame->bindParam('start', $details['start']);
			$addGame->bindParam('postFrequency', $details['postFrequency']);
			$addGame->bindParam('numPlayers', $details['numPlayers']);
			$addGame->bindParam('description', $details['description']);
			$addGame->bindParam('charGenInfo', $details['charGenInfo']);
			$addGame->execute();
			$gameID = $mysql->lastInsertId();
			
			$mysql->query("INSERT INTO players (gameID, userID, approved, isGM, primaryGM) VALUES ($gameID, {$currentUser->userID}, 1, 1, 1)");
			
			$forumInfo = $mysql->query('SELECT MAX(`order`) + 1 AS newOrder, heritage FROM forums WHERE parentID = 2');
			list($order, $heritage) = $forumInfo->fetch(PDO::FETCH_NUM);
			$addForum = $mysql->prepare("INSERT INTO forums (title, parentID, heritage, `order`, gameID) VALUES (:title, 2, ".mt_rand(0, 9999).", {$order}, {$gameID})");
			$addForum->execute(array(':title' => $details['title']));
			$forumID = $mysql->lastInsertId();
			$heritage = sql_forumIDPad(2).'-'.sql_forumIDPad($forumID);
			$mysql->query('UPDATE forums SET heritage = "'.$heritage.'" WHERE forumID = '.$forumID);
			$details['forumID'] = $forumID;
			
			$addForumGroup = $mysql->prepare('INSERT INTO forums_groups (name, ownerID, gameGroup) VALUES (:title, '.$currentUser->userID.', 1)');
			$addForumGroup->execute(array('title' => $details['title']));
			$groupID = $mysql->lastInsertId();
			$details['groupID'] = $groupID;

			$mysql->query("UPDATE games SET forumID = {$forumID}, groupID = {$groupID} WHERE gameID = {$gameID}");
			
			$mysql->query('INSERT INTO forums_groupMemberships (groupID, userID) VALUES ('.$groupID.', '.$currentUser->userID.')');
			
			$mysql->query('INSERT INTO forumAdmins (userID, forumID) VALUES('.$currentUser->userID.', '.$forumID.')');
			$mysql->query('INSERT INTO forums_permissions_groups (`groupID`, `forumID`, `read`, `write`, `editPost`, `createThread`, `deletePost`, `addRolls`, `addDraws`) VALUES ('.$groupID.', '.$forumID.', 2, 2, 2, 2, 2, 2, 2)');
			$mysql->query("INSERT INTO forums_permissions_general SET forumID = $forumID");
			
			addGameHistory($gameID, 'newGame');
			
			$lfgRecips = $mysql->query("SELECT users.userID, users.email FROM users, lfg WHERE users.newGameMail = 1 AND users.userID = lfg.userID AND lfg.systemID = {$details['systemID']}");
			$recips = '';
			foreach ($lfgRecips as $info) $recips .= $info['email'].', ';
			ob_start();
			include('games/process/newGameEmail.php');
			$email = ob_get_contents();
			ob_end_clean();
			mail('Gamers Plane <contact@gamersplane.com>', "New {$systemNames[$system]} Game: {$details['title']}", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>\r\nBcc: ".substr($recips, 0, -2));
			
			header('Location: /games/my/');
		}
	} else header('Location: /games/my/');
?>