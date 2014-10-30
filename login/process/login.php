<?
	checkLogin(0);
	if (isset($_POST['login'])) {
		$username = sanitizeString($_POST['username'], 'lower');
		$password = $_POST['password'];
		
/*		$mysql->setTable('loginRecord');
		$mysql->setInserts(array('username' => $username, 'ipAddress' => $_SERVER['REMOTE_ADDR'], 'timestamp' => date('Y-m-d H:i:s')));
		$mysql->stdQuery('insert');
*/		
		$userCheck = $mysql->prepare('SELECT userID FROM users WHERE LOWER(username) = ?');
		$userCheck->execute(array($username));
		
		if ($userCheck->rowCount()) {
			$userID = $userCheck->fetchColumn();

			global $currentUser;
			$currentUser = new User($userID);

			if (!$currentUser->activated() || !$currentUser->validate($password)) {
				exit;
				$mysql->query('INSERT INTO loginRecords (userID, attemptStamp, ipAddress, successful) VALUES ('.$userID.', NOW(), "'.$_SERVER['REMOTE_ADDR'].'", 0)');
				if (isset($_POST['modal'])) echo '/login/?failed=1';
				else header('Location: /login/?failed=1');
			} else {
				$mysql->query('INSERT INTO loginRecords (userID, attemptStamp, ipAddress, successful) VALUES ('.$userID.', NOW(), "'.$_SERVER['REMOTE_ADDR'].'", 1)');
//				$mysql->query('SELECT userID FROM loginRecords WHERE userID = '.$userID.' AND attemptStamp > SUBTIME(NOW(), "12:00:00")');
//				if ($mysql->numRows > 5) { header('Location: /login?spammed=1'); exit; }

				$currentUser->generateLoginCookie();			
				
//				wp_set_current_user($userID);
//				wp_set_auth_cookie($userID);
//				do_action('wp_login', $userID);
				
				if (isset($_POST['modal'])) echo 1;
				else {
//					if (isset($_SESSION['currentURL'])) header('Location: '.$_SESSION['currentURL']);
//					else header('Location: /');
					header('Location: /');
				}
			}
		} else {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: /login/?failed=1');
		}
	} else { header('Location: /login/'); }
?>