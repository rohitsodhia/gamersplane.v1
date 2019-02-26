<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	function newEmail() {
		$mail = new PHPMailer(true);

		$mail->isSMTP();
		$mail->Host = 'mail.gamersplane.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'contact@gamersplane.com';
		$mail->Password = 'J3B2bxVy0o4rXWuLd397';
		$mail->SMTPSecure = 'tls';
		$mail->Port = 465;
	
		$mail->setFrom('contact@gamersplane.com');
	
		$mail->isHTML(true);

		return $mail;
	}
