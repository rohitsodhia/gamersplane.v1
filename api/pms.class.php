<?
	class pms {
		function __construct() {
			global $loggedIn, $pathOptions;
			if (!$loggedIn) exit;

			if ($pathOptions[0] == 'view' && in_array($_POST['box'], array('inbox', 'outbox'))) 
				$this->displayBox($_POST['box']);
		}

		public function displayBox($box) {
			global $mongo, $currentUser;

			if ($box == 'inbox') 
				$search = array('recipients.userID' => $currentUser->userID);
			else 
				$search = array('sender.userID' => $currentUser->userID);
			$pmsResults = $mongo->pms->find($search)->sort(array('datestamp' => -1));
			$pms = array();
			foreach ($pmsResults as $pm) {
				$pm['read'] = true;
				if ($box == 'inbox') {
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
			header('Content-Type: application/json');
			echo json_encode(array('box' => $box, 'pms' => $pms));
		}
	}
?>