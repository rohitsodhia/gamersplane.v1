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
			}
			$usermeta = $mysql->query("SELECT metaKey, metaValue FROM usermeta WHERE userID = {$this->userID} AND autoload = 1");
			foreach ($usermeta as $eMeta) $this->usermeta[$eMeta['metaKey']] = $eMeta['metaValue'];
		}

		public function __get($var) {
			if (!in_array($var, $this->hiddenVars) && isset($this->$var)) return $this->$var;
			elseif (array_key_exists($var, $this->usermeta)) return $this->usermeta[$var];
			else return null;
		}

		public function newUser($username, $password, $email) {
			global $mysql;

			$this->salt = randomAlphaNum(20);
			$addUser = $mysql->prepare('INSERT INTO users SET username = :username, password = :password, salt = :salt, email = :email, joinDate = :joinDate');
			$addUser->bindValue(':username', $username);
			$addUser->bindValue(':password', hash('sha256', PVAR.$password1.$this->salt));
			$addUser->bindValue(':salt', $this->salt);
			$addUser->bindValue(':email', $email);
			$addUser->bindValue(':joinDate', date('Y-m-d H:i:s'));
			$addUser->execute();

			$this->userID = $mysql->lastInsertId();
			if ($this->userID) return $this->userID;
			else return false;
		}

		public function validate($password) {
			if ($this->userID !== null) {
				if (hash('sha256', PVAR.$password.$this->salt) == $this->password) return true;
				else return false;
			} else return false;
		}

		public function activated() {
			return $this->activatedOn == null?false:true;
		}

		public function getLoginHash() {
			return substr(hash('sha256', PVAR.$this->email.$this->joinDate), 0, 32);
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

			return $this->usermeta[$metaKey];
		}

		public function getAllUsermeta() {
			global $mysql;

			$metaValues = $mysql->query("SELECT metaKey, metaValue FROM usermeta WHERE userID = {$this->userID}");
			foreach ($metaValues as $metas) $this->usermeta[$metas['metaKey']] = $metas['metaValue'];

			return true;
		}

		public function updateUsermeta($metaKey, $metaValue, $autoload = 0) {
			global $mysql;

			if ($autoload != 1) $autoload = 0;
			$addUpdateMetaKey = $mysql->prepare("INSERT INTO usermeta SET userID = {$this->userID}, metaKey = :metaKey, metaValue = :metaValue, autoload = {$autoload} ON DUPLICATE KEY UPDATE metaValue = :metaValue, autoload = {$autoload}");
			$addUpdateMetaKey->bindValue(':metaKey', $metaKey);
			$addUpdateMetaKey->bindValue(':metaValue', $metaValue);
			$addUpdateMetaKey->execute();

			$this->usermeta[$metaKey] = $metaValue;

			return true;
		}
	}
?>