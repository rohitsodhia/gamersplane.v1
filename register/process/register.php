<?php
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

		if (strlen($username) < 4) {
			$formErrors->addError('userShort');
		} elseif (strlen($username) > 24) {
			$formErrors->addError('userLong');
		} else {
			if (!preg_match('/^\w[\w\.]*$/i', $username) || $username != filterString($username)) {
				$formErrors->addError('userInvalid');
			}
			$userCheck = $mysql->prepare('SELECT userID FROM users WHERE LOWER(username) = ?');
			$userCheck->execute([strtolower($username)]);
			if ($userCheck->rowCount()) {
				$formErrors->addError('userTaken');
			}
		}

		if (strlen($password1) == 0) {
			$formErrors->addError('passBlank');
		} else {
			if (strlen($password1) < 6) {
				$formErrors->addError('passShort');
			} elseif (strlen($password1) > 32) {
				$formErrors->addError('passLong');
			} elseif ($password1 != $password2) {
				$formErrors->addError('passMismatch');
			}
		}

		if (strlen($email) == 0) {
			$formErrors->addError('emailBlank');
		} else {
			$blacklistedDomains = ['temporary-mail'];
			foreach ($blacklistedDomains as $domain) {
				if (strpos($email, '@'.$domain) !== FALSE) {
					$formErrors->addError('emailInvalid');
				}
			}
			if (!$formErrors->checkError('emailInvalid')) {
				$disposable_json = file_get_contents('https://open.kickbox.com/v1/disposable/'.$email);
				if (strpos($disposable_json, 'true') !== FALSE) {
					$formErrors->addError('emailInvalid');
				} else {
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$formErrors->addError('emailInvalid');
					} else {
						$emailCheck = $mysql->prepare('SELECT userID from users WHERE LOWER(email) = ?');
						$emailCheck->execute([strtolower($email)]);
						if ($emailCheck->rowCount()) {
							$formErrors->addError('emailTaken');
						}
					}
				}
			}
		}

		if (strtolower($_SERVER['HTTP_HOST']) == 'gamersplane.com') {
			$secret = '6LcT8gsTAAAAAEA0RemG5ryLemgp4h8uwwbCHFgs';
			$recaptcha_options = [
				'http' => [
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query(['secret' => $secret, 'response' => $_POST['g-recaptcha-response']])
				]
			];
			$context  = stream_context_create($recaptcha_options);
			$recaptcha = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context));
			if (!$recaptcha->success) {
				$formErrors->addError('recaptcha');
			}
		}

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
				$mail = getMailObj();
				$mail->addAddress("contact@gamersplane.com");
				$mail->Subject = "New User";
				$mail->body = "New User: {$username}";
				$mail->send();

//				wp_create_user($username, $password1, $email);

				header("Location: /register/success/{$username}");
			} else header('Location: /register/?failed=1');
		}
	} else {
		header('Location: /register/');
	}
?>
