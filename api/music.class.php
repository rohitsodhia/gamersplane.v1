<?php
	class music {
		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'get') {
				$this->getMusic();
			} elseif ($pathOptions[0] == 'toggleApproval' && isset($_POST['_id']) && isset($_POST['approved'])) {
				$this->toggleApproval($_POST['_id'], (bool) $_POST['approved']);
			} elseif ($pathOptions[0] == 'saveSong') {
				$this->saveSong();
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public function getMusic() {
			$mongo = DB::conn('mongo');

			$itemsPerPage = 20;
			$page = intval($_POST['page']) && (int) $_POST['page'] >= 1 ? (int) $_POST['page'] : 1;

			$filter = isset($_POST['filter']) ? (array) $_POST['filter'] : [];
			if (sizeof($filter) && sizeof($filter['genres'])) {
				$filter['genres'] = ['$in' => $filter['genres']];
			} elseif (sizeof($filter) && sizeof($filter['genres']) == 0) {
				unset($filter['genres']);
			}
			if (sizeof($filter) && sizeof($filter['lyrics']) == 1) {
				$filter['lyrics'] = array_search('hasLyrics', $filter['lyrics']) !== false ? true : false;
			} elseif ((sizeof($filter) && sizeof($filter['lyrics']) == 2) || sizeof($filter['lyrics']) == 0) {
				unset($filter['lyrics']);
			}

			$count = $mongo->music->count($filter);
			$songs = $mongo->music->find(
				$filter,
				[
					'sort' => ['approved' => 1, 'title' => 1],
					'skip' => $itemsPerPage * ($page - 1),
					'limit' => $itemsPerPage
				]
			);
			$music = [];
			foreach ($songs as $rawSong) {
				$song['_id'] = $rawSong['_id']->{'$id'};
				$song['url'] = $rawSong['url'];
				$song['title'] = $rawSong['title'];
				$song['approved'] = (bool) $rawSong['approved'];
				$song['lyrics'] = $rawSong['lyrics'] ? true : false;
				$song['genres'] = is_array($rawSong['genres']) ? $rawSong['genres'] : [];
				$song['battlebards'] = $rawSong['battlebards'] ? true : false;
				$song['notes'] = strlen($rawSong['notes']) ? printReady($rawSong['notes']) : null;
				$music[] = $song;
			}

			displayJSON(['success' => true, 'count' => $count, 'music' => $music]);
		}

		public function toggleApproval($id, $approved) {
			global $currentUser;
			$mongo = DB::conn('mongo');

			if (!$currentUser->checkACP('music')) {
				displayJSON(['failed' => true, 'errors' => ['noPermission']]);
			}

			$updated = $mongo->music->updateOne(['_id' => genMongoId($id)], ['$set' => ['approved' => !$approved]]);

			if ($updated['updatedExisting']) {
				displayJSON(['success' => true]);
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public function saveSong() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			$url = sanitizeString($_POST['url']);
			$title = sanitizeString($_POST['title']);
			$lyrics = $_POST['lyrics'] ? true : false;
			$genres = $_POST['genres'];
			$battlebards = $_POST['battlebards'] ? true : false;
			$notes = $_POST['notes'];

			$update = false;
			if (isset($_POST['_id'])) {
				$update = true;
				$mongoID = $_POST['_id'];
			}

			$errors = [];
			if (strlen($url) == 0) {
				$errors[] = 'noURL';
			} else {
				preg_match('#https?://(?:www\.)?(.*?)\.([\w\.]*)(?:/.*)?#', $url, $matches);
				$domain = $matches[1].'.'.$matches[2];
				if (!in_array($domain, ['youtube.com', 'soundcloud.com'])) {
					$errors[] = 'invalidURL';
				} elseif (!$update) {
					$duplicates = $mongo->music->findOne(['url' => $url]);
					if ($duplicates != null) {
						$errors[] = 'dupURL';
					}
				}
			}
			if (strlen($title) == 0) {
				$errors[] = 'noTitle';
			}
			if (sizeof($genres) == 0) {
				$errors[] = 'noGenres';
			}

			if (sizeof($errors)) {
				displayJSON(['failed' => true, 'errors' => $errors]);
			} else {
				if (!$update) {
					$mongoID = genMongoId();
					$mongo->music->insertOne([
						'_id' => $mongoID,
						'user' => [
							'userID' => $currentUser->userID,
							'username' => $currentUser->username
						],
						'url' => $url,
						'title' => $title,
						'lyrics' => $lyrics,
						'genres' => $genres,
						'battlebards' => $battlebards,
						'notes' => $notes,
						'approved' => $currentUser->checkACP('music', false) ? true : false
					]);
					@mail('contact@gamersplane.com', 'New Music', "New Music:\n\rusername: {$currentUser->username},\n\rurl => $url,\n\rtitle => $title", 'From: noone@gamersplane.com');
				} else {
					$mongo->music->updateOne(
						['_id' => genMongoId($mongoID)],
						['$set' => [
							'url' => $url,
							'title' => $title,
							'lyrics' => $lyrics,
							'genres' => $genres,
							'battlebards' => $battlebards,
							'notes' => $notes
						]]
					);
				}

				$song = $mongo->music->findOne(['_id' => genMongoId($mongoID)]);
				if ($song) {
					$song['id'] = (string) $song['_id'];
					unset($song['_id']);
					displayJSON(['success' => true, 'song' => $song]);
				}
			}
		}
	}
?>
