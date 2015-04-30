<?
	function sendActivationEmail($email, $username) {
		$message = "Thank you for registering for Gamers Plane!\n\n";
		$message .= "Please click on the following link to activate your account:\n";
		$message .= '<a href="http://gamersplane.com/register/activate/'.md5($username)."\">Activate account</a>\n";
		$message .= 'Or copy and paste this URL into your browser: http://gamersplane.com/register/activate/'.md5($username)."/\n\n";
		$message .= 'Please do not respond to this email, as it will be ignored';
		$mailSent = false;
		do {
			$mailSent = mail($email, 'Gamers Plane Activation Required', $message, 'From: contact@gamersplane.com');
		} while (!$mailSent);
	}
?>