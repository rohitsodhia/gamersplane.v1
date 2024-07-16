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
			$mysql = DB::conn('mysql');

			$box = strtolower($box);
			if (!in_array($box, ['inbox', 'outbox'])) {
				displayJSON(['failed' => true, 'errors' => ['noBox']]);
			}
			if ($box == 'inbox') {
				$where = "recipientID = {$currentUser->userID} AND recipientDeleted = 0";
			} else {
				$where = "senderID = {$currentUser->userID} AND senderDeleted = 0";
			}
			$page = isset($_POST['page']) && intval($_POST['page']) > 0 ? intval($_POST['page']) : 1;
			$numPMs = $mysql->query("SELECT COUNT(*) as `count` FROM pms WHERE {$where}")->fetchColumn();
			$page = PAGINATE_PER_PAGE * ($page - 1);
			$paginatePerPage = PAGINATE_PER_PAGE;
			$pmsResults = $mysql->query("SELECT pmID, senderID, sender.username senderUsername, recipientID, recipient.username recipientUsername, title, message, datestamp, `read`, replyTo, history FROM pms INNER JOIN users sender ON pms.senderID = sender.userID INNER JOIN users recipient ON pms.recipientID = recipient.userID WHERE {$where} ORDER BY datestamp DESC LIMIT {$page}, {$paginatePerPage}");
			$pms = [];
			foreach ($pmsResults as $pm) {
				$pm = printReady($pm);
				$pm['read'] = (bool) $pm['read'];
				foreach (['pmID', 'senderID', 'recipientID'] as $intKey) {
					$pm[$intKey] = (int) $pm[$intKey];
				}
				$pms[] = $pm;
			}
			displayJSON(['success' => true, 'box' => $box, 'pms' => $pms, 'totalCount' => $numPMs]);
		}

		public function displayPM($pmID) {
			require_once(FILEROOT . '/javascript/markItUp/markitup.bbcode-parser.php');
			global $currentUser;
			$mysql = DB::conn('mysql');

			$pmID = intval($pmID);
			$includeSelfHistory = isset($_POST['includeSelfHistory']) && $_POST['includeSelfHistory'] ? true : false;

			$pm = $mysql->query("SELECT pmID, sender.userID senderID, sender.username senderUsername, recipient.userID recipientID, recipient.username recipientUsername, title, message, datestamp, `read`, replyTo, history FROM pms INNER JOIN users sender ON pms.senderID = sender.userID INNER JOIN users recipient ON pms.recipientID = recipient.userID WHERE pmID = {$pmID} AND (sender.userID = {$currentUser->userID} OR (recipient.userID = {$currentUser->userID} AND recipientDeleted = 0))");
			if (!$pm->rowCount()) {
				displayJSON(['noPM' => true]);
			} else {
				$pm = printReady($pm->fetch());
				$pm['message'] = BBCode2Html($pm['message']);
				$pm['read'] = (bool) $pm['read'];
				$pm['sender'] = [
					'userID' => (int) $pm['senderID'],
					'username' => $pm['senderUsername']
				];
				$pm['recipients'] = [[
					'userID' => (int) $pm['recipientID'],
					'username' => $pm['recipientUsername']
				]];
				$history = json_decode($pm['history']);
				if (isset($_POST['markRead']) && $_POST['markRead'] && !$pm['read']) {
					$mysql->query("UPDATE pms SET `read` = 1 WHERE pmID = {$pmID}");
				}
				if (($history && sizeof($history)) || $includeSelfHistory) {
					$pm['history'] = [];
					if ($includeSelfHistory) {
						$pm['history'][] = [
							'pmID' => (int) $pm['pmID'],
							'sender' => $pm['sender'],
							'recipients' => $pm['recipients'],
							'title' => $pm['title'],
							'message' => $pm['message'],
							'datestamp' => $pm['datestamp'],
							'replyTo' => (int) $pm['replyTo'],
						];
					}
					if ($history && sizeof($history)) {
						$historyPMs = $mysql->query("SELECT SELECT pmID, sender.userID senderID, sender.username senderUsername, recipient.userID recipientID, recipient.username recipientUsername, title, message, datestamp, `read`, replyTo, history FROM pms INNER JOIN users sender ON pms.senderID = sender.userID INNER JOIN users recipient ON pms.recipientID = recipient.userID WHERE pmID IN (" . implode(',', $history) . ")");
						foreach ($historyPMs->fetchAll() as $hPM) {
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
			$mysql = DB::conn('mysql');

			$pmID = intval($pmID);
			$pm = $mysql->query("SELECT pmID FROM pms INNER JOIN users sender ON pms.senderID = sender.userID INNER JOIN users recipient ON pms.recipientID = recipient.userID WHERE pmID = {$pmID} AND (sender.userID = {$currentUser->userID} OR (recipient.userID = {$currentUser->userID} AND recipientDeleted = 0))");
			displayJSON(['allowed' => $pm->rowCount() ? true : false]);
		}

		public function sendPM() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$recipient = sanitizeString(preg_replace('/[^\w.]/', '', $_POST['username']));
			$recipient = $mysql->query("SELECT userID, email FROM users WHERE username = '{$recipient}'")->fetch(PDO::FETCH_OBJ);
			$recipEmail = $recipient->email;
			$recipient->userID = (int) $recipient->userID;
			$replyTo = intval($_POST['replyTo']) > 0 ? intval($_POST['replyTo']) : null;
			if ($currentUser->userID == $recipient->userID) {
				displayJSON(['mailingSelf' => true]);
			} else {
				$history = null;
				if ($replyTo) {
					$parent = $mysql->query("SELECT history FROM pms WHERE pmID = {$replyTo}")->fetch();
					$history = [$replyTo];
					if ($parent['history']) {
						$history = array_merge($history, $parent['history']);
					}
				}
				$addPM = $mysql->prepare("INSERT INTO pms SET recipientID = :recipientID, senderID = :senderID, title = :title, message = :message, datestamp = NOW(), replyTo = :replyTo, history = :history");
				$addPM->execute([
					'recipientID' => $recipient->userID,
					'senderID' => $currentUser->userID,
					'title' => sanitizeString($_POST['title']),
					'message' => sanitizeString($_POST['message']),
					'replyTo' => $replyTo,
					'history' => $history
				]);

				$sendMail = $mysql->query("SELECT metaValue FROM usermeta WHERE userID = {$recipient->userID} AND metaKey = 'pmMail'")->fetchColumn();
				if ($sendMail) {
					ob_start();
					include('emails/pmEmail.php');
					$email = ob_get_contents();
					ob_end_clean();

					$mail = getMailObj();
					$mail->addAddress($recipEmail);
					$mail->Subject = "New PM";
					$mail->msgHTML($email);
					$mail->send();
				}

				displayJSON(['sent' => true]);
			}
		}

		public function deletePM($pmID) {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$pmID = intval($pmID);
			$getPM = $mysql->query("SELECT senderID FROM pms WHERE pmID = {$pmID} AND (senderID = {$currentUser->userID} OR recipientID = {$currentUser->userID})");
			if (!$getPM->rowCount()) {
				displayJSON(['noMatch' => true]);
			}
			$pm = $getPM->fetch();
			if ($pm['senderID'] == $currentUser->userID) {
				$key = 'senderDeleted';
			} else {
				$key = 'recipientDeleted';
			}
			$mysql->query("UPDATE pms SET {$key} = 1 WHERE pmID = {$pmID}");

			displayJSON(['deleted' => true]);
		}
	}
?>
