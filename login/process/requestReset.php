<?
	checkLogin(0);
	
	if (isset($_POST['submit'])) {
		$email = sanatizeString($_POST['email']);
		
		$userCheck = $mysql->query('SELECT email FROM users WHERE email = "'.$email.'"');
		if ($userCheck->rowCount()) {
			$rndPass = randomAlphaNum(10);
			$validationStr = md5($email.'r3Qu'.$rndPass);
			$body = "Gamers Plane Password Reset

---------------------------------------------------

To reset your password, please go to

http://gamersplane.com/login/resetPass?validate={$validationStr}

Enter your current username and the following validation code

{$rndPass}

It will take you to a page where you can enter a new password of your choice.";
			mail($email, 'Gamers Plane Password Reset', $body, "From: contact@gamersplane.com\r\nReply-To: contact@gamersplane.com");
			header('Location: '.SITEROOT.'/login/requestReset?success=1');
		} else header('Location: '.SITEROOT.'/login/requestReset?invalidEmail=1');
	} else header('Location: '.SITEROOT.'/login/requestReset');
?>