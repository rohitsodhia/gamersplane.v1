<?
	class notifications {
		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'user') 
				$this->user();
			else 
				displayJSON(array('failed' => true));
		}

		public function user() {
			global $mongo, $currentUser;

			if (!isset($_POST['userID'])) 
				$userID = $currentUser->userID;
			else 
				$user = (int) $_POST['userID'];

			$page = isset($_POST['page']) && intval($_POST['page'])?intval($_POST['page']):1;
			if ($userID) {
				$numHistories = $mongo->histories->find(array('for.users' => $userID), array('_id' => 1))->count();
				$rHistories = $mongo->histories->find(array('for.users' => $userID))->sort(array('timestamp' => -1))->skip(PAGINATE_PER_PAGE * ($page - 1))->limit(PAGINATE_PER_PAGE);
				$histories = array();
				foreach ($rHistories as $history) {
					$history['_id'] = (string) $history['_id'];
					$history['timestamp'] = $history['timestamp']->sec * 1000;
					$histories[] = $history;
				}
				displayJSON(array('success' => true, 'numHistories' => $numHistories, 'histories' => $histories));
			} else
				displayJSON(array('failed' => true));
		}
	}
?>