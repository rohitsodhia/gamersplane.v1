<?php
require_once(FILEROOT . '/includes/Systems.class.php');

class games
{
	function __construct()
	{
		global $loggedIn, $pathOptions;

		if ($pathOptions[0] == 'getGames') {
			$this->getGames();
		} elseif ($pathOptions[0] == 'details') {
			$this->details($_POST['gameID']);
		} elseif ($pathOptions[0] == 'create') {
			$this->createGame();
		} elseif ($pathOptions[0] == 'update') {
			$this->updateGame();
		} elseif ($pathOptions[0] == 'toggleForum' && intval($_POST['gameID'])) {
			$this->toggleForum($_POST['gameID']);
		} elseif ($pathOptions[0] == 'toggleGameStatus' && intval($_POST['gameID'])) {
			$this->toggleGameStatus($_POST['gameID']);
		} elseif ($pathOptions[0] == 'retire' && intval($_POST['gameID'])) {
			$this->retire($_POST['gameID']);
		} elseif ($pathOptions[0] == 'unretire' && intval($_POST['gameID'])) {
			$this->unretire($_POST['gameID']);
		} elseif ($pathOptions[0] == 'apply') {
			$this->apply();
		} elseif ($pathOptions[0] == 'toggleFavorite') {
			$this->toggleFavorite();
		}  elseif (
			$pathOptions[0] == 'invite' &&
			sizeof($pathOptions) == 1 &&
			intval($_POST['gameID']) &&
			(int)$_POST['userID']
		) {
			$this->invite($_POST['gameID'], $_POST['userID']);
		} elseif (
			$pathOptions[0] == 'invite' && ($pathOptions[1] == 'withdraw' || $pathOptions[1] == 'decline') &&
			intval($_POST['gameID']) &&
			strlen($_POST['userID'])
		) {
			$this->removeInvite($_POST['gameID'], $_POST['userID']);
		} elseif ($pathOptions[0] == 'invite' && $pathOptions[1] == 'accept' && intval($_POST['gameID'])) {
			$this->acceptInvite($_POST['gameID']);
		} elseif (
			$pathOptions[0] == 'characters' &&
			$pathOptions[1] == 'submit' &&
			intval($_POST['gameID']) &&
			intval($_POST['characterID'])
		) {
			$this->submitCharacter((int)$_POST['gameID'], (int)$_POST['characterID']);
		} elseif (
			$pathOptions[0] == 'characters' &&
			$pathOptions[1] == 'remove' &&
			intval($_POST['gameID']) &&
			intval($_POST['characterID'])
		) {
			$this->removeCharacter((int)$_POST['gameID'], (int)$_POST['characterID']);
		} elseif (
			$pathOptions[0] == 'characters' &&
			$pathOptions[1] == 'approve' &&
			intval($_POST['gameID']) &&
			intval($_POST['characterID'])
		) {
			$this->approveCharacter((int)$_POST['gameID'], (int)$_POST['characterID']);
		} elseif ($pathOptions[0] == 'getLFG') {
			$this->getLFG();
		} else {
			displayJSON(['failed' => true]);
		}
	}

	public function getGames()
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$systems = Systems::getInstance();

		$myGames = false;
		$showFullGames = isset($_GET['showFullGames']) && $_GET['showFullGames'] === 'true';
		$limit = isset($_GET['limit']) && (int) $_GET['limit'] > 0 ? (int) $_GET['limit'] : null;
		if (isset($_GET['my']) && $_GET['my']) {
			$myGames = true;
			$getGames = $mysql->query("SELECT games.gameID, games.title, games.system, gm.userID gmID, gm.username gmUsername, gm.lastActivity gmLastActivity, games.`status`, games.customSystem, games.retired, games.forumID, userIsPlayer.isGM, COUNT(players.userID) numPlayers, IF(games_favorites.userID, 1, 0) isFavorite FROM games INNER JOIN players userIsPlayer ON games.gameID = userIsPlayer.gameID AND userIsPlayer.userID = {$currentUser->userID} AND userIsPlayer.approved INNER JOIN users gm ON games.gmID = gm.userID LEFT JOIN players ON games.gameID = players.gameID AND players.approved LEFT JOIN games_favorites ON games.gameID = games_favorites.gameID AND games_favorites.userID = {$currentUser->userID} GROUP BY games.gameID" . ($limit ? "LIMIT {$limit}" : ''));
		} else {
			$findParams = [
				'retired IS NULL'
			];
			if ($_GET['systems']) {
				$searchSystems = [];
				foreach($_GET['search'] as $searchSystem) {
					if ($systems->verifySystem($searchSystem)) {
						$searchSystems[] = $searchSystem;
					}
				}
				$findParams[] = 'games.system IN (' . implode(', ', $searchSystems) . ')';
			}
			if (!$showFullGames) {
				$findParams[] = "numPlayers < games.numPlayers";
			}
			$getGames = $mysql->query("SELECT games.gameID, games.title, games.system, gm.userID gmID, gm.username gmUsername, gm.lastActivity gmLastActivity, games.start, games.`status`, games.customSystem, games.public, games.retired, games.forumID, games.numPlayers, COUNT(players.userID) playerCount FROM games INNER JOIN users gm ON games.gmID = gm.userID" . (!isset($_GET['hideInactive']) || !$_GET['hideInactive'] ? " AND gm.lastActivity > NOW() - INTERVAL 14 DAY" : '') . " LEFT JOIN players ON games.gameID = players.gameID AND players.approved AND players.isGM = 0 WHERE " . implode(' AND ', $findParams) . " GROUP BY games.gameID ORDER BY games.created DESC" . ($limit ? " LIMIT {$limit}" : ''));
		}
		$games = [];
		$gms = [];
		$count = 0;
		foreach ($getGames->fetchAll() as $game) {
			$game['system'] = $systems->getFullName($game['system']);
			$game['isRetired'] = $game['retired'] != null;
			foreach (['gameID', 'forumID', 'numPlayers', 'playerCount'] as $intKey) {
				$game[$intKey] = (int) $game[$intKey];
			}
			foreach (['status', 'public', 'isGM'] as $boolKey) {
				$game[$boolKey] = (bool) $game[$boolKey];
			}
			if ($game['start']) {
				$game['start'] = strtotime($game['start']);
			}
			$game['gm'] = [
				'userID' => $game['gmID'],
				'username' => $game['gmUsername'],
				'lastActivity' => $game['gmLastActivity']
			];
			$game['isFavorite'] = (bool) $game['isFavorite'];
			unset($game['retired'], $game['gmID'], $game['gmUsername'], $game['gmLastActivity']);
			$games[] = $game;
		}

		displayJSON(['success' => true, 'games' => $games]);
	}

	public function details($gameID)
	{
		require_once(FILEROOT . '/javascript/markItUp/markitup.bbcode-parser.php');
		require_once(FILEROOT . '/includes/User.class.php');
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = intval($gameID);
		if (!$gameID) {
			displayJSON(['failed' => true]);
		}
		$gameInfo = $mysql->query("SELECT games.gameID, games.title, games.customSystem, games.system, gm.userID gmID, gm.username gmUsername, gm.lastActivity, games.created, games.start, games.end, games.postFrequency, games.numPlayers, games.charsPerPlayer, games.description, games.charGenInfo, games.forumID, games.groupID, games.status, games.public, games.retired, games.allowedCharSheets, games.gameOptions, games.recruitmentThreadId FROM games INNER JOIN users gm ON games.gmID = gm.userID WHERE games.gameID = {$gameID} LIMIT 1");
		if (!$gameInfo->rowCount()) {
			displayJSON(['failed' => true, 'noGame' => true]);
		}
		$gameInfo = $gameInfo->fetch();
		$gameInfo['readPermissions'] = $mysql->query("SELECT `read` FROM forums_permissions_general WHERE forumID = {$gameInfo['forumID']} LIMIT 1")->fetchColumn();
		$gameInfo['readPermissions'] = (bool)$gameInfo['readPermissions'];
		$gameInfo['gameID'] = (int) $gameInfo['gameID'];
		$gameInfo['gm'] = [
			'userID' => (int) $gameInfo['gmID'],
			'username' => $gameInfo['gmUsername'],
			'lastActivity' => $gameInfo['lastActivity']
		];
		unset($gameInfo['userID'], $gameInfo['username'], $gameInfo['lastActivity']);
		$gameInfo['title'] = printReady($gameInfo['title'], ['nl2br']);
		$gameInfo['created'] = date('F j, Y g:i a', strtotime($gameInfo['created']));
		$gameInfo['description'] = strlen($gameInfo['description']) ? $gameInfo['description'] : 'None Provided';
		$gameInfo['charGenInfo'] = strlen($gameInfo['charGenInfo']) ? $gameInfo['charGenInfo'] : 'None Provided';
		$gameInfo['approvedPlayers'] = 0;
		if (strlen($gameInfo['customSystem'])) {
			unset($gameInfo['customSystem']);
		}
		foreach (['allowedCharSheets', 'postFrequency'] as $jsonKey) {
			$gameInfo[$jsonKey] = json_decode($gameInfo[$jsonKey]);
		}
		$gameInfo['status'] = (bool) $gameInfo['status'] ? 'open' : 'closed';
		$gameInfo['public'] = (bool) $gameInfo['public'];

		$getDecks = $mysql->query("SELECT deckID, label, deck, position FROM decks WHERE gameID = {$gameID}");
		$decks = [];
		if ($getDecks->rowCount()) {
			foreach ($getDecks->fetchAll() as $deck) {

				$decks[] = [
					'deckID' => (int) $deck['deckID'],
					'type' => $deck['type'],
					'label' => $deck['label'],
					'cardsRemaining' => sizeof(json_decode($deck['deck'])) - (int) $deck['position'] + 1
				];
			}
		}

		$getPlayers = $mysql->query("SELECT players.userID, users.username, players.approved, players.isGM FROM players INNER JOIN users ON players.userID = users.userID WHERE players.gameID = {$gameID} ORDER BY players.isGM DESC, users.username");
		$players = [];
		$playerIDs = [];
		foreach ($getPlayers->fetchAll() as $player) {
			$players[] = [
				'user' => [
					'userID' => (int) $player['userID'],
					'username' => $player['username'],
				],
				'approved' => (bool) $player['approved'],
				'isGM' => (bool) $player['isGM'],
				'characters' => []
			];
			$playerIDs[] = $player['userID'];
			if ($player['approved'] && $player['userID'] != $gameInfo['gmID']) {
				$gameInfo['approvedPlayers']++;
			}
		}
		$getCharacters = $mysql->query("SELECT characterID, userID, label, `system`, approved FROM characters WHERE gameID = {$gameID} AND userID IN (". implode(', ', $playerIDs) .") ORDER BY label");
		$characters = [];
		foreach ($getCharacters->fetchAll() as $character) {
			$userID = $character['userID'];
			$character['characterID'] = (int) $character['characterID'];
			$character['approved'] = (bool) $character['approved'];
			unset($character['userID']);
			$characters[$userID][] = $character;
		}
		foreach ($players as &$player) {
			if (isset($characters[$player['user']['userID']])) {
				$player['characters'] = $characters[$player['user']['userID']];
			}
		}

		$getInvites = $mysql->query("SELECT users.userID, users.username FROM gameInvites INNER JOIN users ON gameInvites.userID = users.userID WHERE gameInvites.gameID = {$gameID}");
		$invites = $getInvites->fetchAll();
		array_walk($invites, function (&$value, $key) {
			$value['userID'] = (int) $value['userID'];
		});

		displayJSON([
			'success' => true,
			'details' => $gameInfo,
			'players' => $players,
			'invites' => $invites,
			'decks' => $decks
		]);
	}

	public function createGame()
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$systems = Systems::getInstance();

		$errors = [];
		$details['title'] = sanitizeString($_POST['title']);
		if (strlen($details['title']) == 0) {
			$errors[] = 'invalidTitle';
		}
		$details['system'] = $systems->verifySystem($_POST['system']) ? $_POST['system'] : null;
		$details['allowedCharSheets'] = [];
		if (!is_array($_POST['allowedCharSheets']) || count($_POST['allowedCharSheets']) == 0) {
			$errors[] = 'noCharSheets';
		} else {
			$inPlaceholders = str_repeat("?, ", count($_POST['allowedCharSheets']) - 1) . "?";
			$validCharSheets = $mysql->prepare("SELECT id FROM systems WHERE id IN ({$inPlaceholders}) AND hasCharSheet = TRUE");
			$validCharSheets->execute($_POST['allowedCharSheets']);
			foreach ($validCharSheets->fetchAll() as $system) {
				$details['allowedCharSheets'][] = $system['id'];
			}
			if (sizeof($details['allowedCharSheets']) == 0) {
				$errors[] = 'noCharSheets';
			}
		}
		$details['postFrequency'] = [
			'timesPer' => intval($_POST['postFrequency']->timesPer),
			'perPeriod' => $_POST['postFrequency']->perPeriod
		];
		$details['numPlayers'] = intval($_POST['numPlayers']);
		$details['charsPerPlayer'] = intval($_POST['charsPerPlayer']);
		$details['recruitmentThreadId']=intval($_POST['recruitmentThreadId']);
		if($details['recruitmentThreadId']==0){
			$details['recruitmentThreadId']=null;
		}
		$details['description'] = sanitizeString($_POST['description']);
		$details['charGenInfo'] = sanitizeString($_POST['charGenInfo']);
		if ($_POST['system'] == "custom") {
			$details['customSystem'] = sanitizeString($_POST['customType']);
		}

		$gameOptions = trim($_POST['gameOptions'] ?: "");
		$gameOptions = str_replace(array("‘","’","“","”"), array("'", "'", '"', '"'), $gameOptions);
		json_decode($gameOptions);
		if ($gameOptions != "" || json_last_error() === 0) {
			// JSON is valid
			$details['gameOptions'] = $gameOptions;
		}

		$details['status'] = 0;
		$details['public'] = 1;

		/*			$titleCheck = $mysql->prepare('SELECT gameID FROM games WHERE title = :title'.(isset($_POST['save'])?' AND gameID != '.$gameID:''));
			$titleCheck->execute(array(':title' => $details['title']));
			if ($titleCheck->rowCount())
				$errors[] = 'repeatTitle';*/
		if ($details['system'] == null && !isset($_POST['save'])) {
			$errors[] = 'invalidSystem';
		}
		if ($details['postFrequency']['timesPer'] <= 0 || !($details['postFrequency']['perPeriod'] == 'd' || $details['postFrequency']['perPeriod'] == 'w')) {
			$errors[] = 'invalidFreq';
		}
		if ($details['numPlayers'] < 1) {
			$errors[] = 'invalidNumPlayers';
		}

		if (sizeof($errors)) {
			displayJSON(['failed' => true, 'errors' => $errors]);
		} else {
			$details['postFrequency'] = json_encode($details['postFrequency']);
			$details['allowedCharSheets'] = json_encode($details['allowedCharSheets']);
			$details['gmID'] = (int) $currentUser->userID;
			$details['forumID'] = -1;
			$details['groupID'] = -1;

			$inserts = array_map(function ($value) {
				return "`{$value}` = :{$value}";
			}, array_keys($details));
			$insertGame = $mysql->prepare("INSERT INTO games SET " . implode(', ', $inserts) . ", `created` = NOW(), `start` = NOW()");
			$insertGame->execute($details);
			$gameID = $mysql->lastInsertId();

			$mysql->query("INSERT INTO players SET userID = {$currentUser->userID}, gameID = {$gameID}, approved = 1, isGM = 1");

			$addForum = $mysql->prepare("INSERT INTO forums (title, parentID, heritage, `order`, gameID) VALUES (:title, 2, " . mt_rand(0, 9999) . ", -1, {$gameID})");
			$addForum->execute([':title' => $details['title']]);
			$forumID = $mysql->lastInsertId();
			$heritage = sql_forumIDPad(2) . '-' . sql_forumIDPad($forumID);
			$order = $mysql->query('SELECT MAX(`order`) + 1 AS newOrder FROM forums WHERE parentID = 2')->fetchColumn();
			$mysql->query("UPDATE forums SET heritage = '{$heritage}', `order` = {$order} WHERE forumID = {$forumID}");

			$addForumGroup = $mysql->prepare("INSERT INTO forums_groups (name, ownerID, gameID) VALUES (:title, {$currentUser->userID}, {$gameID})");
			$addForumGroup->execute(['title' => $details['title']]);
			$groupID = $mysql->lastInsertId();

			$mysql->query("INSERT INTO forums_groupMemberships (groupID, userID) VALUES ({$groupID}, {$currentUser->userID})");

			$mysql->query("INSERT INTO forumAdmins (userID, forumID) VALUES({$currentUser->userID}, {$forumID})");
			$mysql->query("INSERT INTO forums_permissions_groups (`groupID`, `forumID`, `read`, `write`, `editPost`, `createThread`, `deletePost`, `addRolls`, `addDraws`) VALUES ({$groupID}, {$forumID}, 2, 2, 2, 2, 2, 2, 2)");
			$mysql->query("INSERT INTO forums_permissions_general (`forumID`, `read`) VALUES ({$forumID}, 1)");

			$mysql->query("UPDATE games SET forumID = {$forumID}, groupID = {$groupID} WHERE gameID = {$gameID} LIMIT 1");

			$currentUser->updateUsermeta('isGM', true);

			$getLFGUsers = $mysql->query("SELECT users.email FROM lfg INNER JOIN users ON lfg.userID = users.userID WHERE lfg.system = '{$details['system']}'");
			if ($getLFGUsers->rowCount()) {
				$recips = $getLFGUsers->fetchAll(PDO::FETCH_COLUMN, 0);
				ob_start();
				include('emails/newGameEmail.php');
				$email = ob_get_contents();
				ob_end_clean();

				$mail = getMailObj();
				$mail->addAddress("contact@gamersplane.com");
				$mail->Subject = "New {$systems->getFullName($system)} Game: {$details['title']}";
				$mail->msgHTML($email);
				foreach ($recips as $email) {
					$mail->addBCC($email);
				}
				$mail->send();
			}

			displayJSON(['success' => true, 'gameID' => (int)$gameID]);
		}
	}

	public function updateGame()
	{
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = intval($_POST['gameID']);
		$gmCheck = $mysql->query("SELECT gameID FROM games WHERE gameID = {$gameID} AND gmID = {$currentUser->userID} LIMIT 1");
		if (!$gmCheck->rowCount()) {
			displayJSON(['unauthorized' => true]);
		}

		$systems = Systems::getInstance();
		$details['system'] = $systems->verifySystem($_POST['system']) ? $_POST['system'] : null;

		$details['title'] = sanitizeString($_POST['title']);
		if (!is_array($_POST['allowedCharSheets']) || count($_POST['allowedCharSheets']) == 0) {
			$errors[] = 'noCharSheets';
		} else {
			$inPlaceholders = str_repeat("?, ", count($_POST['allowedCharSheets']) - 1) . "?";
			$validCharSheets = $mysql->prepare("SELECT id FROM systems WHERE id IN ({$inPlaceholders}) AND hasCharSheet = TRUE");
			$validCharSheets->execute($_POST['allowedCharSheets']);
			foreach ($validCharSheets->fetchAll() as $system) {
				$details['allowedCharSheets'][] = $system['id'];
			}
			if (sizeof($details['allowedCharSheets']) == 0) {
				$errors[] = 'noCharSheets';
			}
		}
		$details['postFrequency'] = [
			'timesPer' => intval($_POST['postFrequency']->timesPer),
			'perPeriod' => $_POST['postFrequency']->perPeriod
		];
		$details['numPlayers'] = intval($_POST['numPlayers']);
		$details['charsPerPlayer'] = intval($_POST['charsPerPlayer']);
		$details['recruitmentThreadId'] = intval($_POST['recruitmentThreadId']);
		if ($details['recruitmentThreadId'] == 0){
			$details['recruitmentThreadId'] = null;
		}
		$details['description'] = sanitizeString($_POST['description']);
		$details['charGenInfo'] = sanitizeString($_POST['charGenInfo']);

		if($_POST['system'] == "custom"){
			$details['customSystem'] = sanitizeString($_POST['customType']);
		} else {
			$details['customSystem'] = null;
		}

		$gameOptions = trim($_POST['gameOptions'] ?: "");
		$gameOptions = str_replace(["‘", "’", "“", "”"], ["'", "'", '"', '"'], $gameOptions);
		$jsonTest = json_decode($gameOptions);
		if ($gameOptions != "" && json_last_error() === 0) {
			// JSON is valid
			$details['gameOptions'] = $gameOptions;
		} else {
			$details['gameOptions'] = '{}';
		}

		$errors = [];
		if (strlen($details['title']) == 0) {
			$errors[] = 'invalidTitle';
		}
		/*
		$titleCheck = $mysql->prepare('SELECT gameID FROM games WHERE title = :title AND gameID != ' . $gameID);
		$titleCheck->execute(array(':title' => $details['title']));
		if ($titleCheck->rowCount())
			$errors[] = 'repeatTitle';
		*/
		// if ($details['system'] == null && !isset($_POST['save']))
		// 	$errors[] = 'invalidSystem';
		if ($details['postFrequency']['timesPer'] <= 0 || !($details['postFrequency']['perPeriod'] == 'd' || $details['postFrequency']['perPeriod'] == 'w')) {
			$errors[] = 'invalidFreq';
		}
		if ($details['numPlayers'] < 1) {
			$errors[] = 'invalidNumPlayers';
		}

		$updateForumGroup = $mysql->prepare("UPDATE forums_groups SET name=:title WHERE gameID={$gameID} ORDER BY groupID LIMIT 1");
		$updateForumGroup->execute(['title' => $details['title']]);

		if (sizeof($errors)) {
			displayJSON(array('failed' => true, 'errors' => $errors));
		} else {
			$setVars = [];
			$details['postFrequency'] = json_encode($details['postFrequency']);
			$details['allowedCharSheets'] = json_encode($details['allowedCharSheets']);
			foreach (array_keys($details) as $key) {
				$setVars[] = "`{$key}` = :{$key}";
			}
			$updateGame = $mysql->prepare("UPDATE games SET " . implode(", ", $setVars) . " WHERE gameID = {$gameID}");
			$updateGame->execute($details);
			$updateForumTitle = $mysql->prepare('UPDATE forums SET title = :title WHERE forumID = :forumID');
			$updateForumTitle->execute(['title' => $details['title'], 'forumID' => $gameInfo['forumID']]);
			// $hl_gameEdited = new HistoryLogger('gameEdited');
			// $hl_gameEdited->addGame($gameID)->save();

			displayJSON(['success' => true, 'gameID' => (int) $gameID]);
		}
	}

	public function toggleForum($gameID) {
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = (int)$gameID;
		$gmCheck = $mysql->query("SELECT forumID FROM games WHERE gameID = {$gameID} AND gmID = {$currentUser->userID} LIMIT 1");
		if ($gmCheck->rowCount()) {
			$forumID = $gmCheck->fetchColumn();
			$mysql->query("UPDATE forums_permissions_general SET `read` = IF(`read` = 1, -1, 1) WHERE forumID = {$forumID} LIMIT 1");
			$mysql->query("UPDATE games SET `public` = NOT `public` WHERE gameID = {$gameID} LIMIT 1");
			displayJSON(['success' => true]);
		} else {
			displayJSON(['failed' => true, 'errors' => 'notGM']);
		}
	}


	public function toggleGameStatus($gameID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = (int)$gameID;
		$gmCheck = $mysql->query("SELECT gameID FROM games WHERE gameID = {$gameID} AND gmID = {$currentUser->userID} LIMIT 1");
		if ($gmCheck->rowCount()) {
			$mysql->query("UPDATE games SET status = NOT status WHERE gameID = {$gameID} LIMIT 1");
			displayJSON(['success' => true]);
		} else {
			displayJSON(['failed' => true, 'errors' => 'notGM']);
		}
	}

	public function retire($gameID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = (int)$gameID;
		$gmCheck = $mysql->query("SELECT gameID FROM games WHERE gameID = {$gameID} AND gmID = {$currentUser->userID} LIMIT 1");
		if ($gmCheck->rowCount()) {
			$mysql->query("UPDATE games SET retired = NOW(), status = FALSE WHERE gameID = {$gameID} LIMIT 1");

			// $groups = $mysql->query("DELETE FROM forums_permissions_group WHERE groupID = {$groupID}");
			// $forums = $mysql->query("SELECT forumID FROM forums WHERE gameID = {$gameID}")->fetchAll(PDO::FETCH_COLUMN);
			// $mysql->query("DELETE FROM forums_permissions_users WHERE forumID IN (".implode(', ', $forums).")");
			// $mysql->query("DELETE FROM forumAdmins WHERE forumID IN (".implode(', ', $forums).")");
			// $mysql->query("DELETE FROM forums_permissions_general WHERE forumID IN (".implode(', ', $forums).") AND forumID != {$forumID}");
			// foreach ($forums as $cForumID)
			// if ($cForumID != $forumID)
			// $mysql->query("INSERT INTO forums_permissions_general SET forumID = {$cForumID}");
			// $mysql->query("UPDATE forums_permissions_general SET `read` = {$public}, `write` = 0, `editPost` = 0, `deletePost` = 0, `createThread` = 0, `deleteThread` = 0, `addPoll` = 0, `addRolls` = -1, `addDraws` = -1, `moderate` = -1 WHERE forumID = {$forumID}");
			// $hl_retired = new HistoryLogger('retired');
			// $hl_retired->addGame($gameID)->addForUsers($players)->addForCharacters($chars)->save();

			$gameCount = $mysql->query("SELECT gameID FROM players WHERE players.userID = {$currentUser->userID} AND players.isGM = TRUE");
			if (!$gameCount->rowCount()) {
				$currentUser->deleteUsermeta('isGM');
			}

			displayJSON(['success' => true]);
		} else {
			displayJSON(['failed' => true, 'errors' => ['notGM']]);
		}
	}

	public function unretire($gameID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = (int)$gameID;
		$gmCheck = $mysql->query("SELECT gameID FROM games WHERE gameID = {$gameID} AND gmID = {$currentUser->userID} LIMIT 1");
		if ($gmCheck->rowCount()) {
			$mysql->query("UPDATE games SET retired = null, status = FALSE WHERE gameID = {$gameID} LIMIT 1");

			$currentUser->updateUsermeta('isGM', true);

			displayJSON(['success' => true]);
		} else {
			displayJSON(['failed' => true, 'errors' => ['notGM']]);
		}
	}

	public function apply()
	{
		global $loggedIn, $currentUser;
		$mysql = DB::conn('mysql');

		if (!$loggedIn) {
			displayJSON(['failed' => true, 'loggedOut' => true]);
		}

		$gameID = intval($_POST['gameID']);
		$status = $mysql->query("SELECT status FROM games WHERE gameID = {$gameID} LIMIT 1")->fetchColumn();
		if ($status) {
			$mysql->query("INSERT INTO players SET gameID = {$gameID}, userID = {$currentUser->userID}");
			// $hl_playerApplied = new HistoryLogger('playerApplied');
			// $hl_playerApplied->addUser($currentUser->userID)->addGame($gameID)->save();
		} else {
			displayJSON(['failed' => true, 'gameClosed' => true]);
		}

		displayJSON(['success' => true]);
	}

	public function invite($gameID, $userID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = (int) $gameID;
		$userID = (int) $userID;

		$gmCheck = $mysql->query("SELECT gameID FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID} AND isGM = TRUE LIMIT 1");
		if ($gmCheck->rowCount()) {
			$userCheck = $mysql->query("SELECT users.userID, users.username, users.email, players.approved FROM users LEFT JOIN players ON users.userID = players.userID AND players.gameID = {$gameID} WHERE users.userID = {$userID} LIMIT 1");
			if (!$userCheck->rowCount()) {
				displayJSON([
					'failed' => true,
					'errors' => ['invalidUser']
				]);
			}
			$user = $userCheck->fetch();
			if ($user['approved'] != NULL) {
				displayJSON([
					'failed' => true,
					'errors' => ['alreadyInGame']
				]);
			}
			try {
				$mysql->query("INSERT INTO gameInvites SET gameID = {$gameID}, userID = {$userID}");
			} catch (Exception $e) {
				if ($inviteCheck->rowCount()) {
					displayJSON([
						'failed' => true,
						'errors' => ['alreadyInvited']
					]);
				}
			}
			$systems = Systems::getInstance();
			$gameInfo = $mysql->query("SELECT system, title FROM games WHERE gameID = {$gameID} LIMIT 1")->fetch();
			ob_start();
			include('emails/gameInviteEmail.php');
			$email = ob_get_contents();
			ob_end_clean();

			$mail = getMailObj();
			$mail->addAddress($user["email"]);
			$mail->Subject = "Game Invite";
			$mail->msgHTML($email);
			// foreach ($recips as $email) {
			// 	$mail->addBCC($email);
			// }
			$mail->send();

			// $hl_playerInvited = new HistoryLogger('playerInvited');
			// $hl_playerInvited->addUser($currentUser->userID, 'gm')->addUser($user['userID'])->addGame($gameID)->save();

			displayJSON([
				'success' => true,
				'user' => [
					'userID' => (int) $user['userID'],
					'username' => $user['username']
				]
			]);
		} else {
			displayJSON(['failed' => true, 'errors' => 'notGM']);
		}
	}

	public function removeInvite($gameID, $userID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = intval($gameID);
		$userID = intval($userID);
		$gmCheck = $mysql->query("SELECT gameID FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID} AND isGM = TRUE LIMIT 1");
		if ($gmCheck->rowCount() || $currentUser->userID == $userID) {
			$mysql->query("DELETE FROM gameInvites WHERE gameID = {$gameID} AND userID = {$userID} LIMIT 1");
			// $hl_inviteRemoved = new HistoryLogger('invite'.ucwords($pathOptions[1]).($pathOptions[1] == 'withdraw'?'n':'d'));
			// if ($pathOptions[1] == 'withdraw')
			// 	$hl_inviteRemoved->addUser($currentUser->userID, 'gm');
			// $hl_inviteRemoved->addUser($userID)->addGame($gameID)->save();
			displayJSON(['success' => true, 'userID' => (int) $userID]);
		} else {
			displayJSON(['failed' => true, 'errors' => 'noPermission']);
		}
	}

	public function acceptInvite($gameID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = (int) $gameID;
		$inviteCheck = $mysql->query("SELECT gameID FROM gameInvites WHERE gameID = {$gameID} AND userID = {$currentUser->userID} LIMIT 1");
		if ($inviteCheck->rowCount()) {
			$mysql->query("INSERT INTO players SET gameID = {$gameID}, userID = {$currentUser->userID}, approved = 1");
			$mysql->query("DELETE FROM gameInvites WHERE gameID = {$gameID} AND userID = {$currentUser->userID} LIMIT 1");
			$groupID = $mysql->query("SELECT groupID FROM games WHERE gameID = {$gameID} LIMIT 1")->fetchColumn();
			$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$groupID}, userID = {$currentUser->userID}");
			displayJSON(['success' => true, 'userID' => (int) $userID]);
		} else {
			displayJSON(['failed' => true, 'errors' => 'noPermission']);
		}
	}

	public function submitCharacter($gameID, $characterID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');

		$characterID = (int) $characterID;
		$gameID = (int) $gameID;
		$playerCheck = $mysql->query("SELECT isGM FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID} AND approved = TRUE LIMIT 1");
		if (!$playerCheck->rowCount()) {
			displayJSON(['failed' => true, 'errors' => ['notPlayer']]);
		}
		$isGM = (bool) $playerCheck->fetchColumn();

		$charCheck = $mysql->query("SELECT name, label, gameID FROM characters WHERE characterID = {$characterID} AND userID = {$currentUser->userID} LIMIT 1");
		if (!$charCheck->rowCount()) {
			displayJSON(['failed' => true, 'errors' => ['notOwner']]);
		}

		$charDetails = $charCheck->fetch();
		if ($charDetails['gameID']) {
			displayJSON(['failed' => true, 'errors' => ['alreadyInGame']]);
		} else {
			$approved = $isGM ? 1 : 0;
			$mysql->query("UPDATE characters SET gameID = {$gameID}, approved = {$approved} WHERE characterID = {$characterID} LIMIT 1");

			$gmEmails = $mysql->query("SELECT u.email FROM users u INNER JOIN usermeta m ON u.userID = m.userID INNER JOIN players ON u.userID = players.userID WHERE players.gameID = {$gameID} AND players.isGM = 1 AND m.metaKey = 'gmMail' AND m.metaValue = 1")->fetchAll(PDO::FETCH_COLUMN);
			if (sizeof($gmEmails)) {
				$emailDetails = new stdClass();
				$emailDetails->action = 'Character Added';
				$emailDetails->gameInfo = (object)$game;
				$charLabel = strlen($charDetails['name']) ? $charDetails['name'] : $charDetails['label'];
				$systems = Systems::getInstance();
				$site_url = getenv('APP_URL');
				$emailDetails->message = "<a href=\"https://{$site_url}/user/{$currentUser->userID}/\" class=\"username\">{$currentUser->username}</a> applied a new character to your game: <a href=\"https://{$site_url}/characters/{$characterID}/\">{$charLabel}</a>.";
				ob_start();
				include('emails/gmEmail.php');
				$email = ob_get_contents();
				ob_end_clean();

				$mail = getMailObj();
				foreach ($gmEmails as $email) {
					$mail->addAddress($email);
				}
				$mail->Subject = "Game Activity: {$emailDetails->action}";
				$mail->msgHTML($email);
				$mail->send();
			}

			displayJSON(['success' => true, 'character' => $charDetails, 'approved' => $isGM]);
		}
	}

	public function removeCharacter($gameID, $characterID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');

		$pendingAction = 'removed';
		$gameID = (int) $gameID;
		$characterID = (int) $characterID;

		$charInfo = $mysql->query("SELECT userID, gameID FROM characters WHERE characterID = {$characterID} LIMIT 1")->fetch();
		$gmCheck = $mysql->query("SELECT isGM FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID} LIMIT 1");
		if ((int) $charInfo['userID'] != $currentUser->userID && !$gmCheck->rowCount()) {
			displayJSON(['failed' => true, 'errors' => 'badAuthentication']);
		}

		$mysql->query("UPDATE characters SET gameID = NULL, approved = 0 WHERE characterID = {$characterID} LIMIT 1");
		if ($charInfo['user']['userID'] == $currentUser->userID) {
			$pendingAction = 'withdrawn';
		} elseif (!$charInfo['approved']) {
			$pendingAction = 'rejected';
		}
		// $hl_charRemoved = new HistoryLogger('character'.ucwords($pendingAction));
		// $hl_charRemoved->addCharacter($characterID);
		// if ($pendingAction != 'withdrawn')
		// 	$hl_charRemoved->addUser($currentUser->userID, 'gm');
		// $hl_charRemoved->addUser($charInfo['userID'])->addGame($gameID)->save();

		displayJSON(['success' => true, 'action' => $pendingAction, 'characterID' => $characterID]);
	}

	public function approveCharacter($gameID, $characterID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = (int) $gameID;
		$characterID = (int) $characterID;

		$charInfo = $mysql->query("SELECT userID, gameID FROM characters WHERE characterID = {$characterID} LIMIT 1")->fetch();
		$gmCheck = $mysql->query("SELECT isGM FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID} LIMIT 1");
		if (!$charInfo || (int) $charInfo['gameID'] != $gameID || !$gmCheck->rowCount()) {
			displayJSON(['failed' => true, 'errors' => 'badAuthentication']);
		}

		$mysql->query("UPDATE characters SET approved = 1 WHERE characterID = {$characterID} LIMIT 1");
		// $hl_charApproved = new HistoryLogger('characterApproved');
		// $hl_charApproved->addCharacter($characterID)->addUser($currentUser->userID, 'gm')->addGame($gameID)->save();

		displayJSON(['success' => true, 'action' => 'characterApproved', 'characterID' => $characterID]);
	}

	public function getLFG()
	{
		$mysql = DB::conn('mysql');

		$lfgCount = intval($_POST['lfgCount']) > 0 ? intval($_POST['lfgCount']) : 10;
		$rLFGs = $mysql->query("SELECT name, lfg AS count FROM systems WHERE lfg > 0 ORDER BY lfg DESC, sortName LIMIT {$lfgCount}")->fetchAll();
		$lfgs = [];
		foreach ($rLFGs as $rLFG) {
			$lfgs[] = ['name' => $rLFG['name'], 'count' => (int)$rLFG['lfg']];
		}

		displayJSON(['success' => true, 'lfgs' => $lfgs]);
	}

	public function toggleFavorite() {
		global $currentUser;
		$mysql = DB::conn('mysql');

		$gameID = (int) $_POST['gameID'];
		try {
			$mysql->query("INSERT INTO games_favorites SET userID = {$currentUser->userID}, gameID = {$gameID}");
		} catch (Exception $e) {
			if (str_contains($e->getMessage(), 'Integrity constraint violation: 1062')) {
				$mysql->query("DELETE FROM games_favorites WHERE userID = {$currentUser->userID} AND gameID = {$gameID}");
			}
		}

		displayJSON(['success' => true]);
	}
}
