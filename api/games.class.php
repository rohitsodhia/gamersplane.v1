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
		$mongo = DB::conn('mongo');

		$myGames = false;
		if (isset($_GET['my']) && $_GET['my']) {
			$myGames = true;
			$rGames = $mongo->games->find(
				[
					'players' => [
						'$elemMatch' => [
							'user.userID' => $currentUser->userID,
							'approved' => true
						]
					]
				],
				['projection' => [
					'gameID' => true,
					'title' => true,
					'system' => true,
					'gm' => true,
					'status' => true,
					'players' => true,
					'customType' => true,
					'retired'=>true,
					'forumID'=>true
				]]
			)->toArray();

			$rfavouriteGames = array_column(iterator_to_array($mongo->gameFavorites->find(
				['userID' => $currentUser->userID],
				['projection'=>['gameID'=>true, '_id'=>false]]
				),false),'gameID');

			foreach ($rGames as &$gameCheck) {
				$gameCheck['isFavorite']=in_array($gameCheck['gameID'], $rfavouriteGames);
			}
		} else {
			$findParams = [
				//'players.user.userID' => ['$ne' => $currentUser->userID],
				//'status' => 'open',
				'retired' => null
			];
			if ($_GET['systems']) {
				$systems = $_GET['systems'];
				if (is_string($systems)) {
					$systems = explode(',', $systems);
				}
				$findParams['system'] = array('$in' => $systems);
			}
			$gameSearchOptions = ['projection' => [
				'gameID' => true,
				'title' => true,
				'system' => true,
				'gm' => true,
				'start' => true,
				'numPlayers' => true,
				'status' => true,
				'players' => true,
				'customType' => true,
				'public'=>true,
				'forumID'=>true,
			]];
			if (isset($_GET['sort'])) {
				$gameSearchOptions['sort'] = [$_GET['sort'] => !isset($_GET['sortOrder']) || $_GET['sortOrder'] == 1 ? 1 : -1];
			}
			$rGames = $mongo->games->find(
				$findParams,
				$gameSearchOptions
			);
			if (!isset($_GET['hideInactive']) || !$_GET['hideInactive'])
				$inactiveGMs = $mysql->query("SELECT u.userID FROM users u INNER JOIN usermeta um ON u.userID = um.userID AND um.metaKey = 'isGM' AND um.metaValue = 1 WHERE u.lastActivity < NOW() - INTERVAL 14 DAY")->fetchAll(PDO::FETCH_COLUMN);
		}
		$showFullGames = isset($_GET['showFullGames']) && $_GET['showFullGames'] === 'true';
		$limit = isset($_GET['limit']) && (int)$_GET['limit'] > 0 ? (int)$_GET['limit'] : null;
		$games = [];
		$gms = [];
		$count = 0;
		$systems = Systems::getInstance();
		foreach ($rGames as $game) {
			if (isset($inactiveGMs) && in_array($game['gm']['userID'], $inactiveGMs)) {
				continue;
			}
			$game['system'] = $systems->getFullName($game['system']);
			$game['isGM'] = false;
			$game['isRetired'] = $game['retired']!=null;
			unset($game['retired']);
			$game['playerCount'] = -1;
			foreach ($game['players'] as $player) {
				if ($player['user']['userID'] == $currentUser->userID) {
					$game['isGM'] = $player['isGM'];
				}
				if ($player['approved']) {
					$game['playerCount']++;
				}
			}
			if (!$myGames && !$showFullGames && $game['playerCount'] >= $game['numPlayers']) {
				continue;
			}
			if ($game['start']) {
				$game['start'] = getMongoSeconds($game['start']);
			}
			unset($game['players']);
			$games[] = $game;
			$gms[] = $game['gm']['userID'];
			if ($limit != null) {
				$count++;
				if ($count == $limit) {
					break;
				}
			}
		}
		if (sizeof($gms)) {
			$gms = array_unique($gms);
			$rUsers = $mysql->query("SELECT userID, lastActivity FROM users WHERE userID IN (" . implode(', ', $gms) . ")")->fetchAll();
			$users = [];
			foreach ($rUsers as $user) {
				$users[$user['userID']] = strtotime($user['lastActivity']);
			}
			foreach ($games as &$game) {
				$game['gm']['lastActivity'] = $users[$game['gm']['userID']];
			}
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
		$gameInfo = $mysql->query("SELECT games.gameID, games.title, games.customSystem, games.system, gm.userID, gm.username, gm.lastActivity, games.created, games.start, games.end, games.postFrequency, games.numPlayers, games.charsPerPlayer, games.description, games.charGenInfo, games.forumID, games.groupID, games.status, games.public, games.retired, games.allowedCharSheets, games.gameOptions, games.recruitmentThreadId INNER JOIN users gm ON games.gmID = gm.userID WHERE games.gameID = {$gameID} LIMIT 1");
		if (!$gameInfo->rowCount()) {
			displayJSON(['failed' => true, 'noGame' => true]);
		}
		$gameInfo['readPermissions'] = $mysql->query("SELECT `read` FROM forums_permissions_general WHERE forumID = {$gameInfo['forumID']} LIMIT 1")->fetchColumn();
		$gameInfo['readPermissions'] = (bool)$gameInfo['readPermissions'];
		$gameInfo['gm'] = [
			'userID' => $gameInfo['userID'],
			'username' => $gameInfo['username'],
			'lastActivity' => $gameInfo['lastActivity']
		];
		unset($gameInfo['userID'], $gameInfo['username'], $gameInfo['lastActivity']);
		$gameInfo['title'] = printReady($gameInfo['title'], ['nl2br']);
		$gameInfo['created'] = date('F j, Y g:i a', getMongoSeconds($gameInfo['created']));
		$gameInfo['description'] = strlen($gameInfo['description']) ? $gameInfo['description'] : 'None Provided';
		$gameInfo['charGenInfo'] = strlen($gameInfo['charGenInfo']) ? $gameInfo['charGenInfo'] : 'None Provided';
		$gameInfo['approvedPlayers'] = 0;
		$players = $mysql->query("SELECT users.userID, users.username, players.approved, players.isGM FROM players INNER JOIN users ON players.userID = users.userID WHERE players.gameID = {$gameID}")
		$gameInfo['players'] = [];
		foreach ($players->fetchAll() as $player) {
			$gameInfo['players'][] = [
				'user' => [
					'userID' => $player['userID'],
					'username' => printReady($player['username'])
					]
				'approved' => $player['approved'],
				'isGM' => $player['isGM'],
				'primaryGM' => $player['userID'] == $gameInfo['gm']['userID']
			];
			if ($player['approved'] && $player['userID'] != $gameInfo['gm']['userID']) {
				$gameInfo['approvedPlayers']++;
			}
		}

		$getDecks = $mysql->query("SELECT deckID, label, deck, position FROM decks WHERE gameID = {$gameID}");
		$decks = [];
		if ($getDecks->rowCount()) {
			foreach ($getDecks->fetchAll() as $deck) {
				$decks[] = [
					'deckID' => $deck['deckID'],
					'type' => $deck['type'],
					'label' => $deck['label'],
					'cardsRemaining' => sizeof($deck['deck']) - $deck['position'] + 1

				]
			}
		}

		$getPlayers = $mysql->query("SELECT players.userID, user.username, players.approved, players.isGM FROM players INNER JOIN users ON players.userID = users.userID WHERE gameID = {$gameID}");
		$players = [];
		$playerIDs = [];
		foreach ($getPlayers->fetchAll() as $player) {
			$players[] = [
				'user' => [
					'userID' => $player['userID'],
					'username' => $player['username'],
				],
				'approved' => $player['approved'],
				'isGM' => $player['isGM'],
				'characters' => []
			];
			$playerIDs[] = $player['userID'];
		}
		$getCharacters = $mysql->query("SELECT charcterID, userID, label, approved FROM characters WHERE userID IN (". implode(', ', $playerIDs) .")")
		$characters = [];
		foreach ($getCharacters->fetchAll() as $character) {
			$userID = $character['userID'];
			unset($character['userID']);
			$characters[$userID][] = $character;
		}
		foreach ($players as &$player) {
			if (isset($characters[$player['userID']])) {
				$player['characters'] = $characters[$player['userID']];
			}
		}
		displayJSON([
			'success' => true,
			'details' => $gameInfo,
			'players' => $players,
			'invites' => is_countable($gameInfo['invites']) && sizeof($gameInfo['invites']) ? $gameInfo['invites'] : [],
			'decks' => $decks
		]);
	}

	public function createGame()
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');
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
				$details['allowedCharSheets'][] = $system['_id'];
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
		if($_POST['system']=="custom"){
			$details['customType'] = sanitizeString($_POST['customType']);
		}

		$gameOptions=trim($_POST['gameOptions']?:"");
		$gameOptions=str_replace(array("‘","’","“","”"), array("'", "'", '"', '"'), $gameOptions);
		$jsonTest = json_decode($gameOptions);
		if ($gameOptions=="" || json_last_error() === 0) {
			// JSON is valid
			$details['gameOptions']=$gameOptions;
		}

		$details['status'] = false;
		$details['public'] = true;

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
			$details['gm'] = ['userID' => $currentUser->userID, 'username' => $currentUser->username];
			$details['created'] = genMongoDate();
			$details['start'] = $details['created'];

			$system = $details['system'];
			$details['gameID'] = mongo_getNextSequence('gameID');
			$gameID = $details['gameID'];
			$details['players'] = [[
				'user' => $details['gm'],
				'approved' => true,
				'isGM' => true
			]];
			$details['decks'] = [];

			$forumInfo = $mysql->query('SELECT MAX(`order`) + 1 AS newOrder FROM forums WHERE parentID = 2');
			list($order) = $forumInfo->fetch(PDO::FETCH_NUM);
			$addForum = $mysql->prepare("INSERT INTO forums (title, parentID, heritage, `order`, gameID) VALUES (:title, 2, " . mt_rand(0, 9999) . ", {$order}, {$gameID})");
			$addForum->execute([':title' => $details['title']]);
			$forumID = $mysql->lastInsertId();
			$heritage = sql_forumIDPad(2) . '-' . sql_forumIDPad($forumID);
			$mysql->query("UPDATE forums SET heritage = '{$heritage}' WHERE forumID = {$forumID}");
			$details['forumID'] = (int)$forumID;

			$addForumGroup = $mysql->prepare("INSERT INTO forums_groups (name, ownerID, gameID) VALUES (:title, {$currentUser->userID}, {$gameID})");
			$addForumGroup->execute(['title' => $details['title']]);
			$groupID = $mysql->lastInsertId();
			$details['groupID'] = (int)$groupID;

			$mysql->query('INSERT INTO forums_groupMemberships (groupID, userID) VALUES (' . $groupID . ', ' . $currentUser->userID . ')');

			$mysql->query('INSERT INTO forumAdmins (userID, forumID) VALUES(' . $currentUser->userID . ', ' . $forumID . ')');
			$mysql->query('INSERT INTO forums_permissions_groups (`groupID`, `forumID`, `read`, `write`, `editPost`, `createThread`, `deletePost`, `addRolls`, `addDraws`) VALUES (' . $groupID . ', ' . $forumID . ', 2, 2, 2, 2, 2, 2, 2)');
			$mysql->query("INSERT INTO forums_permissions_general SET forumID = $forumID");

			$mongo->games->insertOne($details);
			#				$hl_gameCreated = new HistoryLogger('gameCreated');
			#				$hl_gameCreated->addGame($gameID)->save();

			$currentUser->updateUsermeta('isGM', true);

			$lfgRecips = $mongo->users->find(['lfg' => $details['system']], ['projection' => ['userID' => true]])->toArray();
			if (count($lfgRecips)) {
				$userIDs = [];
				foreach ($lfgRecips as $recip) {
					$userIDs[] = $recip['userID'];
				}
				$lfgRecips = $mysql->query("SELECT u.email FROM users u LEFT JOIN usermeta um ON u.userID = um.userID AND um.metaKey = 'newGameMail' WHERE u.userID IN (" . implode(', ', $userIDs) . ") AND um.metaValue != 0");
				$recips = [];
				foreach ($lfgRecips as $info) {
					$recips[] = $info['email'];
				}
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
		$details['allowedCharSheets'] = $_POST['allowedCharSheets'];
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

		if($_POST['system']=="custom"){
			$details['customType'] = sanitizeString($_POST['customType']);
		} else {
			$details['customType'] = null;
		}

		$gameOptions = trim($_POST['gameOptions'] ?: "");
		$gameOptions = str_replace(["‘", "’", "“", "”"], ["'", "'", '"', '"'], $gameOptions);
		$jsonTest = json_decode($gameOptions);
		if ($gameOptions == "" || json_last_error() === 0) {
			// JSON is valid
			$details['gameOptions'] = $gameOptions;
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
			foreach (array_keys($details) as $key) {
				$setVars[] = "{$key} = :{$key}";
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
		$gmCheck = $mysql->query("SELECT gameID FROM games WHERE gameID = {$gameID} AND gmID = {$currentUser->userID} LIMIT 1");
		if ($gmCheck->rowCount()) {
			$mysql->query("UPDATE forums_permissions_general SET `read` = `read` ^ 1 WHERE forumID = {$gameInfo['forumID']}");
			$mysql->query("UPDATE games SET public = NOT public WHERE gameID = {$gameID}");
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
			$mysql->query("UPDATE games SET status = NOT status WHERE gameID = {$gameID}");
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

		$gameID = (int)$gameID;
		$userID = (int)$userID;
					if ($user == strtolower($player['user']['userID'])) {
				displayJSON([
					'failed' => true,
					'errors' => ['alreadyInGame']
				]);
			}

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
			$inviteCheck = $mysql->query("SELECT gameID FROM invites WHERE gameID = {$gameID} AND userID = {$userID} LIMIT 1");
			if ($inviteCheck) {
				displayJSON([
					'failed' => true,
					'errors' => ['alreadyInvited']
				]);
			}
			$mysql->query("INSERT INTO invites SET gameID = {$gameID}, userID = {$userID}");
			$systems = Systems::getInstance();
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
			$mysql->query("DELETE FROM invites WHERE gameID = {$gameID} AND userID = {$userID} LIMIT 1");
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
		$inviteCheck = $mysql->query("SELECT gameID FROM invites WHERE gameID = {$gameID} AND userID = {$currentUser->userID} LIMIT 1");
		if ($inviteCheck->rowCount()) {
			$mysql->query("INSERT INTO players SET gameID = {$gameID}, userID = {$currentUser->userID}, approved = 1");
			$mysql->query("DELETE FROM invites WHERE gameID = {$gameID} AND userID = {$currentUser->userID} LIMIT 1");
			$groupID = $mysql->query("SELECT groupID FROM games WHERE gameID = {$gameID} LIMIT 1")->fetchColumn();
			$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$game['groupID']}, userID = {$userID}");
			// $hl_inviteAccepted = new HistoryLogger('inviteAccepted');
			// $hl_inviteAccepted->addUser($userID)->addGame($gameID)->save();
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
		$playerCheck = $mysql->query("SELECT gameID FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID} AND approved = TRUE LIMIT 1");
		if (!$playerCheck->rowCount()) {
			displayJSON(['failed' => true, 'errors' => ['notPlayer']]);
		}

		$charCheck = $mysql->query("SELECT name, label, gameID FROM characters WHERE characterID = {$characterID} AND userID = {$currentUser->userID} LIMIT 1");
		if (!$charCheck->rowCount()) {
			displayJSON(['failed' => true, 'errors' => ['notOwner']]);
		}

		$charDetails = $charCheck->fetch();
		if ($charDetails['gameID']) {
			displayJSON(['failed' => true, 'errors' => ['alreadyInGame']]);
		} else {
			$mysql->query("UPDATE characters SET gameID = {$gameID} WHERE characterID = {$characterID} LIMIT 1");
			// $hl_charApplied = new HistoryLogger('characterApplied');
			// $hl_charApplied->addUser($currentUser->userID)->addCharacter($characterID)->addGame($gameID)->save();
			// if ($isGM) {
			// 	$hl_charApproved = new HistoryLogger('characterApproved');
			// 	$hl_charApproved->addUser($currentUser->userID, 'gm')->addUser($currentUser->userID)->addCharacter($characterID)->addGame($gameID)->save();
			// }

			$gmEmails = $mysql->query("SELECT u.email FROM users u INNER JOIN usermeta m ON u.userID = m.userID INNER JOIN players ON u.userID = players.userID WHERE players.gameID = {$gameID} AND players.isGM = 1 AND m.metaKey = 'gmMail' AND m.metaValue = 1")->fetchAll(PDO::FETCH_COLUMN);
			if (sizeof($gmEmails)) {
				$emailDetails = new stdClass();
				$emailDetails->action = 'Character Added';
				$emailDetails->gameInfo = (object)$game;
				$charLabel = strlen($charDetails['name']) ? $charDetails['name'] : $charInfo['label'];
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

			displayJSON(['success' => true, 'character' => $charInfo, 'approved' => $isGM]);
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
		if ($charInfo['userID'] != $currentUser->userID && !$gmCheck->rowCount()) {
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
		if (!$charInfo || $charInfo['gameID'] != $gameID || $gmCheck->rowCount()) {
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

		displayJSON(['success' => true, 'state' => $state]);
	}
}
