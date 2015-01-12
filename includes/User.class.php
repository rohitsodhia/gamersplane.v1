<?
	class User {
		protected $userID;
		protected $username;
		protected $password;
		protected $salt;
		protected $email;
		protected $joinDate;
		protected $activatedOn;
		protected $timezone;
		protected $usermeta = array();
		protected $acpPermissions = null;

		protected $hiddenVars = array('password', 'salt');

		public function __construct($userDetail = null) {
			global $mysql;

			if ($userDetail == null) return false;

			$userInfo = $mysql->prepare("SELECT userID, username, password, salt, email, joinDate, activatedOn FROM users WHERE ".(strpos($userDetail, '@')?'email':'userID')." = :userDetail LIMIT 1");
			$userInfo->bindParam(':userDetail', $userDetail);
			$userInfo->execute();
			if ($userInfo->rowCount()) {
				$userInfo = $userInfo->fetch();
				foreach ($userInfo as $key => $value) $this->$key = $value;

				$usermeta = $mysql->query("SELECT metaKey, metaValue FROM usermeta WHERE userID = {$this->userID} AND autoload = 1");
				foreach ($usermeta as $eMeta) {
					if ($eMeta['metaKey'] == 'acpPermissions') $this->acpPermissions = unserialize($eMeta['metaValue']);
					else $this->usermeta[$eMeta['metaKey']] = $eMeta['metaValue'];
				}
			} else return false;
		}

		public function __get($var) {
			if (!in_array($var, $this->hiddenVars) && isset($this->$var)) return $this->$var;
			elseif (array_key_exists($var, $this->usermeta)) return $this->usermeta[$var];
			elseif ($var == 'userID') return 0;
			else return null;
		}

		public function newUser($username, $password, $email) {
			global $mysql;

			$this->salt = randomAlphaNum(20);
			$addUser = $mysql->prepare('INSERT INTO users SET username = :username, password = :password, salt = :salt, email = :email, joinDate = :joinDate');
			$addUser->bindValue(':username', $username);
			$addUser->bindValue(':password', hash('sha256', PVAR.$password.$this->salt));
			$addUser->bindValue(':salt', $this->salt);
			$addUser->bindValue(':email', $email);
			$addUser->bindValue(':joinDate', date('Y-m-d H:i:s'));
			$addUser->execute();

			$this->userID = $mysql->lastInsertId();
			if ($this->userID) return $this->userID;
			else return false;
		}

		public function activated() {
			return $this->activatedOn == null?false:true;
		}

		public function validate($password) {
			if ($this->userID !== null) {
				if (hash('sha256', PVAR.$password.$this->salt) == $this->password) return true;
				else return false;
			} else return false;
		}

		public function updatePassword($password) {
			global $mysql;
			
			$this->salt = randomAlphaNum(20);
			$addUser = $mysql->prepare("UPDATE users SET password = :password, salt = :salt WHERE userID = {$this->userID}");
			$addUser->bindValue(':password', hash('sha256', PVAR.$password.$this->salt));
			$addUser->bindValue(':salt', $this->salt);
			$addUser->execute();
		}

		public function getLoginHash() {
			return substr(hash('sha256', PVAR.$this->email.$this->joinDate), 7, 32);
		}

		public function generateLoginCookie() {
			setcookie('loginHash', '', time() - 30, '/');
			setcookie('loginHash', $this->username.'|'.$this->getLoginHash(), time() + LOGIN_COOKIE_LENGTH, '/');
		}

		public function getUsermeta($metaKey) {
			global $mysql;

			$metaValue = $mysql->prepare("SELECT metaValue FROM usermeta WHERE userID = {$this->userID} AND metaKey = :metaKey");
			$metaValue->bindValue(':metaKey', $metaKey);
			$metaValue->execute();

			$this->usermeta[$metaKey] = $metaValue->rowCount()?$metaValue->fetchColumn():null;
			if (is_string($this->usermeta[$metaKey]) && strlen($this->usermeta[$metaKey]) > 4 && substr($this->usermeta[$metaKey], 0, 2) == 'a:') $this->usermeta[$metaKey] = unserialize($this->usermeta[$metaKey]);

			return $this->usermeta[$metaKey];
		}

		public function getAllUsermeta() {
			global $mysql;

			$metaValues = $mysql->query("SELECT metaKey, metaValue FROM usermeta WHERE userID = {$this->userID} AND autoload = 0");
			foreach ($metaValues as $metas) {
				if (is_string($metas['metaKey']) && strlen($metas['metaKey']) > 4 && substr($metas['metaKey'], 0, 2) == 'a:') $metas['metaKey'] = unserialize($metas['metaKey']);
				$this->usermeta[$metas['metaKey']] = $metas['metaValue'];
			}

			return true;
		}

		public function updateUsermeta($metaKey, $metaValue) {
			global $mysql;

			if ($metaValue != null && $metaValue != '') {
				$updateUsermeta = $mysql->prepare("INSERT INTO usermeta SET userID = {$this->userID}, metaKey = :metaKey, metaValue = :metaValue ON DUPLICATE KEY UPDATE metaValue = :metaValue");
				$updateUsermeta->bindValue(':metaKey', $metaKey);
				if (is_array($metaValue)) $metaValue = serialize($metaValue);
				$updateUsermeta->bindValue(':metaValue', $metaValue);
				$updateUsermeta->execute();

				$this->usermeta[$metaKey] = $metaValue;
			} else $this->deleteUsermeta($metaKey);

			return true;
		}

		public function setMetaAutoload($metaKey, $autoload = 0) {
			global $mysql;
			
			if ($autoload != 1) $autoload = 0;
			$updateAutoload = $mysql->prepare("UPDATE usermeta SET autoload = {$autoload} WHERE userID = {$this->userID} AND metaKey = :metaKey");
			$updateAutoload->bindValue(':metaKey', $metaKey);
			$updateAutoload->execute();
		}

		public function deleteUsermeta($metaKey) {
			global $mysql;

			$deleteUsermeta = $mysql->prepare("DELETE FROM usermeta WHERE userID = {$this->userID} AND metaKey = :metaKey");
			$deleteUsermeta->bindValue(':metaKey', $metaKey);
			$deleteUsermeta->execute();
		}

		public function getAvatar($exists = false) {
			if (file_exists(FILEROOT."/ucp/avatars/{$this->userID}.{$this->avatarExt}")) return $exists?true:"/ucp/avatars/{$this->userID}.{$this->avatarExt}";
			else return $exists?false:'/ucp/avatars/avatar.png';
		}

		public function checkACP($role = null, $redirect = true) {
			if ($role == null && sizeof($this->acpPermissions)) return true;
			elseif (strlen($role)) {
				if (!$redirect && ($this->acpPermissions == null || (!in_array($role, $this->acpPermissions) && !in_array('all', $this->acpPermissions)))) return false;
				elseif ($this->acpPermissions == null) { header('Location: /'); exit; }
				elseif (!in_array($role, $this->acpPermissions) && !in_array('all', $this->acpPermissions)) { header('Location: /acp/'); exit; }
				else return true;
			}
		}
	}
?>