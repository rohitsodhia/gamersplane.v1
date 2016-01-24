<?
	class games {
		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'getGames') 
				$this->getGames();
			elseif ($pathOptions[0] == 'details') 
				$this->details($_POST['gameID']);
			elseif ($pathOptions[0] == 'create') 
				$this->createGame();
			elseif ($pathOptions[0] == 'update') 
				$this->updateGame();
			elseif ($pathOptions[0] == 'toggleForum' && intval($_POST['gameID'])) 
				$this->toggleForum($_POST['gameID']);
			elseif ($pathOptions[0] == 'toggleGameStatus' && intval($_POST['gameID'])) 
				$this->toggleGameStatus($_POST['gameID']);
			elseif ($pathOptions[0] == 'retire' && intval($_POST['gameID'])) 
				$this->retire($_POST['gameID']);
			elseif ($pathOptions[0] == 'apply') 
				$this->apply();
			elseif ($pathOptions[0] == 'invite' && sizeof($pathOptions) == 1 && intval($_POST['gameID']) && strlen($_POST['user'])) 
				$this->invite($_POST['gameID'], $_POST['user']);
			elseif ($pathOptions[0] == 'invite' && ($pathOptions[1] == 'withdraw' || $pathOptions[1] == 'decline') && intval($_POST['gameID']) && strlen($_POST['userID'])) 
				$this->removeInvite($_POST['gameID'], $_POST['userID']);
			elseif ($pathOptions[0] == 'invite' && $pathOptions[1] == 'accept' && intval($_POST['gameID'])) 
				$this->acceptInvite($_POST['gameID']);
			elseif ($pathOptions[0] == 'characters' && $pathOptions[1] == 'submit' && intval($_POST['gameID']) && intval($_POST['characterID'])) 
				$this->submitCharacter((int) $_POST['gameID'], (int) $_POST['characterID']);
			elseif ($pathOptions[0] == 'characters' && $pathOptions[1] == 'remove' && intval($_POST['gameID']) && intval($_POST['characterID'])) 
				$this->removeCharacter((int) $_POST['gameID'], (int) $_POST['characterID']);
			elseif ($pathOptions[0] == 'characters' && $pathOptions[1] == 'approve' && intval($_POST['gameID']) && intval($_POST['characterID'])) 
				$this->approveCharacter((int) $_POST['gameID'], (int) $_POST['characterID']);
			elseif ($pathOptions[0] == 'getLFG') 
				$this->getLFG();
			else 
				displayJSON(array('failed' => true));
		}

		public function getGames() {
			global $currentUser, $mysql, $mongo;

			if (isset($_POST['my']) && $_POST['my']) 
				$rGames = $mongo->games->find(
					array(
						'players' => array(
							'$elemMatch' => array(
								'user.userID' => $currentUser->userID,
								'approved' => true
							)
						),
						'retired' => null
					),
					array(
						'gameID' => true,
						'title' => true,
						'system' => true,
						'gm' => true,
						'status' => true,
						'players' => true,

					)
				);
//				$rGames = $mysql->query("SELECT g.gameID, g.title, g.status, u.userID, u.username, s.shortName system_shortName, s.fullName system_fullName, p.isGM FROM games g INNER JOIN players p ON g.gameID = p.gameID INNER JOIN users u ON g.gmID = u.userID INNER JOIN systems s ON g.system = s.shortName WHERE p.userID = {$currentUser->userID} AND p.approved = 1 AND retired IS NULL");
			else {
/*				$sortOrder = $_POST['sortOrder'] == 'a'?1:-1;
				if ($_POST['orderBy'] == 'createdOn_d' || !isset($_POST['orderBy'])) 
					$orderBy = 'g.created DESC';
				elseif ($_POST['orderBy'] == 'createdOn_a') 
					$orderBy = 'g.created ASC';
				elseif ($_POST['orderBy'] == 'name_a') 
					$orderBy = 'g.title ASC';
				elseif ($_POST['orderBy'] == 'name_d') 
					$orderBy = 'g.title DESC';
				elseif ($_POST['orderBy'] == 'system') 
					$orderBy = 's.fullName ASC';
				$rGames = $mysql->query("SELECT g.gameID, g.title, s.shortName system_shortName, s.fullName system_fullName, g.gmID userID, u.username, u.lastActivity FROM games g INNER JOIN systems s ON g.system = s.shortName LEFT JOIN players p ON g.gameID = p.gameID AND p.userID = {$currentUser->userID} INNER JOIN users u ON g.gmID = u.userID WHERE g.gmID != {$currentUser->userID} AND p.userID IS NULL AND g.status = 1".(isset($_POST['systems']) && sizeof($_POST['systems'])?' AND g.system IN ("'.implode('", "', $_POST['systems']).'")':'')." ORDER BY $orderBy");*/
				$findParams = array(
					'players.user.userID' => array('$ne' => $currentUser->userID),
					'status' => 'open',
					'retired' => null
				);
				if (sizeof($_POST['systems'])) 
					$findParams['system'] = array('$in' => $_POST['systems']);
				$rGames = $mongo->games->find(
					$findParams,
					array(
						'gameID' => true,
						'title' => true,
						'system' => true,
						'gm' => true,
						'start' => true,
						'numPlayers' => true,
						'status' => true,
						'players' => true
					)
				);
			}
			$showFullGames = isset($_POST['showFullGames']) && $_POST['showFullGames']?true:false;
			$games = array();
			$gms = array();
			foreach ($rGames as $game) {
				$game['isGM'] = false;
				$playerCount = -1;
				foreach ($game['players'] as $player) {
					if ($player['user']['userID'] == $currentUser->userID) 
						$game['isGM'] = $player['isGM'];
					if ($player['approved']) 
						$playerCount++;
				}
				if (!$showFullGames && $playerCount == $game['numPlayers']) 
					continue;
				$game['start'] = $game['start']->sec;
				unset($game['numPlayers'], $game['players']);
				$games[] = $game;
				$gms[] = $game['gm']['userID'];
			}
			if (sizeof($gms)) {
				$gms = array_unique($gms);
				$rUsers = $mysql->query("SELECT userID, lastActivity FROM users WHERE userID IN (".implode(', ', $gms).")")->fetchAll();
				$users = array();
				foreach ($rUsers as $user) 
					$users[$user['userID']] = strtotime($user['lastActivity']);
				foreach ($games as &$game) 
					$game['gm']['lastActivity'] = $users[$game['gm']['userID']];
			}

			displayJSON(array('success' => true, 'games' => $games));
		}

		public function details($gameID) {
			require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
			require_once(FILEROOT.'/includes/User.class.php');
			global $mysql, $mongo, $currentUser;

			$gameID = intval($gameID);
			if (!$gameID) 
				displayJSON(array('failed' => true));
			$gameInfo = $mongo->games->findOne(array('gameID' => $gameID));
			if (!$gameInfo) 
				displayJSON(array('failed' => true, 'noGame' => true));
			$gameInfo['readPermissions'] = $mysql->query("SELECT `read` FROM forums_permissions_general WHERE forumID = {$gameInfo['forumID']} LIMIT 1")->fetchColumn();
			$gameInfo['readPermissions'] = (bool) $gameInfo['readPermissions'];
			$gameInfo['gm']['lastActivity'] = User::inactive($mysql->query("SELECT lastActivity FROM users WHERE userID = {$gameInfo['gm']['userID']} LIMIT 1")->fetchColumn());
			$gameInfo['title'] = printReady($gameInfo['title']);
			$gameInfo['created'] = date('F j, Y g:i a', $gameInfo['created']->sec);
			$gameInfo['description'] = strlen($gameInfo['description'])?printReady($gameInfo['description']):'None Provided';
			$gameInfo['charGenInfo'] = strlen($gameInfo['charGenInfo'])?printReady($gameInfo['charGenInfo']):'None Provided';
			$rCharacters = $mysql->query("SELECT characterID, userID, label, approved FROM characters WHERE gameID = {$gameID} ORDER BY label");
			$characters = array();
			foreach ($rCharacters as $character) {
				$character['characterID'] = (int) $character['characterID'];
				$character['userID'] = (int) $character['userID'];
				$character['label'] = printReady($character['label']);
				$character['approved'] = (bool) $character['approved'];
				$characters[$character['userID']][] = $character;
			}
			$gameInfo['approvedPlayers'] = 0;
			foreach ($gameInfo['players'] as &$player) {
				$player['user']['username'] = printReady($player['user']['username']);
				$player['primaryGM'] = $player['user']['userID'] == $gameInfo['gm']['userID']?true:false;
				if ($player['approved'] && !$player['primaryGM']) 
					$gameInfo['approvedPlayers']++;
				$player['characters'] = isset($characters[$player['user']['userID']])?$characters[$player['user']['userID']]:array();
			}
			$invites = $mysql->query("SELECT u.userID, u.username FROM gameInvites i INNER JOIN users u ON i.invitedID = u.userID WHERE i.gameID = {$gameID}")->fetchAll();
			if (sizeof($invites)) {
				array_walk($invites, function (&$invite, $key) {
					$invite['userID'] = (int) $invite['userID'];
				});
			} else 
				$invites = array();
			$decks = $mysql->query("SELECT d.deckID, d.label, d.type, dt.name, d.deck, d.position FROM decks d INNER JOIN deckTypes dt ON d.type = dt.short WHERE gameID = {$gameID}")->fetchAll();
			if (sizeof($decks)) {
				array_walk($decks, function (&$deck, $key) {
					$deck['deckID'] = (int) $deck['deckID'];
					$deck['type'] = array('short' => $deck['type'], 'name' => $deck['name']);
					$deck['cardsRemaining'] = sizeof(explode('~', $deck['deck'])) - $deck['position'] + 1;
					unset($deck['name'], $deck['deck'], $deck['position']);
				});
			} else 
				$decks = array();
			$players = $gameInfo['players'];
			unset($gameInfo['players']);
			displayJSON(array('success' => true, 'details' => $gameInfo, 'players' => $players, 'invites' => $invites, 'decks' => $decks));
		}

		public function createGame() {
			global $currentUser, $mysql, $mongo;
			require_once(FILEROOT.'/includes/Systems.class.php');
			$systems = Systems::getInstance();

			$errors = array();
			$details['title'] = sanitizeString($_POST['title']);
			if (strlen($details['title']) == 0) 
				$errors[] = 'invalidTitle';
			$details['system'] = $systems->verifySystem($_POST['system'])?$_POST['system']:null;
			$details['allowedCharSheets'] = array();
			if (!is_array($_POST['allowedCharSheets']) || sizeof($_POST['allowedCharSheets']) == 0) 
				$errors[] = 'noCharSheets';
			else {
				$validCharSheets = $mongo->systems->find(array('_id' => array('$in' => $_POST['allowedCharSheets']), 'hasCharSheet' => true), array('_id' => true));
				foreach ($validCharSheets as $system) 
					$details['allowedCharSheets'][] = $system['_id'];
				if (sizeof($details['allowedCharSheets']) == 0) 
					$errors[] = 'noCharSheets';
			}
			$details['postFrequency'] = array('timesPer' => intval($_POST['postFrequency']->timesPer), 'perPeriod' => $_POST['postFrequency']->perPeriod);
			$details['numPlayers'] = intval($_POST['numPlayers']);
			$details['charsPerPlayer'] = intval($_POST['charsPerPlayer']);
			$details['description'] = sanitizeString($_POST['description']);
			$details['charGenInfo'] = sanitizeString($_POST['charGenInfo']);
			$details['status'] = 'open';
			$details['public'] = true;

/*			$titleCheck = $mysql->prepare('SELECT gameID FROM games WHERE title = :title'.(isset($_POST['save'])?' AND gameID != '.$gameID:''));
			$titleCheck->execute(array(':title' => $details['title']));
			if ($titleCheck->rowCount()) 
				$errors[] = 'repeatTitle';*/
			if ($details['system'] == null && !isset($_POST['save'])) 
				$errors[] = 'invalidSystem';
			if ($details['postFrequency']['timesPer'] <= 0 || !($details['postFrequency']['perPeriod'] == 'd' || $details['postFrequency']['perPeriod'] == 'w')) 
				$errors[] = 'invalidFreq';
			if ($details['numPlayers'] < 2) 
				$errors[] = 'invalidNumPlayers';

			if (sizeof($errors)) 
				displayJSON(array('failed' => true, 'errors' => $errors));
			else {
				$details['gm'] = array('userID' => $currentUser->userID, 'username' => $currentUser->username);
				$details['created'] = new MongoDate();
				$details['start'] = $details['created'];

				$system = $details['system'];
				$details['gameID'] = mongo_getNextSequence('gameID');
				$gameID = $details['gameID'];
				$details['players'] = array(array(
					'user' => $details['gm'],
					'approved' => true,
					'isGM' => true
				));

				$forumInfo = $mysql->query('SELECT MAX(`order`) + 1 AS newOrder, heritage FROM forums WHERE parentID = 2');
				list($order, $heritage) = $forumInfo->fetch(PDO::FETCH_NUM);
				$addForum = $mysql->prepare("INSERT INTO forums (title, parentID, heritage, `order`, gameID) VALUES (:title, 2, ".mt_rand(0, 9999).", {$order}, {$gameID})");
				$addForum->execute(array(':title' => $details['title']));
				$forumID = $mysql->lastInsertId();
				$heritage = sql_forumIDPad(2).'-'.sql_forumIDPad($forumID);
				$mysql->query("UPDATE forums SET heritage = '{$heritage}' WHERE forumID = {$forumID}");
				$details['forumID'] = $forumID;

				$addForumGroup = $mysql->prepare("INSERT INTO forums_groups (name, ownerID, gameID) VALUES (:title, {$currentUser->userID}, {$gameID})");
				$addForumGroup->execute(array('title' => $details['title']));
				$groupID = $mysql->lastInsertId();
				$details['groupID'] = $groupID;

				$mysql->query('INSERT INTO forums_groupMemberships (groupID, userID) VALUES ('.$groupID.', '.$currentUser->userID.')');

				$mysql->query('INSERT INTO forumAdmins (userID, forumID) VALUES('.$currentUser->userID.', '.$forumID.')');
				$mysql->query('INSERT INTO forums_permissions_groups (`groupID`, `forumID`, `read`, `write`, `editPost`, `createThread`, `deletePost`, `addRolls`, `addDraws`) VALUES ('.$groupID.', '.$forumID.', 2, 2, 2, 2, 2, 2, 2)');
				$mysql->query("INSERT INTO forums_permissions_general SET forumID = $forumID");

				$mongo->games->insert($details);
#				$hl_gameCreated = new HistoryLogger('gameCreated');
#				$hl_gameCreated->addGame($gameID)->save();

				$lfgRecips = $mongo->users->find(array('lfg' => $details['system']), array('userID' => true));
				if ($lfgRecips->count()) {
					$userIDs = array();
					foreach ($lfgRecips as $recip) 
						$userIDs[] = $recip['userID'];
					$lfgRecips = $mysql->query("SELECT email FROM users WHERE userID IN (".implode(', ', $userIDs).")");
					$recips = '';
					foreach ($lfgRecips as $info) 
						$recips .= $info['email'].', ';
					ob_start();
					include('emails/newGameEmail.php');
					$email = ob_get_contents();
					ob_end_clean();
					mail('Gamers Plane <contact@gamersplane.com>', "New {$systems->getFullName($system)} Game: {$details['title']}", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>\r\nBcc: ".substr($recips, 0, -2));
				}

				displayJSON(array('success' => true, 'gameID' => (int) $gameID));
			}
		}

		public function updateGame() {
			global $currentUser, $mysql, $mongo;
			require_once(FILEROOT.'/includes/Systems.class.php');
			$systems = Systems::getInstance();

			$gameID = intval($_POST['gameID']);
			$details['title'] = sanitizeString($_POST['title']);
			$details['allowedCharSheets'] = $_POST['allowedCharSheets'];
			$details['postFrequency'] = array('timesPer' => intval($_POST['postFrequency']->timesPer), 'perPeriod' => $_POST['postFrequency']->perPeriod);
			$details['numPlayers'] = intval($_POST['numPlayers']);
			$details['charsPerPlayer'] = intval($_POST['charsPerPlayer']);
			$details['description'] = sanitizeString($_POST['description']);
			$details['charGenInfo'] = sanitizeString($_POST['charGenInfo']);
			
			$errors = array();
			if (strlen($details['title']) == 0) 
				$errors[] = 'invalidTitle';
/*			$titleCheck = $mysql->prepare('SELECT gameID FROM games WHERE title = :title'.(isset($_POST['save'])?' AND gameID != '.$gameID:''));
			$titleCheck->execute(array(':title' => $details['title']));
			if ($titleCheck->rowCount()) 
				$errors[] = 'repeatTitle';
			if ($details['system'] == null && !isset($_POST['save'])) 
				$errors[] = 'invalidSystem';*/
			if ($details['postFrequency']['timesPer'] <= 0 || !($details['postFrequency']['perPeriod'] == 'd' || $details['postFrequency']['perPeriod'] == 'w')) 
				$errors[] = 'invalidFreq';
			if ($details['numPlayers'] < 2) 
				$errors[] = 'invalidNumPlayers';

			if (sizeof($errors)) 
				displayJSON(array('failed' => true, 'errors' => $errors));
			else {
				$mongo->games->update(array('gameID' => $gameID), array('$set' => $details));
#				$hl_gameEdited = new HistoryLogger('gameEdited');
#				$hl_gameEdited->addGame($gameID)->save();
				
				displayJSON(array('success' => true, 'gameID' => (int) $gameID));
			}
		}

		public function toggleForum($gameID) {
			global $currentUser, $mysql, $mongo;

			$gameID = (int) $gameID;
			$gameInfo = $mongo->games->findOne(array('gameID' => $gameID), array('forumID' => true, 'public' => true, 'players' => true));
			$isGM = false;
			foreach ($gameInfo['players'] as $player) {
				if ($currentUser->userID == $player['user']['userID'] && $player['isGM']) {
					$isGM = true;
					break;
				}
			}
			if ($isGM) {
				$mysql->query("UPDATE forums_permissions_general SET read = read ^ 1 WHERE forumID = {$gameInfo['forumID']}");
				$mongo->games->update(array('gameID' => $gameID), array('$set' => array('public' => !$gameInfo['public'])));
				displayJSON(array('success' => true));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'notGM'));
		}


		public function toggleGameStatus($gameID) {
			global $currentUser, $mysql, $mongo;

			$gameID = (int) $gameID;
			$gameInfo = $mongo->games->findOne(array('gameID' => $gameID), array('forumID' => true, 'status' => true, 'players' => true));
			$isGM = false;
			foreach ($gameInfo['players'] as $player) {
				if ($currentUser->userID == $player['user']['userID'] && $player['isGM']) {
					$isGM = true;
					break;
				}
			}
			if ($isGM) {
				$mongo->games->update(array('gameID' => $gameID), array('$set' => array('status' => $gameInfo['status'] == 'open'?'closed':'open')));
				displayJSON(array('success' => true));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'notGM'));
		}

		public function retire($gameID) {
			global $currentUser, $mysql, $mongo;

			$gameID = (int) $gameID;
			extract($mongo->games->findOne(array('gameID' => $gameID), array('gm' => true, 'forumID' => true, 'groupID' => true, 'public' => true, 'players' => true)));
			$gmID = (int) $gm['userID'];
			if ($currentUser->userID == $gmID) {
				$mongo->games->update(array('gameID' => $gameID), array('$set' => array('retired' => new MongoDate(), 'status' => 'closed', 'players' => array())));
				$chars = $mysql->query("SELECT characterID FROM characters WHERE gameID = {$gameID}")->fetchAll(PDO::FETCH_COLUMN);
				$mysql->query("UPDATE characters SET gameID = NULL WHERE gameID = {$gameID}");
				$groups = $mysql->query("DELETE FROM forums_permissions_group WHERE groupID = {$groupID}");
				$forums = $mysql->query("SELECT forumID FROM forums WHERE gameID = {$gameID}")->fetchAll(PDO::FETCH_COLUMN);
				$mysql->query("DELETE FROM forums_permissions_users WHERE forumID IN (".implode(', ', $forums).")");
				$mysql->query("DELETE FROM forumAdmins WHERE forumID IN (".implode(', ', $forums).")");
				$mysql->query("DELETE FROM forums_permissions_general WHERE forumID IN (".implode(', ', $forums).") AND forumID != {$forumID}");
				foreach ($forums as $cForumID) 
					if ($cForumID != $forumID) 
						$mysql->query("INSERT INTO forums_permissions_general SET forumID = {$cForumID}");
				$mysql->query("UPDATE forums_permissions_general SET `read` = {$public}, `write` = 0, `editPost` = 0, `deletePost` = 0, `createThread` = 0, `deleteThread` = 0, `addPoll` = 0, `addRolls` = -1, `addDraws` = -1, `moderate` = -1 WHERE forumID = {$forumID}");
#				$hl_retired = new HistoryLogger('retired');
#				$hl_retired->addGame($gameID)->addForUsers($players)->addForCharacters($chars)->save();
				displayJSON(array('success' => true));
			} else 
				displayJSON(array('failed' => true, 'errors' => array('notGM')));
		}

		public function apply() {
			global $loggedIn, $currentUser, $mysql, $mongo;
			if (!$loggedIn) 
				displayJSON(array('failed' => true, 'loggedOut' => true));

			$gameID = intval($_POST['gameID']);
			$mongo->games->findOne(array('gameID' => $gameID), array('status' => true));
			if ($status == 'open') {
				$mongo->games->update(array('gameID' => $gameID), array(
					'$push' => array(
						'players' => array(
							'user' => array(
								'userID' => (int) $currentUser->userID,
								'username' => $currentUser->username
							),
							'approved' => false,
							'isGM' => false
						)
					)
				));
#				$hl_playerApplied = new HistoryLogger('playerApplied');
#				$hl_playerApplied->addUser($currentUser->userID)->addGame($gameID)->save();
			}
			else 
				displayJSON(array('failed' => true, 'gameClosed' => true));

			displayJSON(array('success' => true));
		}

		public function invite($gameID, $user) {
			global $mysql, $currentUser, $mongo;

			$gameID = intval($gameID);
			$user = sanitizeString($user, 'lower');
			$gameInfo = $mongo->games->findOne(array('gameID' => $gameID), array('title' => true, 'system' => true, 'players' => true, 'invites' => true));
			$isGM = false;
			foreach ($gameInfo['players'] as $player) {
				if ($currentUser->userID == $player['user']['userID']) 
					if ($player['isGM']) 
						$isGM = true;
				if ($user == strtolower($player['user']['username'])) 
					displayJSON(array('failed' => true, 'errors' => array('alreadyInGame')));
			}
			if ($isGM) {
				$userCheck = $mysql->prepare("SELECT userID, username, email FROM users WHERE LOWER(username) = :username LIMIT 1");
				$userCheck->execute(array(':username' => $user));
				if (!$userCheck->rowCount())
					displayJSON(array('failed' => true, 'errors' => array('invalidUser')));
				$user = $userCheck->fetch();
				foreach ($gameInfo['invites'] as $invite) 
					if ($currentUser->userID == $invite['user']['userID']) 
						displayJSON(array('failed' => true, 'errors' => 'alreadyInvited'));
				$mongo->games->update(array('gameID' => $gameID), array('$push' => array('invites' => array('userID' => $currentUser->userID, 'username' => $currentUser->username))));
				require_once(FILEROOT.'/includes/Systems.class.php');
				$systems = Systems::getInstance();
				ob_start();
				include('emails/gameInviteEmail.php');
				$email = ob_get_contents();
				ob_end_clean();
				@mail($user['email'], "Game Invite", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>");
#				$hl_playerInvited = new HistoryLogger('playerInvited');
#				$hl_playerInvited->addUser($currentUser->userID, 'gm')->addUser($user['userID'])->addGame($gameID)->save();
				displayJSON(array('success' => true, 'user' => array('userID' => (int) $user['userID'], 'username' => $user['username'])));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'notGM'));
		}

		public function removeInvite($gameID, $userID) {
			global $mysql, $currentUser, $mongo;

			$gameID = intval($gameID);
			$userID = intval($userID);
			$game = $mongo->games->findOne(
				array(
					'gameID' => $gameID,
				),
				array(
					'players' => true,
					'invites' => true
				)
			);
			$isGM = false;
			foreach ($game['players'] as $player) {
				if ($player['user']['userID'] == $currentUser->userID && $player['isGM']) {
					$isGM = true;
					break;
				}
			}
			if ($isGM || $currentUser->userID == $userID) {
				$mongo->games->update(
					array(
						'gameID' => $gameID,
					),
					array(
						'$pull' => array(
							'invites.userID' => $userID
						)
					)
				);
#				$hl_inviteRemoved = new HistoryLogger('invite'.ucwords($pathOptions[1]).($pathOptions[1] == 'withdraw'?'n':'d'));
#				if ($pathOptions[1] == 'withdraw') 
#					$hl_inviteRemoved->addUser($currentUser->userID, 'gm');
#				$hl_inviteRemoved->addUser($userID)->addGame($gameID)->save();
				displayJSON(array('success' => true, 'userID' => (int) $userID));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'noPermission'));
		}

		public function acceptInvite($gameID) {
			global $mysql, $currentUser, $mongo;

			$gameID = intval($gameID);
			$userID = (int) $currentUser->userID;
			$game = $mongo->games->findOne(array('gameID' => $gameID, 'invites.user.userID' => $currentUser->userID), array('groupID' => true));
			if ($game) {
				$mongo->games->update(
					array(
						'gameID' => $gameID
					),
					array(
						'$push' => array(
							'user' => array(
								'userID' => $currentUser->userID,
								'username' => $currentUser->username
							),
							'approved' => true,
							'isGM' => false
						),
						'$pull' => array(
							'invites.userID' => $currentUser->userID
						)
					)
				);
				$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$game['groupID']}, userID = {$userID}");
#				$hl_inviteAccepted = new HistoryLogger('inviteAccepted');
#				$hl_inviteAccepted->addUser($userID)->addGame($gameID)->save();
				displayJSON(array('success' => true, 'userID' => (int) $userID));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'noPermission'));
		}

		public function submitCharacter($gameID, $characterID) {
			global $currentUser, $mysql, $mongo;

			$gameID = (int) $gameID;
			$game = $mongo->games->findOne(
				array(
					'gameID' => $gameID,
					'players' => array(
						'$elemMatch' => array(
							'user.userID' => $currentUser->userID,
							'approved' => true
						)
					)
				),
				array(
					'gameID' => true,
					'title' => true,
					'system' => true,
					'players' => true
				)
			);
			if (!$game) 
				displayJSON(array('failed' => true, 'errors' => array('notPlayer')));
			$isGM = false;
			$playerIDs = array();
			foreach ($game['players'] as $player) {
				if ($player['isGM']) 
					$playerIDs[] = $player['user']['userID'];
				if ($player['user']['userID'] == $currentUser->userID) 
					$isGM = $player['isGM'];
			}
			$charInfo = $mysql->query("SELECT characterID, label, gameID FROM characters WHERE retired IS NULL AND characterID = {$characterID} AND userID = {$currentUser->userID}");
			if (!$charInfo->rowCount()) 
				displayJSON(array('failed' => true, 'errors' => array('notOwner')));
			$charInfo = $charInfo->fetch();

			if (is_int($charInfo['gameID'])) 
				displayJSON(array('failed' => true, 'errors' => array('alreadyInGame')));
			else {
				$mysql->query("UPDATE characters SET gameID = {$gameID}".($isGM?', approved = 1':'')." WHERE characterID = {$characterID}");
#				$hl_charApplied = new HistoryLogger('characterApplied');
#				$hl_charApplied->addUser($currentUser->userID)->addCharacter($characterID)->addGame($gameID)->save();
#				if ($isGM) {
#					$hl_charApproved = new HistoryLogger('characterApproved');
#					$hl_charApproved->addUser($currentUser->userID, 'gm')->addUser($currentUser->userID)->addCharacter($characterID)->addGame($gameID)->save();
#				}

				$gmEmails = $mysql->query("SELECT u.email FROM users u INNER JOIN usermeta m ON u.userID = m.userID WHERE u.userID IN (".implode(', ', $playerIDs).") AND m.metaKey = 'gmMail' AND m.metaValue = 1")->fetchAll(PDO::FETCH_COLUMN);
				if (sizeof($gmEmails)) {
					$charDetails = $mongo->characters->findOne(array('characterID' => $characterID), array('name' => 1));
					$emailDetails = new stdClass();
					$emailDetails->action = 'Character Added';
					$emailDetails->gameInfo = (object) $game;
					$charLabel = strlen($charDetails['name'])?$charDetails['name']:$charInfo['label'];
					require_once(FILEROOT.'/includes/Systems.class.php');
					$systems = Systems::getInstance();
					$emailDetails->message = "<a href=\"http://gamersplane.com/user/{$currentUser->userID}/\" class=\"username\">{$currentUser->username}</a> applied a new character to your game: <a href=\"http://gamersplane.com/characters/{$characterID}/\">{$charLabel}</a>.";
					ob_start();
					include('emails/gmEmail.php');
					$email = ob_get_contents();
					ob_end_clean();
					@mail(implode(', ', $gmEmails), "Game Activity: {$emailDetails->action}", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>");
				}

				displayJSON(array('success' => true, 'character' => $charInfo, 'approved' => $isGM));
			}
		}

		public function removeCharacter($gameID, $characterID) {
			global $currentUser, $mysql, $mongo;

			$pendingAction = 'removed';
			$gameID = (int) $gameID;
			$isGM = $mongo->games->findOne(
				array(
					'gameID' => $gameID,
					'players' => array(
						'$elemMatch' => array(
							'user.userID' => $currentUser->userID,
							'isGM' => true,
						)
					)
				),
				array(
					'players.$' => true
				)
			);
			$charInfo = $mysql->query("SELECT userID, approved FROM characters WHERE characterID = {$characterID} AND gameID = {$gameID} LIMIT 1");
			$charInfo = $charInfo->fetch();
			if (!$charInfo['userID'] != $currentUser->userID || !$isGM) 
				displayJSON(array('failed' => true, 'errors' => 'badAuthentication'), exit);
			$mysql->query("UPDATE characters SET approved = 0, gameID = NULL WHERE characterID = {$characterID}");
			if ($charInfo['userID'] == $currentUser->userID) 
				$pendingAction = 'withdrawn';
			if (!$charInfo['approved']) 
				$pendingAction = 'rejected';
#			$hl_charRemoved = new HistoryLogger('character'.ucwords($pendingAction));
#			$hl_charRemoved->addCharacter($characterID);
#			if ($pendingAction != 'withdrawn') 
#				$hl_charRemoved->addUser($currentUser->userID, 'gm');
#			$hl_charRemoved->addUser($charInfo['userID'])->addGame($gameID)->save();
			
			displayJSON(array('success' => true, 'action' => $pendingAction, 'characterID' => $characterID));
		}

		public function approveCharacter($gameID, $characterID) {
			global $currentUser, $mysql, $mongo;

			$gameID = (int) $gameID;
			$game = $mongo->games->findOne(
				array(
					'gameID' => $gameID,
					'players' => array(
						'$elemMatch' => array(
							'user.userID' => $currentUser->userID,
							'isGM' => true,
						)
					)
				),
				array(
					'players' => true
				)
			);
			$charInfo = $mysql->query("SELECT characterID FROM characters WHERE retired IS NULL AND gameID = {$gameID} AND characterID = {$characterID}");
			if ($charInfo->rowCount() == 0 && $game) 
				displayJSON(array('failed' => true, 'errors' => 'badAuthentication'));
			$mysql->query("UPDATE characters SET approved = 1 WHERE characterID = {$characterID}");
#			$hl_charApproved = new HistoryLogger('characterApproved');
#			$hl_charApproved->addCharacter($characterID)->addUser($currentUser->userID, 'gm')->addGame($gameID)->save();
			
			displayJSON(array('success' => true, 'action' => 'characterApproved', 'characterID' => $characterID));
		}

		public function getLFG() {
			global $mongo;

			$lfgCount = intval($_POST['lfgCount']) > 0?intval($_POST['lfgCount']):10;
			$rLFGs = $mongo->systems->find(array('lfg' => array('$ne' => 0)), array('name' => 1, 'lfg' => 1))->sort(array('lfg' => -1, 'sortName' => 1))->limit($lfgCount);
			$lfgs = array();
			foreach ($rLFGs as $rLFG) 
				$lfgs[] = array('name' => $rLFG['name'], 'count' => (int) $rLFG['lfg']);

			displayJSON(array('success' => true, 'lfgs' => $lfgs));
		}
	}
?>