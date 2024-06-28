<?php
	class users {
		const USERS_PER_PAGE = 25;

		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'gamersList') {
				$this->gamersList();
			} elseif ($pathOptions[0] == 'search') {
				$this->search();
			} elseif ($pathOptions[0] == 'getCurrentUser') {
				$this->getCurrentUser();
			} elseif ($pathOptions[0] == 'getHeader') {
				$this->getHeader();
			} elseif ($pathOptions[0] == 'get') {
				$this->getUser();
			} elseif ($pathOptions[0] == 'save') {
				$this->saveUser();
			} elseif ($pathOptions[0] == 'suspend') {
				$this->suspend();
			} elseif ($pathOptions[0] == 'ban') {
				$this->ban();
			} elseif ($pathOptions[0] == 'stats') {
				$this->stats();
			} elseif ($pathOptions[0] == 'getLFG') {
				$this->getLFG();
			} elseif ($pathOptions[0] == 'saveLFG') {
				$this->saveLFG();
			} elseif ($pathOptions[0] == 'removeThreadNotification') {
				$this->removeThreadNotification($_POST['postID']);
			} elseif ($pathOptions[0] == 'removeAllThreadNotifications') {
				$this->removeAllThreadNotifications();
			} elseif ($pathOptions[0] == 'setUserTheme') {
				$this->setUserTheme($_POST['darkTheme']);
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public function gamersList() {
			$mysql = DB::conn('mysql');

			$rUsers = $mysql->query('SELECT users.userID, users.username, users.lastActivity, users.joinDate, IF(lastActivity >= UTC_TIMESTAMP() - INTERVAL 15 MINUTE, 1, 0) online, avatar.avatarExt, lfg.lfgStatus FROM users LEFT OUTER JOIN (SELECT userID, metaValue AS lfgStatus FROM usermeta AS usermeta_lfg WHERE (metaKey = "lookingForAGame")) AS lfg ON users.userID = lfg.userID LEFT OUTER JOIN (SELECT userID, metaValue AS avatarExt FROM usermeta AS usermeta_ava WHERE (metaKey = "avatarExt")) AS avatar ON users.userID = avatar.userID WHERE activatedOn IS NOT NULL' . (!isset($_POST['showInactive']) || !$_POST['showInactive'] ? ' AND lastActivity >= UTC_TIMESTAMP() - INTERVAL 2 WEEK' : '').' ORDER BY online DESC, username')->fetchAll();
			$users = [];
			$total=0;
			if (sizeof($rUsers)) {
				foreach ($rUsers as $user) {
					$user['userID'] = (int) $user['userID'];
					$user['online'] = (bool) $user['online'];
					$user['avatar'] = $user['avatarExt']? "/ucp/avatars/{$user['userID']}.{$user['avatarExt']}": "/ucp/avatars/avatar.png";
					$user['inactive'] = User::inactive($user['lastActivity']);
					unset($user['lastActivity']);
					unset($user['avatarExt']);
					$users[] = $user;
					$total++;
				}
				displayJSON(['users' => $users, 'totalUsers' => (int) $total]);
			} else
				displayJSON(['noUsers' => true]);
		}

		public function search() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$search = sanitizeString(preg_replace('/[^\w.]/', '', $_GET['search']), 'lower');
			$fields = isset($_GET['fields']) && strlen($_GET['fields']) ? 'userID, username, activatedOn, suspendedUntil, banned' : 'userID, username';
			if (isset($_GET['exact']) && (bool) $_GET['exact'] == true) {
				$searchBy = isset($_GET['searchBy']) && in_array($_GET['searchBy'], ['username', 'userID']) ? $_GET['searchBy'] : 'username';
				if ($searchBy == 'userID') {
					$search = intval($search);
					$user = $mysql->query("SELECT {$fields} FROM users WHERE userID = {$search} LIMIT 1")->fetch();
				} else {
					$user = $mysql->query("SELECT {$fields} FROM users WHERE username = '{$search}' LIMIT 1")->fetch();
				}

				if ($user) {
					$user['userID'] = (int) $user['userID'];
					if (isset($user['activatedOn'])) {
						$user['activatedOn'] = strtotime('2020-04-01'); //only checking if null
					}
					if (isset($user['suspendedUntil']) && $user['suspendedUntil'] != null) {
						$user['suspendedUntil'] = strtotime($user['suspendedUntil']);
					}
					if (isset($user['banned'])) {
						$user['banned'] = (bool) $user['banned'];
					}
					displayJSON(['users' => [$user]]);
				} else {
					displayJSON(['noUsers' => true]);
				}
			} else {
				$limit = (int) $_GET['limit'] > 0 ? (int) $_GET['limit'] : 5;
				$page = (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
				$loadType = array_search($_GET['loadType'], ['all', 'active', 'inactive', 'suspended']) ? $_GET['loadType'] : 'all';
				$typeQuery = '';
				if ($loadType == 'active') {
					$typeQuery = ' activatedOn IS NOT NULL';
				} elseif ($loadType == 'inactive') {
					$typeQuery = ' activatedOn IS NULL';
				} elseif ($loadType == 'suspended') {
					$typeQuery = ' suspendedUntil IS NOT NULL';
				}
				if ($typeQuery != '') {
					if (strlen($search)) {
						$typeQuery = ' AND' . $typeQuery;
					} else {
						$typeQuery = ' WHERE' . $typeQuery;
					}
				}
				$valid = $mysql->query("SELECT {$fields} FROM users" . (strlen($search) ? " WHERE (username='{$search}' OR username LIKE '%{$search}%')" : '') . $typeQuery .(strlen($search) ? " ORDER BY CASE WHEN username='{$search}' THEN 1 ELSE 2 END" : ''). ' LIMIT ' . (($page - 1) * $limit) . ', ' . $limit);
				$numUsers = $mysql->query("SELECT COUNT(userID) numUsers FROM users" . (strlen($search) ? " WHERE (username='{$search}' OR username LIKE '%{$search}%')" : '') . $typeQuery)->fetchColumn();
				if ($valid->rowCount()) {
					$users = [];
					foreach ($valid as $user) {
						$user['userID'] = (int) $user['userID'];
						if (isset($user['activatedOn'])) {
							$user['activatedOn'] = strtotime('2020-04-01');  //only checking if null
						}
						if (isset($user['suspendedUntil']) && $user['suspendedUntil'] != null) {
							$user['suspendedUntil'] = strtotime($user['suspendedUntil']);
						}
						if (isset($user['banned'])) {
							$user['banned'] = (bool) $user['banned'];
						}
						if (isset($_GET['md5']) && $currentUser->checkACP('users', false)) {
							$user['userHash'] = md5($user['username']);
						}
						$users[] = $user;
					}
					displayJSON(['users' => $users, 'numUsers' => (int) $numUsers]);
				} else {
					displayJSON(['noUsers' => true]);
				}
			}
		}

		public function getCurrentUser() {
			global $loggedIn, $currentUser;

			if (!$loggedIn) {
				displayJSON(['failed' => true, 'loggedOut' => true]);
			} else {
				$cleanUser = [
					'userID' => $currentUser->userID,
					'username' => $currentUser->username,
					'email' => $currentUser->email,
					'joinDate' => $currentUser->joinDate,
					'activatedOn' => $currentUser->activatedOn,
					'timezone' => $currentUser->timezone,
					'usermeta' => $currentUser->usermeta,
					'acpPermissions' => $currentUser->acpPermissions
				];
				displayJSON($cleanUser);
			}
		}

		public function getHeader() {
			global $loggedIn, $currentUser;
			$mysql = DB::conn('mysql');

			if (!$loggedIn) {
				displayJSON(['failed' => true]);
			}

			$getCharacters = $mysql->query("SELECT characters.characterID, characters.label, characters.system, IF(favorites.userID, 1, 0) isFavorite FROM characters LEFT JOIN characterLibrary_favorites favorites ON characters.characterID = favorites.characterID WHERE (characters.userID = {$currentUser->userID} OR favorites.userID = {$currentUser->userID}) AND characters.retired = NULL ORDER BY isFavorite DESC LIMIT 6");

			$characters = [];
			$hasFavorites = FALSE;
			foreach ($getCharacters->fetchAll() as $char) {
				if ($char['isFavorite']) {
					$hasFavorites = TRUE;
				} elseif ($hasFavorites) {
					break;
				}
				$characters[] = $char;
			}

			$games = $mysql->query("SELECT games.gameID, games.title, games.forumID, players.isGM, IF(players.userID, TRUE, FALSE) isPlayer FROM games LEFT JOIN players ON games.gameID = players.gameID LEFT JOIN games_favorites favorites ON games.gameID = favorites.gameID WHERE (players.userID = {$currentUser->userID} OR favorites.userID = {$currentUser->userID}) AND games.retired = NULL LIMIT 6")->fetchAll();

			usort($games, function($a, $b) {
				if ($a['isPlayer'] != $b['isPlayer']){
					return $a['isPlayer'] > $b['isPlayer'] ? -1 : 1;
				}
				$aTitle = trim(strtolower(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', mb_convert_encoding($a['title'], "UTF-8"))));
				$bTitle = trim(strtolower(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', mb_convert_encoding($b['title'], "UTF-8"))));
				return $aTitle > $bTitle ? 1 : ($aTitle < $bTitle ? -1 : 0);
			});

			$pmCount = $mysql->query("SELECT COUNT(*) as `count` FROM pms WHERE recipientID = {$currentUser->userID} AND `read` = 0 AND senderDeleted = 0 and recipientDeleted = 0")->fetchColumn();

			displayJSON([
				'success' => true,
				'characters' => $characters,
				'games' => $games,
				'avatar' => User::getAvatar($currentUser->userID),
				'pmCount' => $pmCount
			]);
		}

		public function getUser() {
			global $loggedIn, $currentUser;

			if (isset($_POST['userID'])) {
				$user = new User(intval($_POST['userID']));
			} elseif (!isset($_POST['userID'])) {
				$user = $currentUser;
			}
			if (!$user){
				displayJSON(['failed' => true, 'noUser' => true]);
			}
			if ($loggedIn) {
				$getAll = $currentUser->checkACP('users', false) || $user->userID == $currentUser->userID;
			}
			$user->getAllUsermeta();

			$details = [
				'userID' => $user->userID,
				'username' => $user->username,
				'joinDate' => $user->joinDate,
				'lastActivity' => $user->lastActivity,
				'avatar' => [
					'url' => User::getAvatar($user->userID, $user->avatarExt),
					'avatarExt' => $user->avatarExt
				],
				//'gender' => $user->gender ? $user->gender : 'n',
				'pronoun' => $user->pronoun,
				'birthday' => [
					'showAge' => $user->showAge?true:false
				],
				'location' => $user->location,
				'twitter' => $user->twitter,
				'stream' => $user->stream,
				'games' => $user->games,
				'theme' =>  $user->theme??'',
				'warnUnsaved' =>  $user->warnUnsaved??'',
				'lookingForAGame' => $user->lookingForAGame ? $user->lookingForAGame : "0",
			];
			if ($getAll) {
				$details = array_merge($details, [
					'email' => $user->email,
					'birthday' => [
						'date' => $user->birthday,
						'showAge' => $user->showAge ? true : false
					],
					'pmMail' => $user->pmMail ? true : false,
					'newGameMail' => $user->newGameMail ? true : false,
					'gmMail' => $user->gmMail ? true : false,
					'postSide' => $user->postSide,
					'pog_participate' => $user->usermeta['pog_participate'] ? true: false,
					'pog_interests' => $user->usermeta['pog_interests'] ?? '',
					'pog_gift_sent' => $user->usermeta['pog_gift_sent'] ? true: false,
					'pog_gift_recieved' => $user->usermeta['pog_gift_recieved'] ? true: false,
				]);
				if ($user->usermeta['pog_participate']) {
					$pog23_giftee = new User(intval($user->usermeta['pog23_giftee']));
					$pog23_giftee->getAllUsermeta();
					$details = array_merge($details, [
						'pog_assignee' => $pog23_giftee->username,
						'pog_assignee_id' => $pog23_giftee->userID,
						'pog_assignee_email' => $pog23_giftee->email,
						'pog_assignee_interests' => $pog23_giftee->usermeta['pog_interests'] ?? 'None provided',
					]);
				}
			}
			if ($details['birthday']['showAge']) {
				$now = new DateTime();
				$birthday = new DateTime($user->birthday);
				$details['birthday']['age'] = (int) $now->diff($birthday)->y;
			}
			displayJSON(['success' => true, 'details' => $details]);
		}

		public function saveUser() {
			global $loggedIn, $currentUser;

			if (!$loggedIn && $currentUser->userID != $_POST['userID'] && !$currentUser->checkACP('users', false)) {
				displayJSON([
					'failed' => true,
					'noAuth' => true
				]);
			}

			$details = $_POST['details'];
			$newPass = $_POST['newPass'];
			$plane_of_giving = $_POST['plane_of_giving'];
			$userID = (int) $details['userID'];
			if ($currentUser->userID != $userID) {
				$user = new User($userID);
			} else {
				$user = $currentUser;
			}

			$avatarUploaded = false;
			if (isset($details['avatar']['delete']) && $details['avatar']['delete'] == 'true') {
				@unlink(FILEROOT . "/ucp/avatars/{$user->userID}.jpg");
			}
			if ($_FILES['file']['error'] == 0 && $_FILES['file']['size'] > 15 && $_FILES['file']['size'] < 1048576) {
				$avatarExt = trim(end(explode('.', strtolower($_FILES['file']['name']))));
				if ($avatarExt == 'jpeg') {
					$avatarExt = 'jpg';
				}
				if (in_array($avatarExt, ['jpg', 'gif', 'png'])) {
					$maxWidth = 150;
					$maxHeight = 150;

					list($imgWidth, $imgHeight, $imgType) = getimagesize($_FILES['file']['tmp_name']);
					if ($imgWidth >= $maxWidth && $imgHeight >= $maxHeight) {
						if (image_type_to_mime_type($imgType) == 'image/jpeg' || image_type_to_mime_type($imgType) == 'image/pjpeg') {
							$tempImg = imagecreatefromjpeg($_FILES['file']['tmp_name']);
						} elseif (image_type_to_mime_type($imgType) == 'image/gif') {
							$tempImg = imagecreatefromgif($_FILES['file']['tmp_name']);
						} elseif (image_type_to_mime_type($imgType) == 'image/png') {
							$tempImg = imagecreatefrompng($_FILES['file']['tmp_name']);
						}

						$xRatio = $maxWidth / $imgWidth;
						$yRatio = $maxHeight / $imgHeight;

						if ($imgWidth <= $maxWidth && $imgHeight <= $maxHeight) {
							$finalWidth = $imgWidth;
							$finalHeight = $imgHeight;
						} elseif (($xRatio * $imgHeight) < $maxHeight) {
							$finalWidth = $maxWidth;
							$finalHeight = ceil($xRatio * $imgHeight);
						} else {
							$finalWidth = ceil($yRatio * $imgWidth);
							$finalHeight = $maxHeight;
						}

						$tempColor = imagecreatetruecolor($finalWidth, $finalHeight);
						imagealphablending($tempColor, false);
						imagesavealpha($tempColor,true);
						imagecopyresampled($tempColor, $tempImg, 0, 0, 0, 0, $finalWidth, $finalHeight, $imgWidth, $imgHeight);

						$destination = FILEROOT . '/ucp/avatars/' . $user->userID . '.' . $avatarExt;
						foreach (glob(FILEROOT . '/ucp/avatars/' . $user->userID . '.*') as $oldFile) {
							unlink($oldFile);
						}
						if ($avatarExt == 'jpg') {
							imagejpeg($tempColor, $destination, 100);
						} elseif ($avatarExt == 'gif') {
							imagegif($tempColor, $destination);
						} elseif ($avatarExt == 'png') {
							imagepng($tempColor, $destination, 0);
						}
						imagedestroy($tempImg);
						imagedestroy($tempColor);
						$fileUploaded = true;
					}
				} elseif ($avatarExt == 'svg') {
					foreach (glob(FILEROOT . '/ucp/avatars/' . $user->userID . '.*') as $oldFile) {
						unlink($oldFile);
					}
					move_uploaded_file($_FILES['file']['tmp_name'], FILEROOT . "/ucp/avatars/{$user->userID}.svg");
					$fileUploaded = true;
				}

				if ($avatarExt == '') {
					$avatarExt = null;
				}
				$user->updateUsermeta('avatarExt', $avatarExt, true);
				$avatarUploaded = true;
			}

//			$user->updateUsermeta('showAvatars', isset($data->showAvatars)?1:0);
/*
			if ($details['gender'] == 'n') {
				$gender = '';
			} else {
				$gender = $details['gender'] == 'm' ? 'm' : 'f';
			}
			$user->updateUsermeta('gender', $gender);
			*/
			if ($details['pronoun'] == 'null') {
				$details['pronoun'] = '';
			}
			$user->updateUsermeta('pronoun', sanitizeString($details['pronoun']));

			if($details['birthday']['date']){
				$birthday = intval($details['birthday']['date']['year']) . '-' . (intval($details['birthday']['date']['month']) <= 9 ? '0' : '') . intval($details['birthday']['date']['month']) . '-' . (intval($details['birthday']['date']['day']) <= 9 ? '0' : '') . intval($details['birthday']['date']['day']);
				if (preg_match('/^[12]\d{3}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/', $birthday)) {
					$user->updateUsermeta('birthday', $birthday);
				}
			}
			$user->updateUsermeta('showAge', $details['birthday']['showAge'] === 'true' ? 1 : 0);
			if ($details['location'] == 'null') {
				$details['location'] = '';
			}
			$user->updateUsermeta('games', sanitizeString($details['games']));
			$user->updateUsermeta('pmMail', $details['pmMail'] === 'true' ? 1 : 0);
			$user->updateUsermeta('newGameMail', $details['newGameMail'] === 'true' ? 1 : 0);
			$user->updateUsermeta('gmMail', $details['gmMail'] === 'true' ? 1 : 0);
			if ($details['theme'] == 'null') {
				$details['theme'] = '';
			}
			$user->updateUsermeta('theme', sanitizeString($details['theme']),true);
			if ($details['warnUnsaved'] == 'null') {
				$details['warnUnsaved'] = '';
			}
			$user->updateUsermeta('warnUnsaved', sanitizeString($details['warnUnsaved']),true);

			if ($details['lookingForAGame'] == 'null' || $details['lookingForAGame'] == '0') {
				$details['lookingForAGame'] = '';
			}
			$user->updateUsermeta('lookingForAGame', $details['lookingForAGame'],true);

			$errors = [];
			$oldPass = $newPass['oldPassword'];
			$password1 = $newPass['password1'];
			$password2 = $newPass['password2'];
			if (strlen($password1) && strlen($password2) ) {
				if (!$user->validate($oldPass) && !$user->checkACP('users', false)) {
					$errors[] = 'wrongPass';
				}
				if (strlen($password1) < 6) {
					$errors[] = 'passShort';
				}
				if (strlen($password1) > 32) {
					$errors[] = 'passLong';
				}
				if ($password1 != $password2) {
					$errors[] = 'passMismatch';
				}

				if (!sizeof($errors)) {
					$user->updatePassword($password1);
				}
			}

			if (in_array($details['postSide'], ['l', 'r', 'c'])) {
				$postSide = $details['postSide'];
			} else {
				$postSide = 'l';
			}
			$user->updateUsermeta('postSide', $postSide);

			$user->updateUsermeta('pog_participate', $plane_of_giving['participate'] ? true : false);
			$user->updateUsermeta('pog_interests', $plane_of_giving['interests']);
			$user->updateUsermeta('pog_gift_sent', $plane_of_giving['gift_sent'] ? true : false);
			$user->updateUsermeta('pog_gift_received', $plane_of_giving['gift_received'] ? true : false);

			$return = [];
			if (sizeof($errors)) {
				$return['passErrors'] = $errors;
			} else {
				$return['success'] = true;
			}
			if ($avatarUploaded) {
				$return['avatarUploaded'] = true;
			}
			displayJSON($return);
		}

		public function suspend() {
			$mysql = DB::conn('mysql');

			$userID = (int) $_POST['userID'];
			$until = (int) $_POST['until'];
			if ($until > time()) {
				$mysql->query("UPDATE users SET suspendedUntil = '" . date('Y-m-d H:i:s', $until) . "' WHERE userID = {$userID} LIMIT 1");
				displayJSON(['suspended' => $until]);
			} else {
				$mysql->query("UPDATE users SET suspendedUntil = null WHERE userID = {$userID} LIMIT 1");
				displayJSON(['suspended' => null]);
			}
		}

		public function ban() {

		}

		public function stats() {
			$mysql = DB::conn('mysql');
			require_once(FILEROOT . '/includes/Systems.class.php');
			$systems = Systems::getInstance();

			if (isset($_POST['userID']) && intval($_POST['userID']) > 0) {
				$userID = (int) $_POST['userID'];
			} else {
				global $currentUser;
				$userID = $currentUser->userID;
			}

			$postCount = $mysql->query("SELECT COUNT(postID) FROM posts WHERE authorID = {$userID}")->fetchColumn();
			$communityPostCount = $mysql->query("SELECT COUNT(posts.postID) FROM posts INNER JOIN threads ON posts.threadID = threads.threadID INNER JOIN forums ON threads.forumID = forums.forumID WHERE authorID = {$userID} AND forums.gameID IS NULL")->fetchColumn();
			$gamePostCount = $mysql->query("SELECT COUNT(posts.postID) FROM posts INNER JOIN threads ON posts.threadID = threads.threadID INNER JOIN forums ON threads.forumID = forums.forumID WHERE authorID = {$userID} AND forums.gameID IS NOT NULL")->fetchColumn();
			$getActiveGames = $mysql->query(
				"SELECT
					games.gameID, games.title, games.system, games.customSystem, games.forumID, players.isGM, forums_permissions_general.`read`
				FROM games
				INNER JOIN players
					ON games.gameID = players.gameID
				INNER JOIN forums
					ON games.gameID = forums.gameID
				INNER JOIN threads
					ON forums.forumID = threads.forumID
				INNER JOIN posts
					ON threads.threadID = posts.threadID
				INNER JOIN forums_permissions_general permissions
					ON forums.forumID = permissions.forumID
				WHERE
					players.userID = {$userID} AND players.approved = TRUE AND posts.datePosted > NOW() - INTERVAL 1 WEEK AND threads.publicPosting = 0
				GROUP BY games.gameID
				ORDER BY games.title"
			);

			$activeGames = [];
			foreach ($getActiveGames as $activeGame) {
				$title = printReady($activeGame['title']);
				$system = $activeGame['customSystem'] ? $activeGame['customSystem'] : $systems->getFullName($activeGame['system']);
				$forumID = (bool) $activeGame['read'] ? $activeGame['forumID'] : null;
				$isGM = (bool) $activeGame['isGM'];
				$activeGames[] = [
					'gameID' => $activeGame['gameID'],
					'title' => $title,
					'system' => $system,
					'isGM' => $isGM,
					'forumID' => $forumID
				];
			}

			$getCharacters = $mysql->query("SELECT system FROM characters WHERE userID = {$userID} AND retired = 0");
			$characters = [];
			$numChars = 0;
			foreach ($getCharacters->fetchAll() as $character) {
				$system = $character['system'];
				if (!isset($characters[$system])) {
					$characters[$system] = [
						'system' => [
							'slug' => $system,
							'name' => $systems->getFullName($system)
						],
						'numChars' => 1
					];
				} else {
					$characters[$system]['numChars']++;
				}
				$numChars++;
			}
			$characters = array_values($characters);

			$getGamesRunning = $mysql->query("SELECT games.system, count(*) numGames FROM games INNER JOIN players ON games.gameID = players.gameID WHERE players.userID = {$userID} AND players.isGM = TRUE and games.retired = FALSE GROUP BY games.system ORDER BY games.system")->fetchALl();
			$games = [];
			$numGames = 0;
			foreach ($getGamesRunning as $game) {
				$games[] = [
					'system' => [
						'slug' => $game['system'],
						'name' => $systems->getFullName($game['system'])
					],
					'numGames' => $game['numGames']
				];
				$numGames += $game['numGames'];
			}

			displayJSON([
				'posts' => ['count' => $postCount, 'communityCount' => $communityPostCount, 'gameCount' => $gamePostCount],
				'characters' => ['numChars' => $numChars, 'list' => $characters],
				'games' => ['numGames' => $numGames, 'list' => $games],
				'activeGames' => $activeGameRet
			]);
		}

		public function getLFG() {
			$mysql = DB::conn('mysql');

			if (isset($_POST['userID']) && intval($_POST['userID']) > 0) {
				$userID = (int) $_POST['userID'];
			} else {
				global $currentUser;
				$userID = $currentUser->userID;
			}
			$lfg = $mysql->query("SELECT metaValue FROM usermeta WHERE userID = {$userID} AND metaKey = 'acpPermissions'");
			if ($lfg->rowCount()) {
				$lfg = json_decode($lfg->fetchColumn(), true);
				displayJSON(['lfg' => $lfg]);
			} else {
				displayJson([]);
			}
		}

		public function saveLFG() {
			$mysql = DB::conn('mysql');

			if (isset($_POST['userID']) && intval($_POST['userID']) > 0) {
				$userID = (int) $_POST['userID'];
			} else {
				global $currentUser;
				$userID = $currentUser->userID;
			}
			$lfg = $mysql->query("SELECT metaValue FROM usermeta WHERE userID = {$userID} AND metaKey = 'acpPermissions'");
			if ($lfg->rowCount()) {
				$lfg = json_decode($lfg->fetchColumn(), true);
			} else {
				$lfg = [];
			}
			$remove = [];
			$newLFG = [];
			require_once('../includes/Systems.class.php');
			$systems = Systems::getInstance();
			foreach ($_POST['lfg'] as $system) {
				$newLFG[$systems->getSlug($system)] = 1;
			}
			foreach ($lfg as $key => $system) {
				if (array_key_exists($system, $newLFG)) {
					unset($newLFG[$system]);
				} else {
					unset($lfg[$key]);
					$remove[$system] = -1;
				}
			}
			$lfg = array_merge($lfg, array_keys($newLFG));
			$updateSystemLFGCount = $mysql->prepare("UPDATE systems SET lfg = lfg + :incAmount WHERE id = :system");
			foreach (array_merge($remove, $newLFG) as $system => $count) {
				$updateSystemLFGCount->execute(['incAmount' => $count, 'system' => $system]);
			}
			$updateUserLFG = $mysql->prepare("UPDATE users SET lfg = :lfg WHERE userID = :userID");
			$updateUserLFG->execute(['lfg' => $lfg, 'userID' => $userID]);
		}

		public function removeThreadNotification($postId){
			global $currentUser;
			$mysql = DB::conn('mysql');
			$mysql->query("DELETE FROM forumSubs WHERE userID = {$currentUser->userID} AND postID = {$postId}");
		}

		public function removeAllThreadNotifications(){
			global $currentUser;
			$mysql = DB::conn('mysql');
			$mysql->query("DELETE FROM forumSubs WHERE userID = {$currentUser->userID} AND subscribed_to = 't'");

		}

		public function setUserTheme($darkTheme) {
			global $currentUser;

			$currentUser->updateUsermeta('theme', (int) $darkTheme ? 'dark' : '', true);

			return true;
		}
	}
?>
