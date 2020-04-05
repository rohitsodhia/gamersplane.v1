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
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public function gamersList() {
			$mysql = DB::conn('mysql');

			$page = isset($_POST['page']) && intval($_POST['page']) > 0 ? intval($_POST['page']) : 1;
			$total = $mysql->query("SELECT COUNT(userID) FROM users WHERE activatedOn IS NOT NULL" . (!isset($_POST['showInactive']) || !$_POST['showInactive'] ? ' AND lastActivity >= UTC_TIMESTAMP() - INTERVAL 2 WEEK' : ''))->fetchColumn();
			$rUsers = $mysql->query('SELECT userID, username, lastActivity, IF(lastActivity >= UTC_TIMESTAMP() - INTERVAL 15 MINUTE, 1, 0) online, joinDate FROM users WHERE activatedOn IS NOT NULL' . (!isset($_POST['showInactive']) || !$_POST['showInactive'] ? ' AND lastActivity >= UTC_TIMESTAMP() - INTERVAL 2 WEEK' : '').' ORDER BY online DESC, username LIMIT ' . (($page - 1) * self::USERS_PER_PAGE) . ', ' . self::USERS_PER_PAGE);
			$users = [];
			if (sizeof($rUsers)) {
				foreach ($rUsers as $user) {
					$user['userID'] = (int) $user['userID'];
					$user['online'] = (bool) $user['online'];
					$user['avatar'] = User::getAvatar($user['userID']);
					$user['inactive'] = User::inactive($user['lastActivity']);
					unset($user['lastActivity']);
					$users[] = $user;
				}
				displayJSON(['users' => $users, 'totalUsers' => (int) $total]);
			} else
				displayJSON(['noUsers' => true]);
		}

		public function search() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$search = sanitizeString(preg_replace('/[^\w.]/', '', $_GET['search']), 'lower');
			$fields = isset($_GET['fields']) && strlen($_GET['fields']) ? $_GET['fields'] : 'userID, username';
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
						$user['activatedOn'] = strtotime($user['activatedOn']);
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
				$valid = $mysql->query("SELECT {$fields} FROM users" . (strlen($search) ? " WHERE username LIKE '%{$search}%'" : '') . $typeQuery . ' LIMIT ' . (($page - 1) * $limit) . ', ' . $limit);
				$numUsers = $mysql->query("SELECT COUNT(userID) numUsers FROM users" . (strlen($search) ? " WHERE username LIKE '%{$search}%'" : '') . $typeQuery)->fetchColumn();
				if ($valid->rowCount()) {
					$users = [];
					foreach ($valid as $user) {
						$user['userID'] = (int) $user['userID'];
						if (isset($user['activatedOn'])) {
							$user['activatedOn'] = strtotime($user['activatedOn']);
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
			$mongo = DB::conn('mongo');

			if (!$loggedIn) {
				displayJSON(['failed' => true]);
			}

			$rCharacters = $mongo->characters->find(
				[
					'user.userID' => $currentUser->userID,
					'retired' => null
				],
				[
					'projection' => [
						'characterID' => true,
						'label' => true,
						'system' => true
					],
					'sort' => ['label' => 1],
					'limit' => 6
				]
			);
			$characters = [];
			foreach ($rCharacters as $char) {
				$characters[] = $char;
			}

			$rGames = $mongo->games->find(
				[
					'players.user.userID' => $currentUser->userID,
					'retired' => null
				],
				[
					'projection' => [
						'gameID' => true,
						'title' => true,
						'players.$' => true
					],
					'sort' => ['title' => 1],
					'limit' => 6
				]
			);
			$games = [];
			foreach ($rGames as $game) {
				$game['isGM'] = $game['players'][0]['isGM'];
				unset($game['players']);
				$games[] = $game;
			}

			$pmCount = $mongo->pms->count([
				'recipients' => ['$elemMatch' => [
					'userID' => $currentUser->userID,
					'read' => false,
					'deleted' => false
				]]
			]);

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
				'gender' => $user->gender ? $user->gender : 'n',
				'birthday' => [
					'showAge' => $user->showAge?true:false
				],
				'location' => $user->location,
				'twitter' => $user->twitter,
				'stream' => $user->stream,
				'games' => $user->games
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
					'postSide' => $user->postSide
				]);
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
			if ($details['gender'] == 'n') {
				$gender = '';
			} else {
				$gender = $details['gender'] == 'm' ? 'm' : 'f';
			}
			$user->updateUsermeta('gender', $gender);
			$birthday = intval($details['birthday']['date']['year']) . '-' . (intval($details['birthday']['date']['month']) <= 9 ? '0' : '') . intval($details['birthday']['date']['month']) . '-' . (intval($details['birthday']['date']['day']) <= 9 ? '0' : '') . intval($details['birthday']['date']['day']);
			if (preg_match('/^[12]\d{3}-[01]\d-[0-3]\d$/', $birthday)) {
				$user->updateUsermeta('birthday', $birthday);
			}
			$user->updateUsermeta('showAge', $details['birthday']['showAge'] === 'true' ? 1 : 0);
			if ($details['location'] == 'null') {
				$details['location'] = '';
			}
			$user->updateUsermeta('location', sanitizeString($details['location']));
			if ($details['twitter'] == 'null') {
				$details['twitter'] = '';
			}
			$user->updateUsermeta('twitter', sanitizeString($details['twitter']));
			if ($details['stream'] == 'null') {
				$details['stream'] = '';
			}
			$user->updateUsermeta('stream', sanitizeString($details['stream']));
			if ($details['games'] == 'null') {
				$details['games'] = '';
			}
			$user->updateUsermeta('games', sanitizeString($details['games']));
			$user->updateUsermeta('pmMail', $details['pmMail'] === 'true' ? 1 : 0);
			$user->updateUsermeta('newGameMail', $details['newGameMail'] === 'true' ? 1 : 0);
			$user->updateUsermeta('gmMail', $details['gmMail'] === 'true' ? 1 : 0);

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
			global $mysql, $mongo;
			require_once(FILEROOT . '/includes/Systems.class.php');
			$systems = Systems::getInstance();

			if (isset($_POST['userID']) && intval($_POST['userID']) > 0) {
				$userID = (int) $_POST['userID'];
			} else {
				global $currentUser;
				$userID = $currentUser->userID;
			}
			$rCharacters = $mongo->characters->find(
				['user.userID' => $userID, 'retired' => null],
				['projection' => ['system' => true]]
			);
//			$rCharacters = $mysql->query("SELECT c.characterID, c.system shortName, s.fullName, COUNT(c.characterID) numChars FROM characters c INNER JOIN systems s ON c.system = s.shortName WHERE c.userID = {$userID} AND retired IS NULL GROUP BY c.system ORDER BY numChars DESC, s.fullName");
			$characters = [];
			$numChars = 0;
			foreach ($rCharacters as $character) {
				if (!isset($characters[$character['system']])) {
					$characters[$character['system']] = [
						'system' => [
							'slug' => $character['system'],
							'name' => $systems->getFullName($character['system'])
						],
						'numChars' => 1
					];
				} else {
					$characters[$character['system']]['numChars']++;
				}
				$numChars++;
			}
			$characters = array_values($characters);

			$rGames = $mongo->games->aggregate(
				[
					['$match' => [
						'players' => [
							'$elemMatch' => [
								'user.userID' => $userID,
								'isGM' => true
							]
						]
					]],
					['$group' => [
						'_id' => '$system',
						'running' => ['$sum' => 1]
					]]
				]
			);
			$games = [];
			$numGames = 0;
			foreach ($rGames as $game) {
				$games[] = [
					'system' => [
						'slug' => $game['_id'],
						'name' => $systems->getFullName($game['_id'])
					],
					'numGames' => (int) $game['running']
				];
				$numGames += (int) $game['running'];
			}

			displayJSON([
				'characters' => ['numChars' => $numChars, 'list' => $characters],
				'games' => ['numGames' => $numGames, 'list' => $games]
			]);
		}

		public function getLFG() {
			$mongo = DB::conn('mongo');

			if (isset($_POST['userID']) && intval($_POST['userID']) > 0) {
				$userID = (int) $_POST['userID'];
			} else {
				global $currentUser;
				$userID = $currentUser->userID;
			}
			$lfg = $mongo->users->findOne(
				['userID' => $userID],
				['projection' => ['lfg' => 1]]
			);
			displayJSON(['lfg' => $lfg['lfg']]);
		}

		public function saveLFG() {
			$mongo = DB::conn('mongo');

			if (isset($_POST['userID']) && intval($_POST['userID']) > 0) {
				$userID = (int) $_POST['userID'];
			} else {
				global $currentUser;
				$userID = $currentUser->userID;
			}
			$lfg = $mongo->users->findOne(
				['userID' => $userID],
				['projection' => ['lfg' => 1]]
			);
			$remove = [];
			$lfg = $lfg['lfg'];
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
			foreach (array_merge($remove, $newLFG) as $system => $count) {
				$mongo->systems->updateOne(['_id' => $system], ['$inc' => ['lfg' => $count]]);
			}
			$mongo->users->updateOne(['userID' => $userID], ['$set' => ['lfg' => $lfg]]);
		}
	}
?>
