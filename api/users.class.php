<?
	class users {
		const USERS_PER_PAGE = 25;

		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'gamersList') 
				$this->gamersList();
			elseif ($pathOptions[0] == 'search') 
				$this->search();
			elseif ($pathOptions[0] == 'getCurrentUser') 
				$this->getCurrentUser();
			elseif ($pathOptions[0] == 'get') 
				$this->getUser();
			elseif ($pathOptions[0] == 'save') 
				$this->saveUser();
			elseif ($pathOptions[0] == 'stats') 
				$this->stats();
			elseif ($pathOptions[0] == 'getLFG') 
				$this->getLFG();
			elseif ($pathOptions[0] == 'saveLFG') 
				$this->saveLFG();
			else 
				displayJSON(array('failed' => true));
		}

		public function gamersList() {
			global $mysql;

			$page = isset($_POST['page']) && intval($_POST['page']) > 0?intval($_POST['page']):1;
			$total = $mysql->query("SELECT COUNT(userID) FROM users WHERE activatedOn IS NOT NULL".(!isset($_POST['showInactive']) || !$_POST['showInactive']?' AND lastActivity >= UTC_TIMESTAMP() - INTERVAL 2 WEEK':''))->fetchColumn();
			$rUsers = $mysql->query('SELECT userID, username, lastActivity, IF(lastActivity >= UTC_TIMESTAMP() - INTERVAL 15 MINUTE, 1, 0) online, joinDate FROM users WHERE activatedOn IS NOT NULL'.(!isset($_POST['showInactive']) || !$_POST['showInactive']?' AND lastActivity >= UTC_TIMESTAMP() - INTERVAL 2 WEEK':'').' ORDER BY online DESC, username LIMIT '.(($page - 1) * self::USERS_PER_PAGE).', '.self::USERS_PER_PAGE);
			$users = array();
			if (sizeof($rUsers)) {
				foreach ($rUsers as $user) {
					$user['userID'] = (int) $user['userID'];
					$user['online'] = (bool) $user['online'];
					$user['avatar'] = User::getAvatar($user['userID']);
					$user['inactive'] = User::inactive($user['lastActivity']);
					unset($user['lastActivity']);
					$users[] = $user;
				}
				displayJSON(array('users' => $users, 'totalUsers' => (int) $total));
			} else 
				displayJSON(array('noUsers' => true));
		}

		public function search() {
			global $mysql, $currentUser;

			$search = sanitizeString(preg_replace('/[^\w.]/', '', $_GET['search']), 'lower');
			if (isset($_GET['exact']) && (bool) $_GET['exact'] == true) {
				$searchBy = isset($_GET['searchBy']) && in_array($_GET['searchBy'], array('username', 'userID'))?$_GET['searchBy']:'username';
				if ($searchBy == 'userID') {
					$search = intval($search);
					$user = $mysql->query("SELECT userID, username FROM users WHERE userID = {$search}")->fetch();
				} else 
					$user = $mysql->query("SELECT userID, username FROM users WHERE username = '{$search}'")->fetch();

				if ($user) 
					displayJSON(array('users' => array($user)));
				else 
					displayJSON(array('noUsers' => true));
			} else {
				$valid = $mysql->query("SELECT userID, username FROM users WHERE username LIKE '%{$search}%'");
				if ($valid->rowCount()) {
					$users = array();
					foreach ($valid as $user) 
						$users[] = array(
							'userID' => (int) $user['userID'],
							'username' => $user['username']
						);
					displayJSON(array('users' => $users));
				} else 
					displayJSON(array('noUsers' => true));

			}
		}

		public function getCurrentUser() {
			global $loggedIn, $currentUser;

			if (!$loggedIn) 
				displayJSON(array('failed' => true, 'loggedOut' => true));
			else {
				$cleanUser = array(
					'userID' => $currentUser->userID,
					'username' => $currentUser->username,
					'email' => $currentUser->email,
					'joinDate' => $currentUser->joinDate,
					'activatedOn' => $currentUser->activatedOn,
					'timezone' => $currentUser->timezone,
					'usermeta' => $currentUser->usermeta,
					'acpPermissions' => $currentUser->acpPermissions
				);
				displayJSON($cleanUser);
			}
		}

		public function getUser() {
			global $loggedIn, $currentUser;

			if (isset($_POST['userID'])) 
				$user = new User(intval($_POST['userID']));
			elseif (!isset($_POST['userID'])) 
				$user = $currentUser;
			if (!$user) 
				displayJSON(array('failed' => true, 'noUser' => true));
			if ($loggedIn) 
				$getAll = $currentUser->checkACP('users', false) || $user->userID == $currentUser->userID;
			$user->getAllUsermeta();

			$details = array(
				'userID' => $user->userID,
				'username' => $user->username,
				'joinDate' => $user->joinDate,
				'lastActivity' => $user->lastActivity,
				'avatar' => array(
					'url' => User::getAvatar($user->userID, $user->avatarExt),
					'avatarExt' => $user->avatarExt
				),
				'gender' => $user->gender?$user->gender:'n',
				'birthday' => array(
					'showAge' => $user->showAge?true:false
				),
				'location' => $user->location,
				'twitter' => $user->twitter,
				'stream' => $user->stream,
				'games' => $user->games
			);
			if ($getAll) 
				$details = array_merge($details, array(
					'email' => $user->email,
					'birthday' => array(
						'date' => $user->birthday,
						'showAge' => $user->showAge?true:false
					),
					'pmMail' => $user->pmMail?true:false,
					'newGameMail' => $user->newGameMail?true:false,
					'gmMail' => $user->gmMail?true:false,
					'postSide' => $user->postSide
				));
			if ($details['birthday']['showAge']) {
				$now = new DateTime();
				$birthday = new DateTime($user->birthday);
				$details['birthday']['age'] = (int) $now->diff($birthday)->y;
			}
			displayJSON(array('success' => true, 'details' => $details));
		}

		public function saveUser() {
			global $loggedIn, $currentUser;

			if (!$loggedIn && $currentUser->userID != $_POST['userID'] && !$currentUser->checkACP('users', false)) 
				displayJSON(array('failed' => true, 'noAuth' => true));

			$data = json_decode($_POST['data']);
			$details = $data->details;
			$newPass = $data->newPass;
			$userID = (int) $details->userID;
			if ($currentUser->userID != $userID) 
				$user = new User($userID);
			else 
				$user = $currentUser;

			$avatarUploaded = false;
			if ($data->avatar->delete) 
				unlink(FILEROOT."/ucp/avatars/{$user->userID}.jpg");
			if ($_FILES['file']['error'] == 0 && $_FILES['file']['size'] > 15 && $_FILES['file']['size'] < 1048576) {
				$avatarExt = trim(end(explode('.', strtolower($_FILES['file']['name']))));
				if ($avatarExt == 'jpeg') 
					$avatarExt = 'jpg';
				if (in_array($avatarExt, array('jpg', 'gif', 'png'))) {
					$maxWidth = 150;
					$maxHeight = 150;
					
					list($imgWidth, $imgHeight, $imgType) = getimagesize($_FILES['file']['tmp_name']);
					if ($imgWidth >= $maxWidth && $imgHeight >= $maxHeight) {
						if (image_type_to_mime_type($imgType) == 'image/jpeg' || image_type_to_mime_type($imgType) == 'image/pjpeg') $tempImg = imagecreatefromjpeg($_FILES['file']['tmp_name']);
						elseif (image_type_to_mime_type($imgType) == 'image/gif') $tempImg = imagecreatefromgif($_FILES['file']['tmp_name']);
						elseif (image_type_to_mime_type($imgType) == 'image/png') $tempImg = imagecreatefrompng($_FILES['file']['tmp_name']);
						
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
						
						$destination = FILEROOT.'/ucp/avatars/'.$user->userID.'.'.$avatarExt;
						foreach (glob(FILEROOT.'/ucp/avatars/'.$user->userID.'.*') as $oldFile) 
							unlink($oldFile);
						if ($avatarExt == 'jpg') 
							imagejpeg($tempColor, $destination, 100);
						elseif ($avatarExt == 'gif') 
							imagegif($tempColor, $destination);
						elseif ($avatarExt == 'png') 
							imagepng($tempColor, $destination, 0);
						imagedestroy($tempImg);
						imagedestroy($tempColor);
						$fileUploaded = true;
					}
				} elseif ($avatarExt == 'svg') {
					foreach (glob(FILEROOT.'/ucp/avatars/'.$user->userID.'.*') as $oldFile) 
						unlink($oldFile);
					move_uploaded_file($_FILES['file']['tmp_name'], FILEROOT."/ucp/avatars/{$user->userID}.svg");
					$fileUploaded = true;
				}

				if ($avatarExt == '') 
					$avatarExt = null;
				$user->updateUsermeta('avatarExt', $avatarExt, true);
				$avatarUploaded = true;
			}

//			$user->updateUsermeta('showAvatars', isset($data->showAvatars)?1:0);
			if ($details->gender == 'n') 
				$gender = '';
			else 
				$gender = $details->gender == 'm'?'m':'f';
			$user->updateUsermeta('gender', $gender);
			$birthday = intval($details->birthday->date->year).'-'.(intval($details->birthday->date->month) <= 9?'0':'').intval($details->birthday->date->month).'-'.(intval($details->birthday->date->day) <= 9?'0':'').intval($details->birthday->date->day);
			if (preg_match('/^[12]\d{3}-[01]\d-[0-3]\d$/', $birthday)) 
				$user->updateUsermeta('birthday', $birthday);
			$user->updateUsermeta('showAge', $details->birthday->showAge?1:0);
			$user->updateUsermeta('location', sanitizeString($details->location));
			$user->updateUsermeta('twitter', sanitizeString($details->twitter));
			$user->updateUsermeta('stream', sanitizeString($details->stream));
			$user->updateUsermeta('games', sanitizeString($details->games));
			$user->updateUsermeta('pmMail', intval($details->pmMail)?1:0);
			$user->updateUsermeta('newGameMail', intval($details->newGameMail)?1:0);
			$user->updateUsermeta('gmMail', intval($details->gmMail)?1:0);

			$errors = array();
			$oldPass = $newPass->oldPass;
			$password1 = $newPass->password1;
			$password2 = $newPass->password2;
			if (strlen($password1) && strlen($password2) ) {
				if (!$user->validate($oldPass) && !$user->checkACP('users', false)) 
					$errors[] = 'wrongPass';
				if (strlen($password1) < 6) 
					$errors[] = 'passShort';
				if (strlen($password1) > 32) 
					$errors[] = 'passLong';
				if ($password1 != $password2) 
					$errors[] = 'passMismatch';

				if (!sizeof($errors))
					$user->updatePassword($password1);
			}

			if (in_array($details->postSide, array('l', 'r', 'c'))) 
				$postSide = $details->postSide;
			else 
				$postSide = 'l';
			$user->updateUsermeta('postSide', $postSide);

			$return = array();
			if (sizeof($errors)) 
				$return['passErrors'] = $errors;
			else 
				$return['success'] = true;
			if ($avatarUploaded) 
				$return['avatarUploaded'] = true;
			displayJSON($return);
		}

		public function stats() {
			global $mysql, $mongo;
			require_once(FILEROOT.'/includes/Systems.class.php');
			$systems = Systems::getInstance();

			if (isset($_POST['userID']) && intval($_POST['userID']) > 0) 
				$userID = (int) $_POST['userID'];
			else {
				global $currentUser;
				$userID = $currentUser->userID;
			}
			$rCharacters = $mongo->characters->find(array('user.userID' => $userID, 'retired' => null), array('system' => true));
//			$rCharacters = $mysql->query("SELECT c.characterID, c.system shortName, s.fullName, COUNT(c.characterID) numChars FROM characters c INNER JOIN systems s ON c.system = s.shortName WHERE c.userID = {$userID} AND retired IS NULL GROUP BY c.system ORDER BY numChars DESC, s.fullName");
			$characters = array();
			$numChars = 0;
			foreach ($rCharacters as $character) {
				if (!isset($characters[$character['system']])) {
					$characters[$character['system']] = array(
						'system' => array(
							'slug' => $character['system'],
							'name' => $systems->getFullName($character['system'])
						),
						'numChars' => 1
					);
				} else 
					$characters[$character['system']]['numChars']++;
				$numChars++;
			}
			$characters = array_values($characters);

			$rGames = $mongo->games->group(
				array('system' => true),
				array('count' => 0),
				'function (item, result) { result.count++; }',
				array('condition' => array(
					'players' => array(
						'$elemMatch' => array(
							'user.userID' => $userID,
							'isGM' => true
						)
					)
				))
			);
			$games = array();
			$numGames = 0;
			foreach ($rGames['retval'] as $game) {
				$games[] = array(
					'system' => array(
						'slug' => $game['system'],
						'name' => $systems->getFullName($game['system'])
					),
					'numGames' => (int) $game['count']
				);
				$numGames += (int) $game['count'];
			}

			displayJSON(array('characters' => array('numChars' => $numChars, 'list' => $characters), 'games' => array('numGames' => $numGames, 'list' => $games)));
		}

		public function getLFG() {
			global $mongo;

			if (isset($_POST['userID']) && intval($_POST['userID']) > 0) 
				$userID = (int) $_POST['userID'];
			else {
				global $currentUser;
				$userID = $currentUser->userID;
			}
			$lfg = $mongo->users->findOne(array('userID' => $userID), array('lfg' => 1));
			displayJSON(array('lfg' => $lfg['lfg']));
		}

		public function saveLFG() {
			global $mongo;

			if (isset($_POST['userID']) && intval($_POST['userID']) > 0) 
				$userID = (int) $_POST['userID'];
			else {
				global $currentUser;
				$userID = $currentUser->userID;
			}
			$lfg = $mongo->users->findOne(array('userID' => $userID), array('lfg' => 1));
			$remove = array();
			$lfg = $lfg['lfg'];
			$newLFG = array();
			require_once('../includes/Systems.class.php');
			$systems = Systems::getInstance();
			foreach ($_POST['lfg'] as $system) 
				$newLFG[$systems->getSlug($system)] = 1;
			foreach ($lfg as $key => $system) {
				if (array_key_exists($system, $newLFG)) 
					unset($newLFG[$system]);
				else {
					unset($lfg[$key]);
					$remove[$system] = -1;
				}
			}
			$lfg = array_merge($lfg, array_keys($newLFG));
			foreach (array_merge($remove, $newLFG) as $system => $count) 
				$mongo->systems->update(array('_id' => $system), array('$inc' => array('lfg' => $count)));
			$mongo->users->update(array('userID' => $userID), array('$set' => array('lfg' => $lfg)));
		}
	}
?>