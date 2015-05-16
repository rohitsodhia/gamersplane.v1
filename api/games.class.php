<?
	class games {
		function __construct() {
			global $loggedIn, $pathOptions;
			if (!$loggedIn) exit;

			if ($pathOptions[0] == 'details') 
				$this->details($_POST['gameID']);
			elseif ($pathOptions[0] == 'invite' && intval($_POST['gameID']) && strlen($_POST['user'])) 
				$this->invite($_POST['gameID'], $_POST['user']);
/*			elseif ($pathOptions[0] == 'view' && intval($_POST['pmID'])) 
				$this->displayPM($_POST['pmID']);
			elseif ($pathOptions[0] == 'send') 
				$this->sendPM();
			elseif ($pathOptions[0] == 'delete' && intval($_POST['pmID'])) 
				$this->deletePM($_POST['pmID']);*/
			else 
				displayJSON(array('failed' => true));
		}

		public function details($gameID) {
			require_once(FILEROOT.'/../javascript/markItUp/markitup.bbcode-parser.php');
			global $mysql, $mongo, $currentUser;

			$gameID = intval($gameID);
			if (!$gameID) 
				displayJSON(array('failed' => true));
			$gameInfo = $mysql->query("SELECT g.gameID, g.title, g.system, g.gmID, u.username gmUsername, g.created, g.postFrequency, g.numPlayers, g.charsPerPlayer, g.description, g.charGenInfo, g.forumID, g.groupID, g.status, u.username FROM games g INNER JOIN users u ON g.gmID = u.userID WHERE g.gameID = $gameID");
			if (!$gameInfo->rowCount()) 
				displayJSON(array('failed' => true, 'noGame' => true));
			$gameInfo = $gameInfo->fetch();
			$gameInfo['title'] = printReady($gameInfo['title']);
			$system = $mongo->systems->findOne(array('id' => $gameInfo['system']), array('name' => 1));
			$gameInfo['system'] = array('_id' => $gameInfo['system'], 'name' => $system['name']);
			$gameInfo['gm'] = array('userID' => $gameInfo['gmID'], 'username' => $gameInfo['gmUsername']);
			unset($gameInfo['gmID'], $gameInfo['gmUsername']);
			$gameInfo['postFrequency'] = explode('/', $gameInfo['postFrequency']);
			$gameInfo['description'] = printReady($gameInfo['description']);
			$gameInfo['charGenInfo'] = printReady($gameInfo['charGenInfo']);
			$players = $mysql->query("SELECT p.userID, u.username, p.approved, p.isGM FROM players p INNER JOIN users u ON p.userID = u.userID WHERE p.gameID = {$gameID}")->fetchAll();
			$characters = $mysql->query("SELECT characterID, userID, label, approved FROM characters WHERE gameID = {$gameID}")->fetchAll();
			displayJSON(array('details' => $gameInfo, 'players' => $players, 'characters' => $characters));
		}

		public function invite($gameID, $user) {
			global $mysql, $currentUser;

			$gameID = intval($gameID);
			$isGM = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND userID = {$currentUser->userID} AND gameID = {$gameID}");
			if ($isGM->rowCount()) {
				$userCheck = $mysql->prepare("SELECT userID, username, email FROM users WHERE username = :username LIMIT 1");
				$userCheck->execute(array(':username' => $user));
				if ($userCheck->rowCount() == 1) {
					$user = $userCheck->fetch();
					$mysql->query("INSERT INTO gameInvites SET gameID = {$gameID}, invitedID = {$user['userID']}");
					$gameInfo = $mysql->query("SELECT g.title, g.system, s.fullName FROM games g INNER JOIN systems s ON g.system = s.shortName WHERE g.gameID = {$gameID}")->fetch();
					ob_start();
					include('emails/gameInviteEmail.php');
					$email = ob_get_contents();
					ob_end_clean();
					@mail($user['email'], "Game Invite", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>");
					displayJSON(array('success' => true, 'user' => $user));
				} else 
					displayJSON(array('failed' => true, 'errors' => array('invalidUser')));
			} else 
				displayJSON(array('failed' => true, 'errors' => 'notGM'));
		}

		public function checkAllowed($pmID) {
			global $mongo, $currentUser;

			$pmID = intval($pmID);
			$pm = $mongo->pms->findOne(array('pmID' => $pmID, '$or' => array(array('sender.userID' => $currentUser->userID), array('recipient.userID' => $currentUser->userID, 'deleted' => false))));
			displayJSON(array('allowed' => $pm?true:false));
		}

		public function sendPM() {
			global $mysql, $mongo, $currentUser;

			$sender = (object) array('userID' => $currentUser->userID, 'username' => $currentUser->username);
			$recipient = sanitizeString(preg_replace('/[^\w.]/', '', $_POST['username']));
			$recipient = $mysql->query("SELECT userID, username FROM users WHERE username = '{$recipient}'")->fetch(PDO::FETCH_OBJ);
			$recipient->userID = (int) $recipient->userID;
			$recipient->read = false;
			$recipient->deleted = false;
			$replyTo = intval($_POST['replyTo']) > 0?intval($_POST['replyTo']):null;
			if ($sender->userID == $recipient->userID) 
				displayJSON(array('mailingSelf' => true));
			else {
				$history = null;
				if ($replyTo) {
					$parent = $mongo->pms->findOne(array('pmID' => $replyTo));
					$history = array($replyTo);
					if ($parent['history']) 
						$history = array_merge($history, $parent['history']);
				}
				$mongo->pms->insert(array('pmID' => mongo_getNextSequence('pmID'), 'sender' => $sender, 'recipients' => array($recipient), 'title' => sanitizeString($_POST['title']), 'message' => sanitizeString($_POST['message']), 'datestamp' => date('Y-m-d H:i:s'), 'replyTo' => $replyTo, 'history' => $history));
				displayJSON(array('sent' => true));
			}
		}

		public function deletePM($pmID) {
			global $mongo, $currentUser;

			$pmID = intval($pmID);
			$pm = $mongo->pms->findOne(array('pmID' => $pmID, '$or' => array(array('sender.userID' => $currentUser->userID), array('recipients.userID' => $currentUser->userID))));
			if ($pm === null) 
				displayJSON(array('noMatch' => true));
			elseif ($pm['sender']['userID'] == $currentUser->userID) {
				$allowDelete = true;
				foreach ($pm['recipients'] as $recipient) 
					if ($recipient['read'] && !$recipient['deleted']) 
						$allowDelete = false;

				if ($allowDelete) 
					$mongo->pms->remove(array('pmID' => $pmID));

				displayJSON(array('deleted' => true));
			} else {
				$mongo->pms->update(array('pmID' => $pmID, 'recipients.userID' => $currentUser->userID), array('$set' => array('recipients.$.deleted' => true)));

				displayJSON(array('deleted' => true));
			}
		}
	}
?>