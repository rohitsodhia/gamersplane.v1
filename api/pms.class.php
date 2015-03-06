<?
	class pms {
		function __construct() {
			global $loggedIn, $pathOptions;
			if (!$loggedIn) exit;

			if ($pathOptions[0] == 'view' && in_array($pathOptions[1], array('inbox', 'outbox'))) 
				$this->displayBox($pathOption[1]);
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
				if ($box == 'inbox') 
					foreach ($pm['recipients'] as $recipient) 
						if ($recipient['userID'] == $currentUser->userID) 
							$pm['read'] = $recipient['read'];
				$pms[] = $pm;
			}
			header('Content-Type: application/json');
			echo json_encode($pms);
		}
	}
?>