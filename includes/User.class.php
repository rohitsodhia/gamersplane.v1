<?php
	class User {
		protected $userID;
		protected $username;
		protected $password;
		protected $salt;
		protected $email;
		protected $joinDate;
		protected $activatedOn;
		protected $lastActivity;
		protected $timezone;
		protected $usermeta = [];
		protected $acpPermissions = null;

		protected $hiddenVars = ['password', 'salt'];

		public function __construct($userDetail = null) {
			$mysql = DB::conn('mysql');

			if ($userDetail == null) {
				return false;
			}

			$userInfo = $mysql->prepare("SELECT userID, username, password, salt, email, joinDate, activatedOn, lastActivity FROM users WHERE " . (strpos($userDetail, '@') ? 'email' : 'userID') . " = :userDetail LIMIT 1");
			$userInfo->bindParam(':userDetail', $userDetail);
			$userInfo->execute();
			if ($userInfo->rowCount()) {
				$userInfo = $userInfo->fetch();
				foreach ($userInfo as $key => $value) {
					$this->$key = $value;
				}
				$this->userID = (int) $this->userID;

				$usermeta = $mysql->query("SELECT metaKey, metaValue FROM usermeta WHERE userID = {$this->userID} AND autoload = 1");
				foreach ($usermeta as $eMeta) {
					if ($eMeta['metaKey'] != 'acpPermissions') {
						$this->usermeta[$eMeta['metaKey']] = $eMeta['metaValue'];
					} else {
						$this->acpPermissions = unserialize($eMeta['metaValue']);
					}
				}
			} else {
				return false;
			}
		}

		public static function checkLogin($redirect = true) {
			global $currentUser;
			if (!isset($currentUser)) {
				$currentUser = new User();
			}

			$loginHash = $_COOKIE['loginHash'];
			if (is_string($loginHash) && strlen($loginHash)) {
				$mysql = DB::conn('mysql');

				list($username, $loginHash) = explode('|', sanitizeString($loginHash));
				$userCheck = $mysql->prepare('SELECT userID FROM users WHERE username = :username AND suspendedUntil IS NULL AND banned = 0');
				$userCheck->execute(array(':username' => $username));

				if ($userCheck->rowCount()) {
					$userID = $userCheck->fetchColumn();
					$currentUser = new User($userID);
					if ($currentUser->getLoginHash() == $loginHash) {
						$currentUser->generateLoginCookie();
						$mysql->query('UPDATE users SET lastActivity = NOW() WHERE userID = ' . $currentUser->userID);

						return true;
					}
				}
			}

			User::logout();
			if ($redirect) { header('Location: /login/?redirect=1'); exit; }

			return false;
		}

		public static function logout($resetSession = false) {
			if ($resetSession) {
				session_unset();
//				unset($_COOKIE[session_name()]);

				session_regenerate_id(TRUE);
				session_destroy();
				setcookie(session_name(), '', time() - 30, '/', COOKIE_DOMAIN, true, true);
				$_SESSION = [];
			}

			setcookie('loginHash', '', time() - 30, '/', COOKIE_DOMAIN, true, true);
//			session_destroy();
		}

		public function __get($var) {
			if (!in_array($var, $this->hiddenVars) && isset($this->$var)) {
				return $this->$var;
			} elseif (array_key_exists($var, $this->usermeta)) {
				return $this->usermeta[$var];
			} elseif ($var == 'userID') {
				return 0;
			} else {
				return null;
			}
		}

		public function newUser($username, $password, $email) {
			$mysql = DB::conn('mysql');

			$this->salt = randomAlphaNum(20);
			$addUser = $mysql->prepare('INSERT INTO users SET username = :username, password = :password, salt = :salt, email = :email, joinDate = :joinDate');
			$addUser->bindValue(':username', $username);
			$addUser->bindValue(':password', hash('sha256', PVAR . $password . $this->salt));
			$addUser->bindValue(':salt', $this->salt);
			$addUser->bindValue(':email', $email);
			$addUser->bindValue(':joinDate', date('Y-m-d H:i:s'));
			$addUser->execute();
			$this->userID = $mysql->lastInsertId();

			if ($this->userID) {
				return $this->userID;
			} else {
				return false;
			}
		}

		public function activated() {
			return $this->activatedOn == null ? false : true;
		}

		public function validate($password) {
			if ($this->userID !== null) {
				if (hash('sha256', PVAR . $password . $this->salt) == $this->password) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function updatePassword($password) {
			$mysql = DB::conn('mysql');

			$this->salt = randomAlphaNum(20);
			$addUser = $mysql->prepare("UPDATE users SET password = :password, salt = :salt WHERE userID = {$this->userID}");
			$addUser->bindValue(':password', hash('sha256', PVAR . $password . $this->salt));
			$addUser->bindValue(':salt', $this->salt);
			$addUser->execute();
		}

		public function getLoginHash() {
			return substr(hash('sha256', PVAR . $this->email . $this->joinDate), 7, 32);
		}

		public function generateLoginCookie() {
			$secure = getenv('ENVIRONMENT') != 'dev';
			setcookie('loginHash', '', time() - 30, '/', COOKIE_DOMAIN, $secure, true);
			setcookie('loginHash', $this->username . '|' . $this->getLoginHash(), time() + (60 * 60 * 24 * 7), '/', COOKIE_DOMAIN, $secure, true);
		}

		public function getUsermeta($metaKey) {
			$mysql = DB::conn('mysql');

			$metaValue = $mysql->prepare("SELECT metaValue FROM usermeta WHERE userID = {$this->userID} AND metaKey = :metaKey");
			$metaValue->bindValue(':metaKey', $metaKey);
			$metaValue->execute();

			$this->usermeta[$metaKey] = $metaValue->rowCount() ? $metaValue->fetchColumn() : null;
			if (is_string($this->usermeta[$metaKey]) && strlen($this->usermeta[$metaKey]) > 4 && substr($this->usermeta[$metaKey], 0, 2) == 'a:') {
				$this->usermeta[$metaKey] = unserialize($this->usermeta[$metaKey]);
			}

			return $this->usermeta[$metaKey];
		}

		public function getAllUsermeta() {
			$mysql = DB::conn('mysql');

			$metaValues = $mysql->prepare("SELECT metaKey, metaValue FROM usermeta WHERE userID = :userID AND autoload = :autoload");
			$metaValues->bindValue(':userID', $this->userID);
			$metaValues->bindValue(':autoload', 0);
			$metaValues->execute();
			foreach ($metaValues as $metas) {
				if (is_string($metas['metaKey']) && strlen($metas['metaKey']) > 4 && substr($metas['metaKey'], 0, 2) == 'a:') {
					$metas['metaKey'] = unserialize($metas['metaKey']);
				}
				$this->usermeta[$metas['metaKey']] = $metas['metaValue'];
			}

			return true;
		}

		public function updateUsermeta($metaKey, $metaValue, $autoload = false) {
			$mysql = DB::conn('mysql');

			if ($metaValue !== null && $metaValue !== '') {
				$autoload = $autoload === true ? 1 : 0;
				$updateUsermeta = $mysql->prepare("INSERT INTO usermeta SET userID = {$this->userID}, metaKey = :metaKey, metaValue = :metaValue, autoload = {$autoload} ON DUPLICATE KEY UPDATE metaValue = :metaValue");
				$updateUsermeta->bindValue(':metaKey', $metaKey);
				if (is_array($metaValue)) {
					$metaValue = serialize($metaValue);
				}
				$updateUsermeta->bindValue(':metaValue', $metaValue);
				$updateUsermeta->execute();

				$this->usermeta[$metaKey] = $metaValue;
			} else {
				$this->deleteUsermeta($metaKey);
			}

			return true;
		}

		public function deleteUsermeta($metaKey) {
			$mysql = DB::conn('mysql');

			$deleteUsermeta = $mysql->prepare("DELETE FROM usermeta WHERE userID = {$this->userID} AND metaKey = :metaKey");
			$deleteUsermeta->bindValue(':metaKey', $metaKey);
			$deleteUsermeta->execute();
		}

		static function getAvatar($userID, $ext = false, $exists = false) {
			$userID = (int) $userID;
			if ($userID <= 0) {
				return $exists ? false : '/ucp/avatars/avatar.png';
			}

			if (!$ext) {
				$mysql = DB::conn('mysql');

				$ext = $mysql->query("SELECT metaValue FROM usermeta WHERE userID = {$userID} AND metaKey = 'avatarExt'");
				if ($ext->rowCount()) {
					$ext = $ext->fetchColumn();
				} else {
					$ext = false;
				}
			}
			if ($ext !== false && file_exists(FILEROOT . "/ucp/avatars/{$userID}.{$ext}")) {
				return $exists ? true : "/ucp/avatars/{$userID}.{$ext}";
			} else {
				return $exists ? false : '/ucp/avatars/avatar.png';
			}
		}

		public function checkACP($role, $redirect = true) {
			if ($role == 'all' && $this->acpPermissions && sizeof($this->acpPermissions)) {
				return $this->acpPermissions;
			} elseif ($role == 'any' && $this->acpPermissions && sizeof($this->acpPermissions)) {
				return true;
			} else {
				if (!$redirect && ($this->acpPermissions == null || (!in_array($role, $this->acpPermissions) && !in_array('all', $this->acpPermissions)))) {
					return false;
				} elseif ($this->acpPermissions == null) {
					header('Location: /');
					exit;
				} elseif (!in_array($role, $this->acpPermissions) && !in_array('all', $this->acpPermissions)) {
					header('Location: /acp/');
					exit;
				} else {
					return true;
				}
			}
		}

		static public function inactive($lastActivity, $returnImg = true) {
			$diff = time() - strtotime($lastActivity);
			$diff = floor($diff / (60 * 60 * 24));
			if ($diff < 14) {
				return false;
			}
			$diffStr = 'Inactive for';
			if ($diff <= 30) {
				$diffStr .= ' '.($diff - 1).' days';
			} else {
				$diff = floor($diff / 30);
				if ($diff < 12) {
					$diffStr .= ' ' . $diff . ' months';
				} else {
					$diffStr .= 'ever!';
				}
			}
			return $returnImg ? "<img src=\"/images/sleeping.png\" title=\"{$diffStr}\" alt=\"{$diffStr}\">" : $diffStr;
		}

		public function addPostNavigateWarning()
		{
			return ($this->usermeta['warnUnsaved']!='no');
		}
	}
?>
