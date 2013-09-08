<?
	$loggedIn = checkLogin(0);
	
	require_once(FILEROOT.'/blog/wp-blog-header.php');
	require_once(FILEROOT.'/blog/wp-includes/registration.php');
	
	if (isset($_POST['submit'])) {
		unset($_SESSION['errors']);
		unset($_SESSION['errorVals']);
		unset($_SESSION['errorTime']);
		
/*		$regKey = $_POST['regKey'];
		if (!preg_match('/^[A-Z0-9 -]{20}$/i', $_POST['regKey'])) header('Location: '.SITEROOT.'/register/?invalidKey=1');
		$regKey = sanatizeString(str_replace(array(' ', '-'), '', $_POST['regKey']));
		$mysql->query('SELECT used FROM regKeys WHERE regKey = "'.$regKey.'"');
		if ($mysql->rowCount()) {
			list($keyUsed) = $mysql->getList();
			if ($used) header('Location: '.SITEROOT.'/register?regKey='.$regKey);*/
			$username = sanatizeString($_POST['username']);
			$password1 = $_POST['password1'];
			$password2 = $_POST['password2'];
			$email = sanatizeString($_POST['email']);
			$hear = sanatizeString($_POST['hear']);
			
			if (strlen($username) < 4) $_SESSION['errors']['userShort'] = 1;
			elseif (strlen($username) > 24) $_SESSION['errors']['userLong'] = 1;
//			elseif (!preg_match('/^\w/i', $username)) $_SESSION['errors']['userInvalid'] = 1;
			else {
				if (!preg_match('/^\w[\w\.]*$/i', $username) || $username != filterString($username)) $_SESSION['errors']['userInvalid'] = 1;
				$mysql->query('SELECT userID FROM users WHERE username = "'.strtolower($username).'"');
				if ($mysql->rowCount()) $_SESSION['errors']['userTaken'] = 1;
			}
			
			if ($_POST['gender'] != 'Pick One') { header('Location: '.SITEROOT.'/register/success/'.$username); exit; }
			
			if (strlen($password1) == 0) $_SESSION['errors']['passBlank'] = 1;
			else {
				if (strlen($password1) < 6) $_SESSION['errors']['passShort'] = 1;
				elseif (strlen($password1) > 16) $_SESSION['errors']['passLong'] = 1;
				elseif ($password1 != $password2) $_SESSION['errors']['passMismatch'] = 1;
			}
			
			if (strlen($email) == 0) $_SESSION['errors']['emailBlank'] = 1;
			else {
				if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[_a-z0-9-]+(\.[_a-z0-9-]+)*(\.[a-z]{2,3})$/', strtolower($email))) { $_SESSION['errors']['emailInvalid'] = 1; }
				$mysql->query('SELECT userID from users WHERE email = "'.strtolower($email).'"');
				if ($mysql->rowCount()) { $_SESSION['errors']['emailTaken'] = 1; }
			}
			
			require_once(FILEROOT.'/register/recaptcha/recaptchalib.php');
			$privatekey = '6LeuZgcAAAAAACECj0h1wj9SsR2CtuluSyMzBezb';
			$resp = recaptcha_check_answer ($privatekey,
											$_SERVER['REMOTE_ADDR'],
											$_POST['recaptcha_challenge_field'],
											$_POST['recaptcha_response_field']);
			
			if (!$resp->is_valid) {
				$_SESSION['errors']['captchaFailed'] = 1;
			}
			
			if (sizeof($_SESSION['errors'])) {
				$_SESSION['errorVals']['username'] = $_POST['username'];
				$_SESSION['errorVals']['email'] = $_POST['email'];
				$_SESSION['errorTime'] = time() + 300;
				header('Location: '.SITEROOT.'/register?failed=1');
			} else {
				$mysql->query('INSERT INTO users '.$mysql->setupInserts(array('username' => $username, 'password' => hash('sha256', SVAR.$password1), 'email' => $email, 'joinDate' => date('Y-m-d H:i:s'), 'referrence' => $hear/*, 'regKey' => $regKey*/)));
//				$mysql->query('UPDATE regKeys SET used = 1 WHERE regKey = "'.$regKey.'"');
				if ($mysql->getResult()) {
					$message = "Thank you for registering for Gamers Plane!\n\n";
					$message .= "Please click on the following link to activate your account:\n";
					$message .= '<a href="http://gamersplane.com/register/activate/'.md5($username)."\">Activate account</a>\n";
					$message .= 'Or copy and paste this URL into your browser: http://gamersplane.com/register/activate/'.md5($username)."\n\n";
					$message .= 'Please do not respond to this email, as it will be ignored';
					mail($email, 'Gamers Plane Activate Required', $message, 'From: contact@gamersplane.com');
					
					wp_create_user($username, $password1, $email);
					
					header('Location: '.SITEROOT.'/register/success/'.$username);
				} else header('Location: '.SITEROOT.'/register?failed=1');
			}
/*		}
	} elseif (isset($_POST['srk'])) {
		$regKey = $_POST['regKey'];
		if (!preg_match('/^[A-Z0-9 -]{20}$/i', $_POST['regKey'])) header('Location: '.SITEROOT.'/register/?invalidKey=1');
		$regKey = sanatizeString(str_replace(array(' ', '-'), '', $_POST['regKey']));
		header('Location: '.SITEROOT.'/register?regKey='.$regKey);*/
	} //else header('Location: '.SITEROOT.'/403/');
?>