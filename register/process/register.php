<?
	define('SHORTINIT', TRUE);
//	require_once(FILEROOT.'/blog/wp-load.php');
//	require_once(FILEROOT.'/blog/wp-includes/registration.php');
	
	if (isset($_POST['submit'])) {
		unset($_SESSION['errors']);
		unset($_SESSION['errorVals']);
		unset($_SESSION['errorTime']);
		
		$username = sanitizeString($_POST['username']);
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		$email = sanitizeString($_POST['email']);
		$hear = sanitizeString($_POST['hear']);
		
		if (strlen($username) < 4) $formErrors->addError('userShort');
		elseif (strlen($username) > 24) $formErrors->addError('userLong');
		else {
			if (!preg_match('/^\w[\w\.]*$/i', $username) || $username != filterString($username)) $formErrors->addError('userInvalid');
			$userCheck = $mysql->prepare('SELECT userID FROM users WHERE LOWER(username) = ?');
			$userCheck->execute(array(strtolower($username)));
			if ($userCheck->rowCount()) $formErrors->addError('userTaken');
		}
		
		if (strlen($password1) == 0) $formErrors->addError('passBlank');
		else {
			if (strlen($password1) < 6) $formErrors->addError('passShort');
			elseif (strlen($password1) > 32) $formErrors->addError('passLong');
			elseif ($password1 != $password2) $formErrors->addError('passMismatch');
		}
		
		if (strlen($email) == 0) $formErrors->addError('emailBlank');
		else {
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $formErrors->addError('emailInvalid');
			$emailCheck = $mysql->prepare('SELECT userID from users WHERE LOWER(email) = ?');
			$emailCheck->execute(array(strtolower($email)));
			if ($emailCheck->rowCount()) $formErrors->addError('emailTaken');
		}
		
		require_once(FILEROOT.'/register/recaptcha/recaptchalib.php');
		$privatekey = '6LeuZgcAAAAAACECj0h1wj9SsR2CtuluSyMzBezb';
		$resp = recaptcha_check_answer ($privatekey,
										$_SERVER['REMOTE_ADDR'],
										$_POST['recaptcha_challenge_field'],
										$_POST['recaptcha_response_field']);
		
		if (!$resp->is_valid) $formErrors->addError('captchaFailed');
		
		if ($formErrors->errorsExist()) {
			$formErrors->setErrors('registration');
			header('Location: /register/?failed=1');
		} else {
			$newUser = new User();
			$userID = $newUser->newUser($username, $password1, $email);
			$newestPost = $mysql->query('SELECT MAX(postID) FROM posts');
			$newestPost = $newestPost->fetchColumn();
			$mysql->query("INSERT INTO forums_readData_forums SET userID = {$userID}, forumID = 0, markedRead = {$newestPost}");
			if ($userID) {
				$newUser->updateUsermeta('reference', $hear);

				sendActivationEmail($email, $username);
				mail('contact@gamersplane.com', 'New User', 'New User: '.$username, 'From: noone@gamersplane.com');

				addUserHistory($userID, 'registered');
				
//				wp_create_user($username, $password1, $email);
				
				header("Location: /register/success/{$username}");
			} else header('Location: /register/?failed=1');
		}
	} else header('Location: /register/');
?>