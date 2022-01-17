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
		} elseif ($pathOptions[0] == 'apply') {
			$this->apply();
		} elseif (
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
			);
		} else {
			$findParams = [
				'players.user.userID' => ['$ne' => $currentUser->userID],
				'status' => 'open',
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
				'customType' => true
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
		$mongo = DB::conn('mongo');

		$gameID = intval($gameID);
		if (!$gameID) {
			displayJSON(['failed' => true]);
		}
		$gameInfo = $mongo->games->findOne(['gameID' => $gameID]);
		if (!$gameInfo) {
			displayJSON(['failed' => true, 'noGame' => true]);
		}
		$gameInfo['readPermissions'] = $mysql->query("SELECT `read` FROM forums_permissions_general WHERE forumID = {$gameInfo['forumID']} LIMIT 1")->fetchColumn();
		$gameInfo['readPermissions'] = (bool)$gameInfo['readPermissions'];
		$gameInfo['gm']['lastActivity'] = User::inactive($mysql->query("SELECT lastActivity FROM users WHERE userID = {$gameInfo['gm']['userID']} LIMIT 1")->fetchColumn());
		$gameInfo['title'] = printReady($gameInfo['title'], ['nl2br']);
		$gameInfo['created'] = date('F j, Y g:i a', getMongoSeconds($gameInfo['created']));
		$gameInfo['description'] = strlen($gameInfo['description']) ? $gameInfo['description'] : 'None Provided';
		$gameInfo['charGenInfo'] = strlen($gameInfo['charGenInfo']) ? $gameInfo['charGenInfo'] : 'None Provided';
		$gameInfo['approvedPlayers'] = 0;
		foreach ($gameInfo['players'] as &$player) {
			$player['user']['username'] = printReady($player['user']['username']);
			$player['primaryGM'] = $player['user']['userID'] == $gameInfo['gm']['userID'];
			if ($player['approved'] && !$player['primaryGM']) {
				$gameInfo['approvedPlayers']++;
			}
		}

		$decks = $gameInfo['decks'];
		unset($gameInfo['decks']);
		if (is_array($decks) && sizeof($decks)) {
			foreach ($decks as &$deck) {
				$deck = [
					'deckID' => $deck['deckID'],
					'type' => $deck['type'],
					'label' => $deck['label'],
					'cardsRemaining' => sizeof($deck['deck']) - $deck['position'] + 1
				];
			}
		} else {
			$decks = [];
		}

		$players = $gameInfo['players'];
		$rCharacters = $mongo->characters->find(
			['game.gameID' => $gameID],
			['projection' => [
				'characterID' => true,
				'user' => true,
				'label' => true,
				'system' => true,
				'game' => true
			]]
		);
		$characters = [];
		foreach ($rCharacters as $character) {
			$userID = $character['user']['userID'];
			if (!isset($characters[$userID])) {
				$characters[$userID] = [];
			}
			$character['approved'] = $character['game']['approved'];
			unset($character['_id'], $character['user'], $character['game']);
			$characters[$userID][] = $character;
		}
		foreach ($players as &$player) {
			if (isset($characters[$player['user']['userID']])) {
				$player['characters'] = $characters[$player['user']['userID']];
			} else {
				$player['characters'] = [];
			}
		}
		unset($gameInfo['players']);
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
		if (!is_array($_POST['allowedCharSheets']) || sizeof($_POST['allowedCharSheets']) == 0) {
			$errors[] = 'noCharSheets';
		} else {
			$validCharSheets = $mongo->systems->find(
				[
					'_id' => ['$in' => $_POST['allowedCharSheets']],
					'hasCharSheet' => true
				],
				['projection' => ['_id' => true]]
			);
			foreach ($validCharSheets as $system) {
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

		$details['status'] = 'closed';
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
				mail('Gamers Plane <contact@gamersplane.com>', "New {$systems->getFullName($system)} Game: {$details['title']}", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>\r\nBcc: " . implode(', ', $recips));
			}

			displayJSON(['success' => true, 'gameID' => (int)$gameID]);
		}
	}

	public function updateGame()
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');

		$gameID = intval($_POST['gameID']);
		$gameInfo = $mongo->games->findOne(
			['gameID' => $gameID],
			['projection' => ['forumID' => true, 'public' => true, 'players' => true]]
		);
		$isGM = false;
		foreach ($gameInfo['players'] as $player) {
			if ($currentUser->userID == $player['user']['userID'] && $player['isGM']) {
				$isGM = true;
				break;
			}
		}
		if (!$isGM) {
			displayJSON(['unauthorized' => true]);
		}

		$details['title'] = sanitizeString($_POST['title']);
		$details['allowedCharSheets'] = $_POST['allowedCharSheets'];
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
		$details['customType'] = sanitizeString($_POST['customType']);

		$gameOptions=trim($_POST['gameOptions']?:"");
		$gameOptions=str_replace(array("‘","’","“","”"), array("'", "'", '"', '"'), $gameOptions);
		$jsonTest = json_decode($gameOptions);
		if ($gameOptions=="" || json_last_error() === 0) {
			// JSON is valid
			$details['gameOptions']=$gameOptions;
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

		if (sizeof($errors)) {
			displayJSON(array('failed' => true, 'errors' => $errors));
		} else {
			$mongo->games->updateOne(['gameID' => $gameID], ['$set' => $details]);
			$mongo->characters->updateOne(['game.gameID' => $gameID], ['$set' => ['game.title' => $details['title']]]);
			$updateForumTitle = $mysql->prepare('UPDATE forums SET title = :title WHERE forumID = :forumID');
			$updateForumTitle->execute(['title' => $details['title'], 'forumID' => $gameInfo['forumID']]);
			// $hl_gameEdited = new HistoryLogger('gameEdited');
			// $hl_gameEdited->addGame($gameID)->save();

			displayJSON(['success' => true, 'gameID' => (int) $gameID]);
		}
	}

	public function toggleForum($gameID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');

		$gameID = (int)$gameID;
		$gameInfo = $mongo->games->findOne(
			['gameID' => $gameID],
			['projection' => ['forumID' => true, 'public' => true, 'players' => true]]
		);
		$isGM = false;
		foreach ($gameInfo['players'] as $player) {
			if ($currentUser->userID == $player['user']['userID'] && $player['isGM']) {
				$isGM = true;
				break;
			}
		}
		if ($isGM) {
			$mysql->query("UPDATE forums_permissions_general SET `read` = `read` ^ 1 WHERE forumID = {$gameInfo['forumID']}");
			$mongo->games->updateOne(['gameID' => $gameID], ['$set' => ['public' => !$gameInfo['public']]]);
			displayJSON(['success' => true]);
		} else {
			displayJSON(['failed' => true, 'errors' => 'notGM']);
		}
	}


	public function toggleGameStatus($gameID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');

		$gameID = (int)$gameID;
		$gameInfo = $mongo->games->findOne(['gameID' => $gameID], ['projection' => ['forumID' => true, 'status' => true, 'players' => true]]);
		$isGM = false;
		foreach ($gameInfo['players'] as $player) {
			if ($currentUser->userID == $player['user']['userID'] && $player['isGM']) {
				$isGM = true;
				break;
			}
		}
		if ($isGM) {
			$mongo->games->updateOne(
				['gameID' => $gameID],
				['$set' => ['status' => $gameInfo['status'] == 'open' ? 'closed' : 'open']]
			);
			displayJSON(['success' => true]);
		} else {
			displayJSON(['failed' => true, 'errors' => 'notGM']);
		}
	}

	public function retire($gameID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');

		$gameID = (int)$gameID;
		extract($mongo->games->findOne(['gameID' => $gameID], ['projection' => ['gm' => true, 'forumID' => true, 'groupID' => true, 'public' => true, 'players' => true]]));
		$gmID = (int)$gm['userID'];
		if ($currentUser->userID == $gmID) {
			$mongo->games->updateOne(['gameID' => $gameID], ['$set' => ['retired' => genMongoDate(), 'status' => 'closed']]);
			// $groups = $mysql->query("DELETE FROM forums_permissions_group WHERE groupID = {$groupID}");
			// $forums = $mysql->query("SELECT forumID FROM forums WHERE gameID = {$gameID}")->fetchAll(PDO::FETCH_COLUMN);
			// $mysql->query("DELETE FROM forums_permissions_users WHERE forumID IN (".implode(', ', $forums).")");
			// $mysql->query("DELETE FROM forumAdmins WHERE forumID IN (".implode(', ', $forums).")");
			// $mysql->query("DELETE FROM forums_permissions_general WHERE forumID IN (".implode(', ', $forums).") AND forumID != {$forumID}");
			// foreach ($forums as $cForumID)
			// if ($cForumID != $forumID)
			// $mysql->query("INSERT INTO forums_permissions_general SET forumID = {$cForumID}");
			// $mysql->query("UPDATE forums_permissions_general SET `read` = {$public}, `write` = 0, `editPost` = 0, `deletePost` = 0, `createThread` = 0, `deleteThread` = 0, `addPoll` = 0, `addRolls` = -1, `addDraws` = -1, `moderate` = -1 WHERE forumID = {$forumID}");
			#				$hl_retired = new HistoryLogger('retired');
			#				$hl_retired->addGame($gameID)->addForUsers($players)->addForCharacters($chars)->save();

			$gameCount = $mongo->games->count(['gm.userID' => $currentUser->userID, 'retired' => null]);
			if ($gameCount == 0) {
				$currentUser->deleteUsermeta('isGM');
			}

			displayJSON(['success' => true]);
		} else {
			displayJSON(['failed' => true, 'errors' => ['notGM']]);
		}
	}

	public function apply()
	{
		global $loggedIn, $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');

		if (!$loggedIn) {
			displayJSON(['failed' => true, 'loggedOut' => true]);
		}

		$gameID = intval($_POST['gameID']);
		$status = $mongo->games->findOne(['gameID' => $gameID], ['projection' => ['status' => true]]);
		if ($status['status'] == 'open') {
			$mongo->games->updateOne(['gameID' => $gameID], [
				'$push' => [
					'players' => [
						'user' => [
							'userID' => (int)$currentUser->userID,
							'username' => $currentUser->username
						],
						'approved' => false,
						'isGM' => false
					]
				]
			]);
			#				$hl_playerApplied = new HistoryLogger('playerApplied');
			#				$hl_playerApplied->addUser($currentUser->userID)->addGame($gameID)->save();
		} else {
			displayJSON(['failed' => true, 'gameClosed' => true]);
		}

		displayJSON(['success' => true]);
	}

	public function invite($gameID, $userID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');

		$gameID = intval($gameID);
		$gameInfo = $mongo->games->findOne(
			['gameID' => $gameID],
			['projection' => [
				'title' => true,
				'system' => true,
				'players' => true,
				'invites' => true
			]]
		);
		$isGM = false;
		foreach ($gameInfo['players'] as $player) {
			if ($currentUser->userID == $player['user']['userID']) {
				if ($player['isGM']) {
					$isGM = true;
				}
			}
			if ($user == strtolower($player['user']['userID'])) {
				displayJSON([
					'failed' => true,
					'errors' => ['alreadyInGame']
				]);
			}
		}
		if ($isGM) {
			$userCheck = $mysql->prepare("SELECT userID, username, email FROM users WHERE userID = :userID LIMIT 1");
			$userCheck->execute(array(':userID' => $userID));
			if (!$userCheck->rowCount()) {
				displayJSON([
					'failed' => true,
					'errors' => ['invalidUser']
				]);
			}
			$user = $userCheck->fetch();
			if (isset($gameInfo['invites'])) {
				foreach ($gameInfo['invites'] as $invite) {
					if ($currentUser->userID == $invite['user']['userID']) {
						displayJSON([
							'failed' => true,
							'errors' => ['alreadyInvited']
						]);
					}
				}
			}
			$mongo->games->updateOne(
				['gameID' => $gameID],
				['$push' => [
					'invites' => [
						'userID' => (int)$user['userID'],
						'username' => $user['username']
					]
				]]
			);
			$systems = Systems::getInstance();
			ob_start();
			include('emails/gameInviteEmail.php');
			$email = ob_get_contents();
			ob_end_clean();
			@mail($user['email'], "Game Invite", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>");
			#				$hl_playerInvited = new HistoryLogger('playerInvited');
			#				$hl_playerInvited->addUser($currentUser->userID, 'gm')->addUser($user['userID'])->addGame($gameID)->save();
			displayJSON([
				'success' => true,
				'user' => [
					'userID' => (int)$user['userID'],
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
		$mongo = DB::conn('mongo');

		$gameID = intval($gameID);
		$userID = intval($userID);
		$game = $mongo->games->findOne(
			['gameID' => $gameID],
			['projection' => [
				'players' => true,
				'invites' => true
			]]
		);
		$isGM = false;
		foreach ($game['players'] as $player) {
			if ($player['user']['userID'] == $currentUser->userID && $player['isGM']) {
				$isGM = true;
				break;
			}
		}
		if ($isGM || $currentUser->userID == $userID) {
			$mongo->games->updateOne(
				['gameID' => $gameID],
				[
					'$pull' => [
						'invites' => ['userID' => $userID]
					]
				]
			);
			#				$hl_inviteRemoved = new HistoryLogger('invite'.ucwords($pathOptions[1]).($pathOptions[1] == 'withdraw'?'n':'d'));
			#				if ($pathOptions[1] == 'withdraw')
			#					$hl_inviteRemoved->addUser($currentUser->userID, 'gm');
			#				$hl_inviteRemoved->addUser($userID)->addGame($gameID)->save();
			displayJSON(['success' => true, 'userID' => (int)$userID]);
		} else {
			displayJSON(['failed' => true, 'errors' => 'noPermission']);
		}
	}

	public function acceptInvite($gameID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');

		$gameID = intval($gameID);
		$userID = (int)$currentUser->userID;
		$game = $mongo->games->findOne(['gameID' => $gameID, 'invites.userID' => $currentUser->userID], ['projection' => ['groupID' => true]]);
		if ($game) {
			$mongo->games->updateOne(
				['gameID' => $gameID],
				[
					'$push' => [
						'players' => [
							'user' => [
								'userID' => $currentUser->userID,
								'username' => $currentUser->username
							],
							'approved' => true,
							'isGM' => false
						]
					],
					'$pull' => [
						'invites' => ['userID' => $currentUser->userID]
					]
				]
			);
			$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$game['groupID']}, userID = {$userID}");
			#				$hl_inviteAccepted = new HistoryLogger('inviteAccepted');
			#				$hl_inviteAccepted->addUser($userID)->addGame($gameID)->save();
			displayJSON(['success' => true, 'userID' => (int)$userID]);
		} else {
			displayJSON(['failed' => true, 'errors' => 'noPermission']);
		}
	}

	public function submitCharacter($gameID, $characterID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');

		$gameID = (int)$gameID;
		$game = $mongo->games->findOne(
			[
				'gameID' => $gameID,
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
				'players' => true
			]]
		);
		if (!$game) {
			displayJSON(['failed' => true, 'errors' => ['notPlayer']]);
		}
		$isGM = false;
		$playerIDs = [];
		foreach ($game['players'] as $player) {
			if ($player['isGM']) {
				$playerIDs[] = $player['user']['userID'];
			}
			if ($player['user']['userID'] == $currentUser->userID) {
				$isGM = $player['isGM'];
			}
		}
		$charInfo = $mongo->characters->findOne(
			[
				'characterID' => $characterID,
				'user.userID' => $currentUser->userID
			],
			['projection' => ['characterID' => true, 'label' => true, 'game' => true]]
		);
		if (!$charInfo) {
			displayJSON(['failed' => true, 'errors' => ['notOwner']]);
		}

		if ($charInfo['game'] != null) {
			displayJSON(['failed' => true, 'errors' => ['alreadyInGame']]);
		} else {
			$mongo->characters->updateOne(
				['characterID' => $charInfo['characterID']],
				['$set' => [
					'game' => [
						'gameID' => $game['gameID'],
						'title' => $game['title'],
						'approved' => (bool)$isGM
					]
				]]
			);
			#				$hl_charApplied = new HistoryLogger('characterApplied');
			#				$hl_charApplied->addUser($currentUser->userID)->addCharacter($characterID)->addGame($gameID)->save();
			#				if ($isGM) {
			#					$hl_charApproved = new HistoryLogger('characterApproved');
			#					$hl_charApproved->addUser($currentUser->userID, 'gm')->addUser($currentUser->userID)->addCharacter($characterID)->addGame($gameID)->save();
			#				}

			$gmEmails = $mysql->query("SELECT u.email FROM users u INNER JOIN usermeta m ON u.userID = m.userID WHERE u.userID IN (" . implode(', ', $playerIDs) . ") AND m.metaKey = 'gmMail' AND m.metaValue = 1")->fetchAll(PDO::FETCH_COLUMN);
			if (sizeof($gmEmails)) {
				$charDetails = $mongo->characters->findOne(['characterID' => $characterID], ['projection' => ['name' => 1]]);
				$emailDetails = new stdClass();
				$emailDetails->action = 'Character Added';
				$emailDetails->gameInfo = (object)$game;
				$charLabel = strlen($charDetails['name']) ? $charDetails['name'] : $charInfo['label'];
				$systems = Systems::getInstance();
				$emailDetails->message = "<a href=\"http://gamersplane.com/user/{$currentUser->userID}/\" class=\"username\">{$currentUser->username}</a> applied a new character to your game: <a href=\"http://gamersplane.com/characters/{$characterID}/\">{$charLabel}</a>.";
				ob_start();
				include('emails/gmEmail.php');
				$email = ob_get_contents();
				ob_end_clean();
				@mail(implode(', ', $gmEmails), "Game Activity: {$emailDetails->action}", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>");
			}

			displayJSON(['success' => true, 'character' => $charInfo, 'approved' => $isGM]);
		}
	}

	public function removeCharacter($gameID, $characterID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');

		$pendingAction = 'removed';
		$gameID = (int)$gameID;
		$characterID = (int)$characterID;
		$isGM = false;
		$game = $mongo->games->findOne(
			['gameID' => $gameID],
			['projection' => ['players' => true]]
		);
		$charInfo = $mongo->characters->findOne(
			['characterID' => $characterID],
			['projection' => ['user' => true, 'game' => true]]
		);
		$players = [];
		$character = [];
		foreach ($game['players'] as $player) {
			if ($player['user']['userID'] == $currentUser->userID && $player['isGM']) {
				$isGM = true;
			}
			if ($player['user']['userID'] == $charInfo['user']['userID']) {
				$characters = $player['characters'];
			}
		}
		if ($charInfo['user']['userID'] != $currentUser->userID && !$isGM) {
			displayJSON(['failed' => true, 'errors' => 'badAuthentication']);
		}

		$mongo->characters->updateOne(
			['characterID' => $characterID],
			['$set' => ['game' => null]]
		);
		if ($charInfo['user']['userID'] == $currentUser->userID) {
			$pendingAction = 'withdrawn';
		} elseif (!$charInfo['approved']) {
			$pendingAction = 'rejected';
		}
		#			$hl_charRemoved = new HistoryLogger('character'.ucwords($pendingAction));
		#			$hl_charRemoved->addCharacter($characterID);
		#			if ($pendingAction != 'withdrawn')
		#				$hl_charRemoved->addUser($currentUser->userID, 'gm');
		#			$hl_charRemoved->addUser($charInfo['userID'])->addGame($gameID)->save();

		displayJSON(['success' => true, 'action' => $pendingAction, 'characterID' => $characterID]);
	}

	public function approveCharacter($gameID, $characterID)
	{
		global $currentUser;
		$mysql = DB::conn('mysql');
		$mongo = DB::conn('mongo');

		$gameID = (int)$gameID;
		$characterID = (int)$characterID;
		$game = $mongo->games->findOne(
			[
				'gameID' => $gameID,
				'players' => [
					'$elemMatch' => [
						'user.userID' => $currentUser->userID,
						'isGM' => true,
					]
				]
			],
			['projection' => ['players' => true]]
		);
		$charInfo = $mongo->characters->findOne(
			[
				'characterID' => $characterID,
				'game.gameID' => $gameID,
				'retired' => null
			],
			['projection' => ['characterID' => true, 'user' => true]]
		);
		if (!$charInfo && $game) {
			displayJSON(['failed' => true, 'errors' => 'badAuthentication']);
		}
		$mongo->characters->updateOne(
			['characterID' => $characterID],
			['$set' => ['game.approved' => true]]
		);
		#			$hl_charApproved = new HistoryLogger('characterApproved');
		#			$hl_charApproved->addCharacter($characterID)->addUser($currentUser->userID, 'gm')->addGame($gameID)->save();

		displayJSON(['success' => true, 'action' => 'characterApproved', 'characterID' => $characterID]);
	}

	public function getLFG()
	{
		$mongo = DB::conn('mongo');

		$lfgCount = intval($_POST['lfgCount']) > 0 ? intval($_POST['lfgCount']) : 10;
		$rLFGs = $mongo->systems->find(
			['lfg' => ['$ne' => 0]],
			[
				'projection' => ['name' => 1, 'lfg' => 1],
				'sort' => ['lfg' => -1, 'sortName' => 1],
				'limit' => $lfgCount
			]
		);
		$lfgs = [];
		foreach ($rLFGs as $rLFG) {
			$lfgs[] = ['name' => $rLFG['name'], 'count' => (int)$rLFG['lfg']];
		}

		displayJSON(['success' => true, 'lfgs' => $lfgs]);
	}
}
