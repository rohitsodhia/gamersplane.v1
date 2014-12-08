<?
	function startSession() {
		session_start();
		
//		putenv('TZ=GMT');
		date_default_timezone_set('GMT');
	}
	
	function checkLogin($redirect = 1) {
		global $currentUser;
		if (!isset($currentUser)) $currentUser = new User();
		
		if (isset($_COOKIE['loginHash'])) {
			global $mysql;

			list($username, $loginHash) = explode('|', sanitizeString($_COOKIE['loginHash']));
			$userCheck = $mysql->prepare('SELECT userID FROM users WHERE username = :username');
			$userCheck->execute(array(':username' => $username));

			if ($userCheck->rowCount()) {
				$userID = $userCheck->fetchColumn();
				$currentUser = new User($userID);
				if ($currentUser->getLoginHash() == $loginHash) {
					$currentUser->generateLoginCookie();

//					wp_set_current_user($userInfo['userID']);
//					wp_set_auth_cookie($userInfo['userID']);
//					do_action('wp_login', $userInfo['userID']);

					$mysql->query('UPDATE users SET lastActivity = NOW() WHERE userID = '.$currentUser->userID);

					return true;
				}
			}
		}
		
		logout();
		if ($redirect) { header('Location: /login/?redirect=1'); exit; }
		
		return false;
	}

	function logout() {
		session_unset();
//		unset($_COOKIE[session_name()]);
		
		session_regenerate_id(TRUE);
		session_destroy();
		setcookie(session_name(), '', time() - 30, '/');
		$_SESSION = array();

		setcookie('loginHash', '', time() - 30, '/');
//		session_destroy();
	}
?>