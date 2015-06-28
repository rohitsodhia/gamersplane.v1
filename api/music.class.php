<?
	class music {
		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'get') 
				$this->getMusic();
			elseif ($pathOptions[0] == 'toggleApproval' && isset($_POST['id']) && isset($_POST['approved'])) 
				$this->toggleApproval($_POST['id'], (bool) $_POST['approved']);
			else 
				displayJSON(array('failed' => true));
		}

		public function getMusic() {
			global $mongo;

			$page = intval($_POST['page']) && (int) $_POST['page'] >= 1?(int) $_POST['page']:1;

			$count = $mongo->music->count();
			$songs = $mongo->music->find()->sort(array('approved' => 1, 'genres' => 1, 'title' => 1))->skip(10 * ($page - 1))->limit(10);
			$music = array();
			foreach ($songs as $song) {
				$song['id'] = $song['_id']->{'$id'};
				unset($song['_id']);
				$music[] = $song;
			}

			displayJSON(array('success' => true, 'count' => $count, 'music' => $music));
		}

		public function toggleApproval($id, $approved) {
			global $mongo, $currentUser;

			if (!$currentUser->checkACP('music')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermission')));

			$updated = $mongo->music->update(array('_id' => new MongoId($id)), array('$set' => array('approved' => !$approved)));

			if ($updated['updatedExisting']) 
				displayJSON(array('success' => true));
			else 
				displayJSON(array('failed' => true));
		}
	}
?>