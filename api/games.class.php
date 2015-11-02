<?
	class games {
		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'details') 
				$this->details($_POST['gameID']);
			elseif ($pathOptions[0] == 'toggleGameStatus' && intval($_POST['gameID'])) 
				$this->toggleGameStatus($_POST['gameID']);
			elseif ($pathOptions[0] == 'toggleForum' && intval($_POST['gameID'])) 
				$this->toggleForum($_POST['gameID']);
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
/*			elseif ($pathOptions[0] == 'view' && intval($_POST['pmID'])) 
				$this->displayPM($_POST['pmID']);
			elseif ($pathOptions[0] == 'delete' && intval($_POST['pmID'])) 
				$this->deletePM($_POST['pmID']);*/
			else 
				displayJSON(array('failed' => true));
		}

		public function details($gameID) {
			require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
			global $mysql, $mongo, $currentUser;

			$gameID = intval($gameID);
			if (!$gameID) 
				displayJSON(array('failed' => true));
			$gameInfo = $mysql->query("SELECT g.gameID, g.title, g.system, g.gmID, u.username gmUsername, u.lastActivity, g.created, g.postFrequency, g.numPlayers, g.charsPerPlayer, g.description, g.charGenInfo, g.forumID, p.`read` readPermissions, g.groupID, g.status, g.retired FROM games g INNER JOIN users u ON g.gmID = u.userID INNER JOIN forums_permissions_general p ON g.forumID = p.forumID WHERE g.gameID = {$gameID}");
			if (!$gameInfo->rowCount()) 
				displayJSON(array('failed' => true, 'noGame' => true));
			$gameInfo = $gameInfo->fetch();
			$isGM = $gameInfo['gmID'] == $currentUser->userID && $gameInfo['retired'] == null?true:false;
			$gameInfo['gameID'] = (int) $gameInfo['gameID'];
			$gameInfo['title'] = printReady($gameInfo['title']);
			$system = $mongo->systems->findOne(array('_id' => $gameInfo['system']), array('name' => 1));
			$gameInfo['system'] = array('_id' => printReady($gameInfo['system']), 'name' => printReady($system['name']));
			require_once(FILEROOT.'/includes/User.class.php');
			$gameInfo['gm'] = array('userID' => (int) $gameInfo['gmID'], 'username' => printReady($gameInfo['gmUsername']), 'inactive' => User::inactive($gameInfo['lastActivity']));
			unset($gameInfo['gmID'], $gameInfo['gmUsername'], $gameInfo['lastActivity']);
			$gameInfo['created'] = date('F j, Y g:i a', strtotime($gameInfo['created']));
			$gameInfo['postFrequency'] = explode('/', $gameInfo['postFrequency']);
			$gameInfo['postFrequency'][0] = (int) $gameInfo['postFrequency'][0];
			$gameInfo['postFrequency'][1] = $gameInfo['postFrequency'][1] == 'd'?'day':'week';
			$gameInfo['numPlayers'] = (int) $gameInfo['numPlayers'];
			$gameInfo['charsPerPlayer'] = (int) $gameInfo['charsPerPlayer'];
			$gameInfo['description'] = strlen($gameInfo['description'])?printReady($gameInfo['description']):'None Provided';
			$gameInfo['charGenInfo'] = strlen($gameInfo['charGenInfo'])?printReady($gameInfo['charGenInfo']):'None Provided';
			$gameInfo['forumID'] = (int) $gameInfo['forumID'];
			$gameInfo['readPermissions'] = (bool) $gameInfo['readPermissions'];
			$gameInfo['groupID'] = (int) $gameInfo['groupID'];
			$gameStatus = array('Closed', 'Open');
			$gameInfo['status'] = (bool) $gameInfo['status'];
			$rCharacters = $mysql->query("SELECT characterID, userID, label, approved FROM characters WHERE gameID = {$gameID} ORDER BY label");
			$characters = array();
			foreach ($rCharacters as $character) {
				$character['characterID'] = (int) $character['characterID'];
				$character['userID'] = (int) $character['userID'];
				$character['label'] = printReady($character['label']);
				$character['approved'] = (bool) $character['approved'];
				$characters[$character['userID']][] = $character;
			}
			$players = $mysql->query("SELECT p.userID, u.username, p.approved, p.isGM, p.primaryGM FROM players p INNER JOIN users u ON p.userID = u.userID WHERE p.gameID = {$gameID} ORDER BY p.approved, u.username")->fetchAll();
			$gameInfo['approvedPlayers'] = 0;
			foreach ($players as &$player) {
				$player['userID'] = (int) $player['userID'];
				$player['username'] = printReady($player['username']);
				$player['approved'] = $player['approved']?true:false;
				$player['isGM'] = $player['isGM']?true:false;
				$player['primaryGM'] = $player['primaryGM']?true:false;
				if ($player['approved'] && !$player['primaryGM']) 
					$gameInfo['approvedPlayers']++;
				$player['characters'] = isset($characters[$player['userID']])?$characters[$player['userID']]:array();
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
			displayJSON(array('success' => true, 'details' => $gameInfo, 'players' => $players, 'invites' => $invites, 'decks' => $decks));
		}

		public function toggleForum($gameID) {
			global $currentUser, $mysql;

			$gameID = (int) $gameID;
			$isGM = $mysql->query("SELECT p.isGM FROM players p INNER JOIN games g ON p.gameID = g.gameID WHERE p.userID = {$currentUser->userID} AND p.gameID = {$gameID} AND g.retired IS NULL");
			if ($isGM->rowCount()) {
				$mysql->query("UPDATE games g, forums_permissions_general p SET p.read = p.read ^ 1, g.public = g.public ^ 1 WHERE g.gameID = $gameID AND g.forumID = p.forumID");
				displayJSON(array('success' => true));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'notGM'));
		}

		public function toggleGameStatus($gameID) {
			global $currentUser, $mysql;

			$gameID = (int) $gameID;
			$isGM = $mysql->query("SELECT p.isGM FROM players p INNER JOIN games g ON p.gameID = g.gameID WHERE p.userID = {$currentUser->userID} AND p.gameID = {$gameID} AND g.retired IS NULL");
			if ($isGM->rowCount()) {
				$mysql->query("UPDATE games SET status = !status WHERE gameID = {$gameID}");
				displayJSON(array('success' => true));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'notGM'));
		}

		public function retire($gameID) {
			global $currentUser, $mysql;

			$gameID = (int) $gameID;
			list($gmID, $forumID, $public) = $mysql->query("SELECT gmID, forumID, public FROM games WHERE gameID = {$gameID}")->fetch(PDO::FETCH_NUM);
			$gmID = (int) $gmID;
			$forumID = (int) $forumID;
			if ($currentUser->userID == $gmID) {
				$mysql->query("UPDATE games SET retired = NOW(), status = 0 WHERE gameID = {$gameID}");
				$chars = $mysql->query("SELECT characterID FROM characters WHERE gameID = {$gameID}")->fetchAll(PDO::FETCH_COLUMN);
				$mysql->query("UPDATE characters SET gameID = NULL WHERE gameID = {$gameID}");
				$groups = $mysql->query("DELETE p FROM forums_permissions_groups p INNER JOIN forums_groups g ON p.groupID = g.groupID WHERE g.gameID = {$gameID}");
				$forums = $mysql->query("SELECT forumID FROM forums WHERE gameID = {$gameID}")->fetchAll(PDO::FETCH_COLUMN);
				$mysql->query("DELETE FROM forums_permissions_users WHERE forumID IN (".implode(', ', $forums).")");
				$mysql->query("DELETE FROM forumAdmins WHERE forumID IN (".implode(', ', $forums).")");
				$mysql->query("DELETE FROM forums_permissions_general WHERE forumID IN (".implode(', ', $forums).") AND forumID != {$forumID}");
				foreach ($forums as $cForumID) 
					if ($cForumID != $forumID) 
						$mysql->query("INSERT INTO forums_permissions_general SET forumID = {$cForumID}");
				$mysql->query("UPDATE forums_permissions_general SET `read` = {$public}, `write` = 0, `editPost` = 0, `deletePost` = 0, `createThread` = 0, `deleteThread` = 0, `addPoll` = 0, `addRolls` = -1, `addDraws` = -1, `moderate` = -1 WHERE forumID = {$forumID}");
				$players = $mysql->query("SELECT userID FROM players WHERE gameID = {$gameID}")->fetchAll(PDO::FETCH_COLUMN);
				$mysql->query("DELETE FROM players WHERE gameID = {$gameID} AND primaryGM = 1");
				$hl_retired = new HistoryLogger('retired');
				$hl_retired->addGame($gameID)->addForUsers($players)->addForCharacters($chars)->save();
				displayJSON(array('success' => true));
			} else 
				displayJSON(array('failed' => true, 'errors' => array('notGM')));
		}

		public function apply() {
			global $loggedIn, $currentUser, $mysql;
			if (!$loggedIn) 
				displayJSON(array('failed' => true, 'loggedOut' => true));

			$gameID = intval($_POST['gameID']);
			$status = $mysql->query("SELECT status FROM games WHERE gameID = {$gameID} LIMIT 1")->fetchColumn();
			if ($status == 1) {
				$mysql->query("INSERT INTO players SET gameID = {$gameID}, userID = {$currentUser->userID}");
				$hl_playerApplied = new HistoryLogger('playerApplied');
				$hl_playerApplied->addUser($currentUser->userID)->addGame($gameID)->save();
			}
			else 
				displayJSON(array('failed' => true, 'gameClosed' => true));

			displayJSON(array('success' => true));
		}

		public function invite($gameID, $user) {
			global $mysql, $currentUser;

			$gameID = intval($gameID);
			$isGM = $mysql->query("SELECT p.isGM FROM players p INNER JOIN games g ON p.gameID = g.gameID WHERE p.userID = {$currentUser->userID} AND p.gameID = {$gameID} AND g.retired IS NULL");
			if ($isGM->rowCount()) {
				$userCheck = $mysql->prepare("SELECT u.userID, u.username, u.email, p.approved FROM users u LEFT JOIN players p ON u.userID = p.userID AND p.gameID = {$gameID} WHERE u.username = :username LIMIT 1");
				$userCheck->execute(array(':username' => $user));
				if (!$userCheck->rowCount())
					displayJSON(array('failed' => true, 'errors' => array('invalidUser')));
				$user = $userCheck->fetch();
				if ($user['approved']) 
					displayJSON(array('failed' => true, 'errors' => array('alreadyInGame')));
				try {
					$mysql->query("INSERT INTO gameInvites SET gameID = {$gameID}, invitedID = {$user['userID']}");
				} catch (Exception $e) {
					displayJSON(array('failed' => true, 'errors' => 'alreadyInvited'));
				}
				$gameInfo = $mysql->query("SELECT g.title, g.system, s.fullName FROM games g INNER JOIN systems s ON g.system = s.shortName WHERE g.gameID = {$gameID}")->fetch();
				ob_start();
				include('emails/gameInviteEmail.php');
				$email = ob_get_contents();
				ob_end_clean();
				@mail($user['email'], "Game Invite", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>");
				$hl_playerInvited = new HistoryLogger('playerInvited');
				$hl_playerInvited->addUser($currentUser->userID, 'gm')->addUser($user['userID'])->addGame($gameID)->save();
				displayJSON(array('success' => true, 'user' => array('userID' => (int) $user['userID'], 'username' => $user['username'])));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'notGM'));
		}

		public function removeInvite($gameID, $userID) {
			global $mysql, $currentUser;

			$gameID = intval($gameID);
			$userID = intval($userID);
			$isGM = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND userID = {$currentUser->userID} AND gameID = {$gameID}");
			if ($isGM->rowCount() || $currentUser->userID == $userID) {
				$mysql->query("DELETE FROM gameInvites WHERE gameID = {$gameID} AND invitedID = {$userID}");
				$hl_inviteRemoved = new HistoryLogger('invite'.ucwords($pathOptions[1]).($pathOptions[1] == 'withdraw'?'n':'d'));
				if ($pathOptions[1] == 'withdraw') 
					$hl_inviteRemoved->addUser($currentUser->userID, 'gm');
				$hl_inviteRemoved->addUser($userID)->addGame($gameID)->save();
				displayJSON(array('success' => true, 'userID' => (int) $userID));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'noPermission'));
		}

		public function acceptInvite($gameID) {
			global $mysql, $currentUser;

			$gameID = intval($gameID);
			$userID = (int) $currentUser->userID;
			$validGame = $mysql->query("SELECT g.groupID FROM gameInvites i INNER JOIN games g ON i.gameID = g.gameID WHERE i.gameID = {$gameID} AND i.invitedID = {$userID} AND g.retired IS NULL");
			if ($validGame->rowCount()) {
				$mysql->query("INSERT INTO players SET gameID = {$gameID}, userID = {$userID}, approved = 1");
				$groupID = $validGame->fetchColumn();
				$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$groupID}, userID = {$userID}");
				$mysql->query("DELETE FROM gameInvites WHERE gameID = {$gameID} AND invitedID = {$userID}");
				$hl_inviteAccepted = new HistoryLogger('inviteAccepted');
				$hl_inviteAccepted->addUser($userID)->addGame($gameID)->save();
				displayJSON(array('success' => true, 'userID' => (int) $userID));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'noPermission'));
		}

		public function submitCharacter($gameID, $characterID) {
			global $currentUser, $mysql, $mongo;

			$player = $mysql->query("SELECT isGM FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID} AND approved = 1");
			if ($player->rowCount() == 0) 
				displayJSON(array('failed' => true, 'errors' => array('notPlayer')));
			$isGM = $player->fetchColumn()?true:false;
			$charInfo = $mysql->query("SELECT characterID, userID, label, approved FROM characters WHERE retired IS NULL AND characterID = {$characterID} AND userID = {$currentUser->userID}");
			if (!$charInfo->rowCount()) 
				displayJSON(array('failed' => true, 'errors' => array('notOwner')));
			$charInfo = $charInfo->fetch();
			$charInfo['characterID'] = (int) $charInfo['characterID'];
			$charInfo['userID'] = (int) $charInfo['userID'];
			$charInfo['approved'] = (bool) $charInfo['approved'];

			if (is_int($charInfo['gameID'])) 
				displayJSON(array('failed' => true, 'errors' => array('alreadyInGame')));
			elseif ($charInfo['gameID'] == 0) {
				$mysql->query("UPDATE characters SET gameID = {$gameID}".($isGM?', approved = 1':'')." WHERE characterID = {$characterID}");
				$hl_charApplied = new HistoryLogger('characterApplied');
				$hl_charApplied->addUser($currentUser->userID)->addCharacter($characterID)->addGame($gameID)->save();
				if ($isGM) {
					$hl_charApproved = new HistoryLogger('characterApproved');
					$hl_charApproved->addUser($currentUser->userID, 'gm')->addUser($currentUser->userID)->addCharacter($characterID)->addGame($gameID)->save();
				}

				$gmEmails = $mysql->query("SELECT u.email FROM users u INNER JOIN players p ON u.userID = p.userID AND p.isGM = 1 LEFT JOIN usermeta m ON u.userID = m.userID WHERE p.gameID = {$gameID} AND m.metaKey = 'gmMail' AND m.metaValue = 1")->fetchAll(PDO::FETCH_COLUMN);
				if (sizeof($gmEmails)) {
					$charDetails = $mongo->characters->findOne(array('characterID' => $characterID), array('name' => 1));
					$emailDetails = new stdClass();
					$emailDetails->action = 'Character Added';
					$emailDetails->gameInfo = $mysql->query("SELECT gameID, title, system FROM games WHERE gameID = {$gameID}")->fetch(PDO::FETCH_OBJ);
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
			} else 
				displayJSON(array('failed' => true));
		}

		public function removeCharacter($gameID, $characterID) {
			global $currentUser, $mysql;

			$pendingAction = 'removed';
			$gmCheck = $mysql->query("SELECT isGM FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID}");
			$charInfo = $mysql->query("SELECT c.label, c.userID, u.username, g.title, g.charsPerPlayer, g.system FROM characters c INNER JOIN users u ON c.userID = u.userID INNER JOIN games g ON c.gameID = g.gameID WHERE c.characterID = {$characterID}");
			if ($charInfo->rowCount() == 0 && $gmCheck->rowCount() == 0) 
				displayJSON(array('failed' => true, 'errors' => 'badAuthentication'), exit);
			$mysql->query("UPDATE characters SET approved = 0, gameID = NULL WHERE characterID = {$characterID}");
			$charInfo = $charInfo->fetch();
			if ($currentUser->userID == $charInfo['userID']) 
				$pendingAction = 'withdrawn';
			if (!$charInfo['approved']) 
				$pendingAction = 'rejected';
			$hl_charRemoved = new HistoryLogger('character'.ucwords($pendingAction));
			$hl_charRemoved->addCharacter($characterID);
			if ($pendingAction != 'withdrawn') 
				$hl_charRemoved->addUser($currentUser->userID, 'gm');
			$hl_charRemoved->addUser($charInfo['userID'])->addGame($gameID)->save();
			
			displayJSON(array('success' => true, 'action' => $pendingAction, 'characterID' => $characterID));
		}

		public function approveCharacter($gameID, $characterID) {
			global $currentUser, $mysql;

			$gmCheck = $mysql->query("SELECT isGM FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID}");
			$charInfo = $mysql->query("SELECT c.label, c.userID, u.username, g.title, g.charsPerPlayer, g.system FROM characters c INNER JOIN users u ON c.userID = u.userID INNER JOIN games g ON c.gameID = g.gameID WHERE c.retired IS NULL AND c.characterID = {$characterID}");
			if ($charInfo->rowCount() == 0 && $gmCheck->rowCount() == 0) 
				displayJSON(array('failed' => true, 'errors' => 'badAuthentication'), exit);
			$mysql->query("UPDATE characters SET approved = 1 WHERE characterID = {$characterID}");
			$hl_charApproved = new HistoryLogger('characterApproved');
			$hl_charApproved->addCharacter($characterID)->addUser($currentUser->userID, 'gm')->addGame($gameID)->save();
			
			displayJSON(array('success' => true, 'action' => 'characterApproved', 'characterID' => $characterID));
		}
	}
?>