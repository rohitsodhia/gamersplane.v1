<?
	class pms {
		function __construct() {
			global $loggedIn, $pathOptions;
			if (!$loggedIn) exit;

			if ($pathOptions[0] == 'view' && in_array($pathOptions[1], array('inbox', 'outbox'))) 
				$this->displayBox($pathOption[1]);
		}

		public function displayBox($box) {
			global $mysql, $currentUser;
			$pms = $mysql->query("SELECT pms.pmID, pms.senderID, pms.recipientIDs, pms.title, pms.datestamp, c.`read` FROM pms INNER JOIN pms_inBox c ON pms.pmID = c.pmID AND c.userID = {$currentUser->userID} WHERE pms.senderID ".($box == 'inbox'?'!':'')."= {$currentUser->userID} ORDER BY datestamp DESC")->fetchAll(PDO::FETCH_GROUP);
			array_walk($pms, function (&$value, $key) { $value = array_merge(array('pmID' => $key), $value[0]); });
			$userIDs = array();
			foreach ($pms as $pmID => $pm) {
				$userIDs[] = $pm['senderID'];
				$pms[$pmID]['recipientIDs'] = explode(',', $pm['recipientIDs']);
				$userIDs = array_merge($userIDs, explode(',', $pm['recipientIDs']));
				if ($pm['read']) $this->unread++;
			}
			$userIDs = array_unique($userIDs);
			$users = $mysql->query("SELECT userID, username FROM users WHERE userID in (".implode(',', $userIDs).")")->fetchAll(PDO::FETCH_GROUP);
			array_walk($users, function (&$value, $key) { $value = array_merge(array('userID' => $key), $value[0]); });

			foreach ($pms as $pmID => $pm) {
				$pm['sender'] = (object) $users[$pm['senderID']];
				$pm['recipients'] = array();
				foreach ($pm['recipientIDs'] as $recipientID) 
					$pm['recipients'][] = (object) $users[$recipientID];
				unset($pm['senderID'], $pm['recipientIDs']);
				$pms[$pmID] = $pm;
			}
			header('Content-Type: application/json');
			echo json_encode($pms);
		}
	}
?>