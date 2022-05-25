<?
	function sendActivationEmail($email, $username) {
		$pathBase = 'https://'.getenv('APP_URL');
		$message = "Thank you for registering for Gamers Plane!\n\n";
		$message .= "Please click on the following link to activate your account:\n";
		$message .= '<a href="'.$pathBase.'/register/activate/'.md5($username)."\">Activate account</a>\n";
		$message .= 'Or copy and paste this URL into your browser: '.$pathBase.'/register/activate/'.md5($username)."/\n\n";
		$message .= 'Please do not respond to this email, as it will be ignored';
		$mailSent = false;
		if (strtolower($_SERVER['HTTP_HOST']) == getenv('APP_URL')) {
			$mail = getMailObj();
			$mail->addAddress($email);
			$mail->Subject = "Gamers' Plane Activation Required";
			$mail->msgHTML($message);
			do {
				$sent = $mail->send();
			} while (!$sent);
		}
	}
?>
