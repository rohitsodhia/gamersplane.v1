<?
	if (isset($_POST['submit'])) {
		$email = sanitizeString($_POST['email'], '+lower');

		$userCheck = $mysql->prepare('SELECT userID FROM users WHERE LOWER(email) = ? AND activatedOn IS NOT NULL');
		$userCheck->execute(array($email));
		if ($userCheck->rowCount()) {
			$user = new User($email);
			$validationStr = substr(md5($email.substr($user->password, 3).$user->activatedOn), 4, 13);
			$pathBase = 'https://'.getenv('APP_URL');
			$body = "Gamers Plane Password Reset

---------------------------------------------------

To reset your password, please go to

{$pathBase}/login/resetPass/?email={$email}&validate={$validationStr}

It will take you to a page where you can enter a new password of your choice.";
			mail($email, 'Gamers Plane Password Reset', $body, "From: contact@gamersplane.com\r\nReply-To: contact@gamersplane.com");
			header('Location: /login/requestReset/?success=1'.(isset($_POST['modal'])?'&modal=1':''));
		} else
			header('Location: /login/requestReset/?invalidEmail=1'.(isset($_POST['modal'])?'&modal=1':''));
	} else
		header('Location: /login/requestReset/'.(isset($_POST['modal'])?'?modal=1':''));
?>