<?
	checkLogin();
	
	if (isset($_POST['create']) || isset($_POST['save'])) {
		unset($_SESSION['errors']);
		unset($_SESSION['errorVals']);
		unset($_SESSION['errorTime']);
		
		$userID = intval($_SESSION['userID']);
		$gameID = intval($_POST['gameID']);
		$details['title'] = sanatizeString($_POST['title']);
		$systemInfo = $mysql->query('SELECT systemID, shortName, fullName FROM systems WHERE systemID = '.intval($_POST['system']));
		if ($systemInfo->rowCount()) {
			$systemInfo = $systemInfo->fetch();
			$details['systemID'] = intval($_POST['system']);
			$details['system'] = $systemInfo['fullName'];
		} else $details['systemID'] = 0;
		$details['postFrequency'] = intval($_POST['timesPer']).'/'.$_POST['perPeriod'];
		$details['numPlayers'] = intval($_POST['numPlayers']);
		$details['description'] = sanatizeString($_POST['description']);
		$details['charGenInfo'] = sanatizeString($_POST['charGenInfo']);
		
		if (strlen($details['title']) == 0) { $_SESSION['errors']['invalidTitle'] = TRUE; }
		if (!isset($_POST['save'])) {
			$titleCheck = $mysql->query('SELECT gameID FROM games WHERE title = "'.$details['title'].'"');
			if ($titleCheck->rowCount()) $_SESSION['errors']['repeatTitle'] = TRUE;
		}
		if ($details['systemID'] == 0) $_SESSION['errors']['invalidSystem'] = TRUE;
		if (intval($_POST['timesPer']) == 0 || !($_POST['perPeriod'] == 'd' || $_POST['perPeriod'] == 'w')) { $_SESSION['errors']['invalidFreq'] = TRUE; }
		if ($details['numPlayers'] < 2) { $_SESSION['errors']['invalidNumPlayers'] = TRUE; }
		
		if (sizeof($_SESSION['errors'])) {
			$_SESSION['errorVals'] = $_POST;
			$_SESSION['errorTime'] = time() + 300;
			if (isset($_POST['save'])) header('Location: '.SITEROOT.'/games/'.$gameID.'?failed=1');
			else header('Location: '.SITEROOT.'/games/new?failed=1');
		} elseif (isset($_POST['save'])) {
			$mysql->query('UPDATE games SET '.$mysql->setupUpdates($details).' WHERE gameID = '.$gameID);
			$mysql->query('UPDATE forums, forums_groups, games SET forums.title = "'.$details['title'].'", forums_groups.name = "'.$details['title'].'" WHERE forums.forumID = games.forumID AND forums_groups.groupID = games.groupID AND games.gameID = '.$gameID);
			$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action) VALUES ($gameID, $userID, NOW(), 'editedGame')");
			
			header('Location: '.SITEROOT.'/games/'.$gameID);
		} else {
			$details['gmID'] = $userID;
			$details['created'] = date('Y-m-d H:i:s');
			$details['start'] = $details['created'];

			$forumInfo = $mysql->query('SELECT MAX(`order`) + 1 AS newOrder, heritage FROM forums WHERE parentID = 2');
			list($order, $heritage) = $forumInfo->fetch();
			$mysql->query('INSERT INTO forums (`title`, `parentID`, `order`) VALUES ("'.$details['title'].'", 2, '.$order.')');
			$forumID = $mysql->lastInsertId();
			$heritage = substr($heritage, 0, -3).str_pad($forumID, 3, '0', STR_PAD_LEFT);
			$mysql->query('UPDATE forums SET heritage = "'.$heritage.'" WHERE forumID = '.$forumID);
			$details['forumID'] = $forumID;
			
			$mysql->query('INSERT INTO forums_groups (`name`, `ownerID`, `gameGroup`) VALUES ("'.$details['title'].'", '.$userID.', 1)');
			$groupID = $mysql->lastInsertId();
			$details['groupID'] = $groupID;
			
			$system = $details['system'];
			unset($details['system']);
			$addGame = $mysql->prepare('INSERT INTO games (`title`, `systemID`, `gmID`, `created`, `start`, `postFrequency`, `numPlayers`, `description`, `charGenInfo`, `forumID`, `groupID`) VALUES (:title, :systemID, :gmID, :created, :start, :postFrequency, :numPlayers, :description, :charGenInfo, :forumID, :groupID)');
			$addGame->bindParam('title', $details['title']);
			$addGame->bindParam('systemID', $details['systemID']);
			$addGame->bindParam('gmID', $details['gmID']);
			$addGame->bindParam('created', $details['created']);
			$addGame->bindParam('start', $details['start']);
			$addGame->bindParam('postFrequency', $details['postFrequency']);
			$addGame->bindParam('numPlayers', $details['numPlayers']);
			$addGame->bindParam('description', $details['description']);
			$addGame->bindParam('charGenInfo', $details['charGenInfo']);
			$addGame->bindParam('forumID', $details['forumID']);
			$addGame->bindParam('groupID', $details['groupID']);
			$addGame->execute();
			$gameID = $mysql->lastInsertId();
			
			$mysql->query("INSERT INTO gms (gameID, userID, `primary`) VALUES ($gameID, $userID, 1)");
			
			$mysql->query('INSERT INTO forums_groupMemberships (groupID, userID) VALUES ('.$groupID.', '.$userID.')');
			
			$mysql->query('INSERT INTO forumAdmins (userID, forumID) VALUES('.$userID.', '.$forumID.')');
			$mysql->query('INSERT INTO forums_permissions_general (`forumID`, `read, `write`, `createThread`, `moderate`) VALUES ('.$forumID.', -1, -1, -1, -1)');
			$mysql->query('INSERT INTO forums_permissions_groups (`groupID`, `forumID`, `read`, `write`, `editPost`, `createThread`, `deletePost`, `addRolls`, `addDraws`) VALUES ('.$groupID.', '.$forumID.', 1, 1, 1, 1, 1, 1, 1)');
//			$mysql->query('INSERT INTO forums_permissions_users '.$mysql->setupInserts(array('userID' => $userID, 'forumID' => $forumID, 'read' => 1, 'write' => 1, 'editPost' => 1, 'deletePost' => 1, 'createThread' => 1, 'deleteThread' => 1, 'moderate' => 1)));
			
			$mysql->query("INSERT INTO chat_sessions (gameID, locked) VALUES ($gameID, 0)");
			
			$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action) VALUES ($gameID, $userID, NOW(), 'newGame')");
			
//			mail('contact@gamersplane.com', 'New Game', "Game: {$details['title']}\nGM: {$_SESSION['username']}\nSystem: {$system}");
			
			$lfgRecips = $mysql->query("SELECT users.userID, users.email FROM users, lfg WHERE users.newGameMail = 1 AND users.userID = lfg.userID AND lfg.game = '{$system}'");
			$recips = '';
			foreach ($lfgRecips as $info) $recips .= $info['email'].', ';
			ob_start();
			include('games/process/newGameEmail.php');
			$email = ob_get_contents();
			ob_end_clean();
			mail('Gamers Plane <contact@gamersplane.com>', "New {$systemNames[$system]} Game: {$details['title']}", $email, 'Content-type: text/html;\r\nFrom: Gamers Plane <contact@gamersplane.com>;\r\nBcc: '.substr($recips, 0, -2));
			
			header('Location: '.SITEROOT.'/games/my/');
		}
	} else { header('Location: '.SITEROOT.'/games/my/'); }
?>