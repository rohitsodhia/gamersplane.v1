<?
	checkLogin(0);
	
	if (isset($_POST['submit'])) {
		$username = sanatizeString($_POST['username']);
		$key = sanatizeString($_POST['key']);
		$pass1 = sanatizeString($_POST['pass1']);
		$pass2 = sanatizeString($_POST['pass2']);
		
		if (strlen($pass1) < 6 || strlen($pass1) > 16 || $pass1 != $pass2) { header('Location: '.SITEROOT.'/login/resetPass?passError=1'); exit; }
		
		$userCheck = $mysql->query('SELECT userID, email FROM users WHERE username = "'.$username.'"');
		if ($mysql->rowCount()) {
			$userInfo = $userCheck->fetch();
			if (md5($userInfo['email'].'r3Qu'.$key) == $_POST['validationStr']) {
				$mysql->query('UPDATE users SET password = "'.hash('sha256', SVAR.$pass1).'" WHERE userID = '.$userInfo['userID']);
				header('Location: '.SITEROOT.'/login/?resetSuccess=1');
			} else header('Location: '.SITEROOT.'/login/resetPass?invalid=1');
		} else header('Location: '.SITEROOT.'/login/resetPass?invalid=1');
	} else header('Location: '.SITEROOT.'/login/resetPass');
?>