<?php
	class notifications {
		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'user') {
				$this->user();
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public function user() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			if (!isset($_POST['userID'])) {
				$userID = $currentUser->userID;
			} else {
				$user = (int) $_POST['userID'];
			}

			$page = isset($_POST['page']) && intval($_POST['page']) ? intval($_POST['page']) : 1;
			if ($userID) {
				$numHistories = count($mongo->histories->find(
					['for.users' => $userID],
					['projection' => ['_id' => 1]]
				));
				$rHistories = $mongo->histories->find(
					['for.users' => $userID],
					[
						'sort' => ['timestamp' => -1],
						'skip' => PAGINATE_PER_PAGE * ($page - 1),
						'limit' => PAGINATE_PER_PAGE
					]
				);
				$histories = [];
				foreach ($rHistories as $history) {
					$history['_id'] = (string) $history['_id'];
					$history['timestamp'] = getMongoSeconds($history['timestamp']) * 1000;
					$histories[] = $history;
				}
				displayJSON(['success' => true, 'numHistories' => $numHistories, 'histories' => $histories]);
			} else {
				displayJSON(['failed' => true]);
			}
		}
	}
?>
