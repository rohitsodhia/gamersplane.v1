<?
	class music {
		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'get') 
				$this->getMusic();
			elseif ($pathOptions[0] == 'toggleApproval' && isset($_POST['id']) && isset($_POST['approved'])) 
				$this->toggleApproval($_POST['id'], (bool) $_POST['approved']);
			elseif ($pathOptions[0] == 'addSong') 
				$this->addSong();
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

		public function addSong() {
			global $currentUser, $mongo;

			$url = sanitizeString($_POST['url']);
			$title = sanitizeString($_POST['title']);
			$lyrics = $_POST['lyrics']?true:false;
			$genres = $_POST['genres'];
			$notes = $_POST['notes'];

			$update = false;
			if (isset($_POST['mongoID'])) {
				$update = true;
				$mongoID = $_POST['mongoID'];
			}

			$errors = array();
			if (strlen($url) == 0) 
				$errors[] = 'noURL';
			else {
				preg_match('#https?://(?:www\.)?(.*?)\.([\w\.]*)(?:/.*)?#', $url, $matches);
				$domain = $matches[1].'.'.$matches[2];
				if (!in_array($domain, array('youtube.com', 'soundcloud.com'))) 
					$errors[] = 'invalidURL';
				elseif (!$update) {
					$duplicates = $mongo->music->findOne(array('url' => $url));
					if ($duplicates != null) 
						$errors[] = 'dupURL';
				}
			}
			if (strlen($title) == 0) 
				$errors[] = 'noTitle';
			if (sizeof($genres) == 0) 
				$errors[] = 'noGenres';

			if (sizeof($errors)) 
				displayJSON(array('failed' => true, 'errors' => $errors));
			else {
				if (!$update) {
					$mongo->music->insert(array(
						'userID' => $currentUser->userID,
						'username' => $currentUser->username,
						'url' => $url,
						'title' => $title,
						'lyrics' => $lyrics,
						'genres' => $genres,
						'notes' => $notes,
						'approved' => false
					));
					@mail('contact@gamersplane.com', 'New Music', "New Music:\n\rusername: {$currentUser->username},\n\rurl => $url,\n\rtitle => $title", 'From: noone@gamersplane.com');
				} else 
					$mongo->music->update(array('_id' => new MongoId($mongoID)), array('$set' => array(
						'url' => $url,
						'title' => $title,
						'lyrics' => $lyrics,
						'genres' => $genres,
						'notes' => $notes,
					)));

				displayJSON(array('success' => true, 'data' => array(
					'url' => $url,
					'title' => $title,
					'lyrics' => $lyrics,
					'genres' => $genres,
					'notes' => $notes,
				)));
			}
		}
	}
?>