<?
	function startSession() {
		session_start();
		
//		putenv('TZ=GMT');
		date_default_timezone_set('GMT');
		
		if (!isset($_COOKIE['loginHash']) && (isset($_SESSION['userID']) || isset($_SESSION['username']))) logout();
		if (!isset($_SESSION['timezone'])) $_SESSION['timezone'] = 'GMT';
	}
	
	function checkLogin($redirect = 1) {
		if (isset($_COOKIE['loginHash'])) {
			global $mysql;
			$loginHash = sanitizeString($_COOKIE['loginHash']);
			$userCheck = $mysql->prepare('SELECT userID, username, joinDate, timezone FROM users WHERE MD5(CONCAT("'.SVAR.'", `username`, `joinDate`)) = :loginHash');
			$userCheck->execute(array(':loginHash' => $loginHash));
			
			if ($userCheck->rowCount()) {
				$userInfo = $userCheck->fetch();
				if (!isset($_SESSION['userID']) && !isset($_SESSION['username'])) $mysql->query('INSERT INTO loginRecords (userID, attemptStamp, ipAddress, successful) VALUES ('.$userInfo['userID'].', NOW(), "'.$_SERVER['REMOTE_ADDR'].'", 2)');
				$_SESSION['userID'] = $userInfo['userID'];
				$_SESSION['username'] = $userInfo['username'];
				$_SESSION['timezone'] = $userInfo['timezone'];
				setcookie('loginHash', '', time() - 30, COOKIE_ROOT);
				setcookie('loginHash', md5(SVAR.$userInfo['username'].$userInfo['joinDate']), time() + (60 * 60 * 24 * 7), COOKIE_ROOT);
				
//				wp_set_current_user($userInfo['userID']);
//				wp_set_auth_cookie($userInfo['userID']);
//				do_action('wp_login', $userInfo['userID']);
				
				$mysql->query('UPDATE users SET lastActivity = "'.date('Y-m-d H:i:s').'" WHERE userID = '.$userInfo['userID']);
				
				return TRUE;
			} else {
				logout();
				wp_logout();
				if ($redirect) { header('Location: /login?redirect=1'); exit; }
				
				return FALSE;
			}
		} else {
//			logout();
			if ($redirect) { header('Location: /login?redirect=1'); exit; }
			
			return FALSE;
		}
	}
	
	function logout() {
		session_unset();
//		unset($_COOKIE[session_name()]);
		
		session_regenerate_id(TRUE);
		session_destroy();
		setcookie(session_name(), '', time() - 30, COOKIE_ROOT);
		$_SESSION = array();
		
		setcookie('loginHash', '', time() - 30, COOKIE_ROOT);
//		session_destroy();
	}
?>