<?
	class User {
		protected $userID;
		protected $email;
		protected $password;
		protected $salt;
		protected $joinDate;
		protected $activatedOn;
		protected $timezone;
		protected $usermeta = array();

		protected $hiddenVars = array('password', 'salt');

		public function __construct($userDetail = null) {
			global $mysql;

			if ($userDetail == null) return false;

			$userInfo = $mysql->prepare("SELECT userID, email, password, salt, joinDate, activatedOn, timezone FROM users WHERE ".(strpos($userDetail, '@')?'email':'userID')." = :userDetail LIMIT 1");
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

		public function validate($password) {
			if ($this->userID !== null) {
				if (hash('sha256', PVAR.$password.$this->salt) == $this->password) return true;
				else {
					foreach ($this as $key => $value) if ($key != 'hiddenVars') $this->$key = null;
					return false;
				}
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
			setcookie('loginHash', $this->email.'|'.$this->getLoginHash(), time() + (60 * 60 * 24 * 7), '/');
		}
	}
?>