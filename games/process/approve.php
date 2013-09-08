<?
	checkLogin(0);
	
	$gameID = intval($_POST['gameID']);
	if (isset($_POST['approve'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		
		$sanityCheck = $mysql->query('SELECT characters.userID, games.groupID FROM characters INNER JOIN games ON characters.gameID = games.gameID INNER JOIN gms ON games.gameID = gms.gameID WHERE characters.characterID = '.$characterID.' AND gms.userID = '.$userID);
		
		if ($sanityCheck->rowCount() == 0) header('Location: '.SITEROOT.'/games/'.$gameID.'/?approveError=1');
		else {
			list($playerID, $groupID, $system) = $sanityCheck->fetch();
			$mysql->query('UPDATE characters SET approved = 1, activeSince = NOW() WHERE characterID = '.$characterID);
			$mysql->query('INSERT INTO forums_groupMemberships '.$mysql->setupInserts(array('groupID' => $groupID, 'userID' => $playerID)));
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'joinedGame')");
			
			$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, enactedUpon, action) VALUES ($gameID, $userID, NOW(), $playerID, 'charApproved')");
			
			header('Location: '.SITEROOT.'/games/'.$gameID);
		}
	} else header('Location: '.SITEROOT.'/games/'.($gameID?$gameID:''));
?>