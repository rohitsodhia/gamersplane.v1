<?
	if (isset($_POST['submit'])) {
		unset($_SESSION['errors']);
		unset($_SESSION['errorVals']);
		unset($_SESSION['errorTime']);

		$inserts['name'] = sanitizeString($_POST['name']);
		$inserts['date'] = date('Y-m-d H:i:s');
		$inserts['username'] = sanitizeString($_POST['username']);
		$inserts['email'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)?$_POST['email']:'';
		$inserts['subject'] = sanitizeString($_POST['subject']);
		$inserts['comment'] = sanitizeString($_POST['comment']);

		$nonBlankFields = array('name', 'email', 'subject', 'comment');
		foreach ($inserts as $field => $value) { if ((in_array($field, $nonBlankFields) && $value == '')) {
			$_SESSION['errors'][$field] = 1;
		} }

		if (sizeof($_SESSION['errors'])) {
			$_SESSION['errorVals'] = $inserts;
			$_SESSION['errorTime'] = time() + 300;

			if (isset($_POST['modal'])) echo 0;
			else header('Location: /contact/failed');
		} else {
			$addContact = $mysql->prepare('INSERT INTO contact SET name = :name, date = :date, username = :username, email = :email, subject = :subject, comment = :comment');
			$addContact->bindValue(':name', $inserts['name']);
			$addContact->bindValue(':date', $inserts['date']);
			$addContact->bindValue(':username', $inserts['username']);
			$addContact->bindValue(':email', $inserts['email']);
			$addContact->bindValue(':subject', $inserts['subject']);
			$addContact->bindValue(':comment', $inserts['comment']);
			$addContact->execute();

			$message = '';
			foreach ($inserts as $key => $value) { $message .= ucfirst($key).":\n".printReady($value)."\n\n"; }

			$mail->addAddress("contact@gamersplane.com");
			$mail->Subject = "Gamers' Plane Contact: ".printReady($inserts["subject"]);
			$mail->body = $message;
			$mail->setFrom($inserts["email"]);
			$mail->send();

			unset($_SESSION['errors']);
			unset($_SESSION['errorVals']);

			if (isset($_POST['modal'])) echo 1;
			else header('Location: /contact/success');
		}
	} else { header('Location: /contact'); }
?>
