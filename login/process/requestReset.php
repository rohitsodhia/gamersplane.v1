<?
	checkLogin(0);
	
	if (isset($_POST['submit'])) {
		$email = sanitizeString($_POST['email'], '+lower');
		
		$userCheck = $mysql->prepare('SELECT email FROM users WHERE LOWER(email) = ?');
		$userCheck->execute(array($email));
		if ($userCheck->rowCount()) {
			$rndPass = randomAlphaNum(10);
			$validationStr = md5($email.'r3Qu'.$rndPass);
			echo $rndPass;
			echo "http://gamersplane.com/login/resetPass?validate={$validationStr}";exit;
			$body = "Gamers Plane Password Reset

---------------------------------------------------

To reset your password, please go to

http://gamersplane.com/login/resetPass?validate={$validationStr}

Enter your current username and the following code

{$rndPass}

It will take you to a page where you can enter a new password of your choice.";
			mail($email, 'Gamers Plane Password Reset', $body, "From: contact@gamersplane.com\r\nReply-To: contact@gamersplane.com");
			if (isset($_POST['modal'])) echo 1;
			else header('Location: /login/requestReset?success=1'.(isset($_POST['modal'])?'&modal=1':''));
		} else {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: /login/requestReset?invalidEmail=1');
		}
	} else header('Location: /login/requestReset');
?>