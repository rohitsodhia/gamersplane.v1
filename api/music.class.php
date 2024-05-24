<?php
	require_once(FILEROOT.'/includes/tools/Music_consts.class.php');

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
			$mysql = DB::conn('mysql');

			$itemsPerPage = 20;
			$page = intval($_POST['page']) && (int) $_POST['page'] >= 1 ? (int) $_POST['page'] : 1;

			$filter = isset($_POST['filter']) ? array_intersect((array) $_POST['filter'], Music_consts::getGenres()) : [];
			if (sizeof($filter) && sizeof($filter['genres'])) {
				$filter['genres'] = "genres IN ('" . implode("', '", $filter['genres']) . "')";
			} elseif (sizeof($filter) && sizeof($filter['genres']) == 0) {
				unset($filter['genres']);
			}
			if (sizeof($filter) && sizeof($filter['lyrics']) == 1) {
				$filter['lyrics'] = "lyrics = " . array_search('hasLyrics', $filter['lyrics']) !== false ? 1 : 0;
			} elseif ((sizeof($filter) && sizeof($filter['lyrics']) == 2) || sizeof($filter['lyrics']) == 0) {
				unset($filter['lyrics']);
			}
			$count = $mysql->query("SELECT COUNT(*) as `count` FROM music" . (sizeof($filter) ? ' WHERE ' . implode(' AND ', $filter) : ''))->fetchColumn();
			$musicQuery = $mysql->query("SELECT * FROM music" . (sizeof($filter) ? ' WHERE ' . implode(' AND ', $filter) : '') . " ORDER BY approved DESC, title LIMIT {$itemsPerPage * ($page - 1)}, {$itemsPerPage}");
			$songs = $musicQuery->fetchAll();
			$music = [];
			foreach ($songs as $rawSong) {
				$song['id'] = $rawSong['id'];
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
			$mysql = DB::conn('mysql');

			if (!$currentUser->checkACP('music')) {
				displayJSON(['failed' => true, 'errors' => ['noPermission']]);
			}

			$mysql->query("UPDATE music SET approved = NOT approved WHERE id = {$songID}");

			if ($updated['updatedExisting']) {
				displayJSON(['success' => true]);
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public function saveSong() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$url = sanitizeString($_POST['url']);
			$title = sanitizeString($_POST['title']);
			$lyrics = $_POST['lyrics'] ? true : false;
			$genres = $_POST['genres'] ? $_POST['genres'] : [];
			$battlebards = $_POST['battlebards'] ? true : false;
			$notes = $_POST['notes'];

			$update = false;
			if (isset($_POST['id'])) {
				$update = true;
				$id = (int) $_POST['id'];
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
					$duplicates = $mysql->prepare("SELECT id FROM music WHERE url = :url");
					$duplicates->execute($url);
					$duplicates = $mysql->music->findOne(['url' => $url]);
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
					$addMusic = $mysql->prepare("INSERT INTO music SET user = :user, url = :url, title = :title, lyrics = :lyrics, genres = :genres, notes = :notes");
					$addMusic->execute([':user' => $currentUser->userID, ':url' => $url, ':title' => $title, ':lyrics' => $lyrics, ':genres' => $genres, ':notes' => $notes]);

					$mail = getMailObj();
					$mail->addAddress("contact@gamersplane.com");
					$mail->Subject = "New Music";
					$mail->Body = "New Music:\n\rusername: {$currentUser->username},\n\rurl => $url,\n\rtitle => $title";
					$mail->send();
				} else {
					$updateMusic = $mysql->prepare("UPDATE music SET user = :user, url = :url, title = :title, lyrics = :lyrics, genres = :genres, notes = :notes WHERE id = :id");
					$updateMusic->execute(['id' => $id, ':user' => $currentUser->userID, ':url' => $url, ':title' => $title, ':lyrics' => $lyrics, ':genres' => $genres, ':notes' => $notes]);
				}

				$song = $mysql->query("SELECT * FROM music WHERE id = {$id}")->fetch();
				if ($song) {
					displayJSON(['success' => true, 'song' => $song]);
				}
			}
		}
	}
?>
