<?
	if (isset($_POST['submit'])) {
		$errors = array();
		$email = sanitizeString($_POST['email']);
		$pass1 = sanitizeString($_POST['pass1']);
		$pass2 = sanitizeString($_POST['pass2']);
		
		if (strlen($pass1) < 6) 
			$errors['passError'] = 'short';
		elseif (strlen($pass1) > 32) 
			$errors['passError'] = 'long';
		elseif ($pass1 != $pass2) 
			$errors['passError'] = 'mismatch';
		
		$user = new User($email);
		if ($user) {
			$g_validationStr = preg_match('/^[a-z0-9]{13}$/i', $_POST['validationStr'])?$_POST['validationStr']:false;
			$o_validationStr = substr(md5($email.substr($user->password, 3).$user->activatedOn), 4, 13);
			if ($g_validationStr == $o_validationStr) {
				$user->updatePassword($pass1);

				if ($_POST['ajaxForm']) 
					echo 'success';
				else 
					header('Location: /login/?resetSuccess=1');
			} else {
				$errors['invalidValidation'] = 1;

				if ($_POST['ajaxForm']) 
					echo json_encode($errors);
				else 
					header('Location: /login/resetPass/?'.http_build_query($errors));
			}
		} else {
			if (!$userCheck->rowCount()) $errors['invalidUser'] = 1;

			if ($_POST['ajaxForm']) 
				echo json_encode($errors);
			else 
				header('Location: /login/resetPass/?'.http_build_query($errors));
		}
	} else 
		header('Location: /login/resetPass/');
?>