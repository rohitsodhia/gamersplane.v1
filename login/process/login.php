<?
	checkLogin(0);
	if (isset($_POST['login'])) {
		$username = sanatizeString(strtolower($_POST['username']));
		$password = hash('sha256', SVAR.$_POST['password']);
		
/*		$mysql->setTable('loginRecord');
		$mysql->setInserts(array('username' => $username, 'ipAddress' => $_SERVER['REMOTE_ADDR'], 'timestamp' => date('Y-m-d H:i:s')));
		$mysql->stdQuery('insert');
*/		
		$userCheck = $mysql->query('SELECT userID, username, password, joinDate, active, timezone FROM users WHERE LOWER(username) = "'.$username.'"');
		
		if ($userCheck->rowCount()) {
			$userInfo = $userCheck->fetch();
			
			if ($userInfo['active'] == 0 || $userInfo['password'] != $password) {
				$mysql->query('INSERT INTO loginRecords (userID, attemptStamp, ipAddress, successful) VALUES ('.$userInfo['userID'].', NOW(), "'.$_SERVER['REMOTE_ADDR'].'", 0)');
				if (isset($_POST['modal'])) echo 0;
				else header('Location: '.SITEROOT.'/login?failed=1');
			} else {
				$mysql->query('INSERT INTO loginRecords (userID, attemptStamp, ipAddress, successful) VALUES ('.$userInfo['userID'].', NOW(), "'.$_SERVER['REMOTE_ADDR'].'", 1)');
//				$mysql->query('SELECT userID FROM loginRecords WHERE userID = '.$userInfo['userID'].' AND attemptStamp > SUBTIME(NOW(), "12:00:00")');
//				if ($mysql->numRows > 5) { header('Location: '.SITEROOT.'/login?spammed=1'); exit; }
			
				$_SESSION['userID'] = $userInfo['userID'];
				$_SESSION['username'] = $userInfo['username'];
				$_SESSION['timezone'] = $userInfo['timezone'];
				
				setcookie('loginHash', md5(SVAR.$userInfo['username'].$userInfo['joinDate']), time() + (60 * 60 * 24 * 7), COOKIE_ROOT);
				
//				wp_set_current_user($userInfo['userID']);
//				wp_set_auth_cookie($userInfo['userID']);
//				do_action('wp_login', $userInfo['userID']);
				
				if (isset($_POST['modal'])) echo 1;
				else {
					if (isset($_SESSION['currentURL']) && $_SESSION['currentURL'] != SITEROOT) header('Location: '.$_SESSION['currentURL']);
					else header('Location: '.SITEROOT.'/');
				}
			}
		} else {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: '.SITEROOT.'/login?failed=1');
		}
	} else { header('Location: '.SITEROOT.'/login'); }
?>