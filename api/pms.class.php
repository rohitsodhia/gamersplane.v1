<?
	class pms {
		function __construct() {
			global $loggedIn, $pathOptions;
			if (!$loggedIn) exit;


			if ($pathOptions[0] == 'list' && in_array($_POST['box'], array('inbox', 'outbox'))) 
				$this->displayBox($_POST['box']);
			elseif ($pathOptions[0] == 'allowed' && intval($_POST['pmID'])) 
				$this->checkAllowed($_POST['pmID']);
			elseif ($pathOptions[0] == 'view' && intval($_POST['pmID'])) 
				$this->displayPM($_POST['pmID']);
			elseif ($pathOptions[0] == 'send') 
				$this->sendPM();
			elseif ($pathOptions[0] == 'delete' && intval($_POST['pmID'])) 
				$this->deletePM($_POST['pmID']);
			else 
				displayJSON(array('failed' => true));
		}

		public function displayBox($box) {
			global $mongo, $currentUser;

			if ($box == 'inbox') 
				$search = array('recipients.userID' => $currentUser->userID, 'recipients.deleted' => false);
			else 
				$search = array('sender.userID' => $currentUser->userID);
			$page = isset($_POST['page']) && intval($_POST['page'])?intval($_POST['page']):1;
			$numPMs = $mongo->pms->find($search, array('_id' => 1))->count();
			$pmsResults = $mongo->pms->find($search)->sort(array('datestamp' => -1))->skip(PAGINATE_PER_PAGE * ($page - 1))->limit(PAGINATE_PER_PAGE);
			$pms = array();
			foreach ($pmsResults as $pm) {
				$pm['read'] = true;
				if ($box == 'inbox') {
					$pm['allowDelete'] = true;
					foreach ($pm['recipients'] as $recipient) 
						if ($recipient['userID'] == $currentUser->userID) 
							$pm['read'] = $recipient['read'];
				} else {
					$pm['allowDelete'] = true;
					foreach ($pm['recipients'] as $recipient) {
						if ($recipient['read']) {
							$pm['allowDelete'] = false;
							break;
						}
					}
				}
				$pms[] = $pm;
			}
			displayJSON(array('box' => $box, 'pms' => $pms, 'totalCount' => $numPMs));
		}

		public function displayPM($pmID) {
			require_once(FILEROOT.'/../javascript/markItUp/markitup.bbcode-parser.php');
			global $mongo, $currentUser;

			$pmID = intval($pmID);
			$includeSelfHistory = isset($_POST['includeSelfHistory']) && $_POST['includeSelfHistory']?true:false;

			$pm = $mongo->pms->findOne(array('pmID' => $pmID, '$or' => array(array('sender.userID' => $currentUser->userID), array('recipients.userID' => $currentUser->userID, 'recipients.deleted' => false))));
			if ($pm === null) displayJSON(array('noPM' => true));
			else {
				$pm['title'] = printReady($pm['title']);
				$pm['message'] = BBCode2Html(printReady($pm['message']));
				$pm['allowDelete'] = true;
				$history = $pm['history'];
				if ($pm['sender']['userID'] == $currentUser->userID) {
					foreach ($pm['recipients'] as $recipient) 
						if ($recipient['read'] && !$recipient['deleted']) 
							$pm['allowDelete'] = false;
				} elseif (isset($_POST['markRead']) && $_POST['markRead']) 
					$mongo->pms->update(array('pmID' => $pmID, 'recipients.userID' => $currentUser->userID), array('$set' => array('recipients.$.read' => true)));
				if (sizeof($history)) {
					$pm['history'] = array();
					if ($includeSelfHistory) 
						$pm['history'] = array(
							'pmID' => $pm['pmID'],
							'sender' => $pm['sender'],
							'recipients' => $pm['recipients'],
							'title' => $pm['title'],
							'message' => $pm['message'],
							'datestamp' => $pm['datestamp'],
							'replyTo' => $pm['replyTo'],
						);
					foreach ($history as $pmID) {
						$hPM = $mongo->pms->findOne(array('pmID' => $pmID, '$or' => array(array('sender.userID' => $currentUser->userID), array('recipients.userID' => $currentUser->userID))));
						$hPM['title'] = printReady($hPM['title']);
						$hPM['message'] = BBCode2Html(printReady($hPM['message']));
						$pm['history'][] = $hPM;
						if (sizeof($pm['history']) == 10) 
							break;
					}

				}
				displayJSON($pm);
			}
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