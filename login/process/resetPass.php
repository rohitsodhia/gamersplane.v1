<?
	checkLogin(0);

	if (isset($_POST['submit'])) {
		$errors = array();
		$username = sanitizeString($_POST['username'], '+lower');
		$key = sanitizeString($_POST['key']);
		$pass1 = sanitizeString($_POST['pass1']);
		$pass2 = sanitizeString($_POST['pass2']);
		
		if (strlen($pass1) < 6) $errors['passError'] = 'short';
		elseif (strlen($pass1) > 32) $errors['passError'] = 'long';
		elseif ($pass1 != $pass2) $errors['passError'] = 'mismatch';
		
		$userCheck = $mysql->prepare('SELECT userID, email FROM users WHERE LOWER(username) = ?');
		$userCheck->execute(array($username));

		if ($userCheck->rowCount() && sizeof($errors) == 0) {
			$userInfo = $userCheck->fetch();
			if (md5($userInfo['email'].'r3Qu'.$key) == $_POST['validationStr']) {
				$mysql->query('UPDATE users SET password = "'.hash('sha256', SVAR.$pass1).'" WHERE userID = '.$userInfo['userID']);
				if ($_POST['ajaxForm']) echo 'success';
				else header('Location: /login/?resetSuccess=1');
			} else {
				$errors['invalidValidation'] = 1;

				if ($_POST['ajaxForm']) echo json_encode($errors);
				else header('Location: /login/resetPass/?'.http_build_query($errors));
			}
		} else {
			if (!$userCheck->rowCount()) $errors['invalidUser'] = 1;

			if ($_POST['ajaxForm']) echo json_encode($errors);
			else header('Location: /login/resetPass/?'.http_build_query($errors));
		}
	} else header('Location: /login/resetPass/');
?>