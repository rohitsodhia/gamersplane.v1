<?php
	class pms {
		function __construct() {
			global $loggedIn, $pathOptions;
			if (!$loggedIn) {
				exit;
			}

			if ($pathOptions[0] == 'get') {
				$this->get($_POST['box']);
			} elseif ($pathOptions[0] == 'allowed' && intval($_POST['pmID'])) {
				$this->checkAllowed($_POST['pmID']);
			} elseif ($pathOptions[0] == 'view' && intval($_POST['pmID'])) {
				$this->displayPM($_POST['pmID']);
			} elseif ($pathOptions[0] == 'send') {
				$this->sendPM();
			} elseif ($pathOptions[0] == 'delete' && intval($_POST['pmID'])) {
				$this->deletePM($_POST['pmID']);
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public function get($box) {
			global $currentUser;
			$mongo = DB::conn('mongo');

			$box = strtolower($box);
			if (!in_array($box, ['inbox', 'outbox'])) {
				displayJSON(['failed' => true, 'errors' => ['noBox']]);
			}
			if ($box == 'inbox') {
				$search = ['recipients.userID' => $currentUser->userID, 'recipients.deleted' => false];
			} else {
				$search = ['sender.userID' => $currentUser->userID];
			}
			$page = isset($_POST['page']) && intval($_POST['page']) > 0 ? intval($_POST['page']) : 1;
			$numPMs = $mongo->pms->count($search);
			$pmsResults = $mongo->pms->find(
				$search,
				[
					'sort' => ['datestamp' => -1],
					'skip' => PAGINATE_PER_PAGE * ($page - 1),
					'limit' => PAGINATE_PER_PAGE
				]
			);
			$pms = [];
			foreach ($pmsResults as $pm) {
				$pm = printReady($pm);
				$pm['read'] = true;
				if ($box == 'inbox') {
					$pm['allowDelete'] = true;
					foreach ($pm['recipients'] as $recipient) {
						if ($recipient['userID'] == $currentUser->userID) {
							$pm['read'] = $recipient['read'];
						}
					}
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
			displayJSON(['success' => true, 'box' => $box, 'pms' => $pms, 'totalCount' => $numPMs]);
		}

		public function displayPM($pmID) {
			require_once(FILEROOT . '/javascript/markItUp/markitup.bbcode-parser.php');
			global $currentUser;
			$mongo = DB::conn('mongo');

			$pmID = intval($pmID);
			$includeSelfHistory = isset($_POST['includeSelfHistory']) && $_POST['includeSelfHistory'] ? true : false;

			$pm = $mongo->pms->findOne(
				[
					'pmID' => $pmID,
					'$or' => [
						['sender.userID' => $currentUser->userID],
						['recipients.userID' => $currentUser->userID, 'recipients.deleted' => false]
					]
				]
			);
			if ($pm === null) {
				displayJSON(['noPM' => true]);
			} else {
				$pm = printReady($pm);
				$pm['message'] = BBCode2Html($pm['message']);
				$pm['allowDelete'] = true;
				$history = $pm['history'];
				if ($pm['sender']['userID'] == $currentUser->userID) {
					foreach ($pm['recipients'] as $recipient) {
						if ($recipient['read'] && !$recipient['deleted']) {
							$pm['allowDelete'] = false;
						}
					}
				} elseif (isset($_POST['markRead']) && $_POST['markRead']) {
					$mongo->pms->updateOne(
						[
							'pmID' => $pmID,
							'recipients.userID' => $currentUser->userID
						],
						['$set' => ['recipients.$.read' => true]]
					);
				}
				if (($history && sizeof($history)) || $includeSelfHistory) {
					$pm['history'] = [];
					if ($includeSelfHistory) {
						$pm['history'][] = [
							'pmID' => $pm['pmID'],
							'sender' => $pm['sender'],
							'recipients' => $pm['recipients'],
							'title' => $pm['title'],
							'message' => $pm['message'],
							'datestamp' => $pm['datestamp'],
							'replyTo' => $pm['replyTo'],
						];
					}
					if ($history && sizeof($history)) {
						foreach ($history as $pmID) {
							$hPM = $mongo->pms->findOne(
								[
									'pmID' => $pmID,
									'$or' => [
										['sender.userID' => $currentUser->userID],
										['recipients.userID' => $currentUser->userID]
									]
								]
							);
							$hPM['title'] = printReady($hPM['title']);
							$hPM['message'] = BBCode2Html(printReady($hPM['message']));
							$pm['history'][] = $hPM;
							if (sizeof($pm['history']) == 10) {
								break;
							}
						}
					}
				}
				displayJSON($pm);
			}
		}

		public function checkAllowed($pmID) {
			global $currentUser;
			$mongo = DB::conn('mongo');

			$pmID = intval($pmID);
			$pm = $mongo->pms->findOne(
				[
					'pmID' => $pmID,
					'$or' => [
						['sender.userID' => $currentUser->userID],
						['recipient.userID' => $currentUser->userID, 'deleted' => false]
					]
				]
			);
			displayJSON(['allowed' => $pm ? true : false]);
		}

		public function sendPM() {
			global $currentUser;
			$mysql = DB::conn('mysql');
			$mongo = DB::conn('mongo');

			$sender = (object) ['userID' => $currentUser->userID, 'username' => $currentUser->username];
			$recipient = sanitizeString(preg_replace('/[^\w.]/', '', $_POST['username']));
			$recipient = $mysql->query("SELECT userID, username, email FROM users WHERE username = '{$recipient}'")->fetch(PDO::FETCH_OBJ);
			$recipEmail = $recipient->email;
			unset($recipient->email);
			$recipient->userID = (int) $recipient->userID;
			$recipient->read = false;
			$recipient->deleted = false;
			$replyTo = intval($_POST['replyTo']) > 0 ? intval($_POST['replyTo']) : null;
			if ($sender->userID == $recipient->userID) {
				displayJSON(['mailingSelf' => true]);
			} else {
				$history = null;
				if ($replyTo) {
					$parent = $mongo->pms->findOne(['pmID' => $replyTo]);
					$history = [$replyTo];
					if ($parent['history']) {
						$history = array_merge($history, $parent['history']);
					}
				}
				$mongo->pms->insertOne([
					'pmID' => mongo_getNextSequence('pmID'),
					'sender' => $sender,
					'recipients' => [$recipient],
					'title' => sanitizeString($_POST['title']),
					'message' => sanitizeString($_POST['message']),
					'datestamp' => date('Y-m-d H:i:s'),
					'replyTo' => $replyTo,
					'history' => $history
				]);

				if ($currentUser->getUsermeta('pmMail')) {
					ob_start();
					include('emails/pmEmail.php');
					$email = ob_get_contents();
					ob_end_clean();
					mail($recipEmail, "New PM", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>");
				}

				displayJSON(['sent' => true]);
			}
		}

		public function deletePM($pmID) {
			global $currentUser;
			$mongo = DB::conn('mongo');

			$pmID = intval($pmID);
			$pm = $mongo->pms->findOne(
				[
					'pmID' => $pmID,
					'$or' => [
						['sender.userID' => $currentUser->userID], ['recipients.userID' => $currentUser->userID]
					]
				]
			);
			if ($pm === null) {
				displayJSON(['noMatch' => true]);
			} elseif ($pm['sender']['userID'] == $currentUser->userID) {
				$allowDelete = true;
				foreach ($pm['recipients'] as $recipient) {
					if ($recipient['read'] && !$recipient['deleted']) {
						$allowDelete = false;
					}
				}

				if ($allowDelete) {
					$mongo->pms->deleteOne(['pmID' => $pmID]);
				}

				displayJSON(['deleted' => true]);
			} else {
				$mongo->pms->updateOne(
					['pmID' => $pmID, 'recipients.userID' => $currentUser->userID],
					['$set' => ['recipients.$.deleted' => true]]
				);

				displayJSON(['deleted' => true]);
			}
		}
	}
?>
