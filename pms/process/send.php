<?
	if (isset($_POST['send'])) {
		unset($_SESSION['errors']);
		unset($_SESSION['errorTime']);
		
		$replyTo = intval($_POST['replyTo']);
		$username = sanitizeString($_POST['username']);
		$title = sanitizeString($_POST['title']);
		$message = sanitizeString($_POST['message']);
		
		if ($replyTo) {
			try { $replyManager = new PMManager($pmID); }
			catch (Exception $e) { header('Location: /pms/'); exit; }
		}
		
		$recipientCheck = $mysql->prepare("SELECT userID FROM users WHERE username = :username");
		$recipientCheck->bindValue(':username', $username);
		$recipientCheck->execute();
		$recipientID = $recipientCheck->fetchColumn();

		$formErrors->clearErrors();
		
		if (!$recipientID) $formErrors->addError('invalidUser');
		if (!strlen($title)) $formErrors->addError('noTitle');
		if (!strlen($message)) $formErrors->addError('noMessage');
		
		if ($formErrors->errorsExist()) {
			$formErrors->setErrors('pm', $_POST);
			header('Location: '.$_SESSION['lastURL']);
		} else {
			$sendMessage = $mysql->prepare('INSERT INTO pms SET senderID = :senderID, recipientIDs = :recipientID, title = :title, message = :message, datestamp = NOW()'.($replyTo?', replyTo = :replyTo':''));
			$sendMessage->bindValue(':senderID', $currentUser->userID);
			$sendMessage->bindValue(':recipientID', $recipientID);
			$sendMessage->bindValue(':title', $title);
			$sendMessage->bindValue(':message', $message);
			if ($replyTo) 
				$sendMessage->bindValue(':replyTo', $replyTo);
			$sendMessage->execute();
			$pmID = $mysql->lastInsertId();

			$mysql->query("INSERT INTO pms_inBox SET pmID = {$pmID}, userID = {$currentUser->userID}");
			$mysql->query("INSERT INTO pms_inBox SET pmID = {$pmID}, userID = {$recipientID}");

			header('Location: /pms/?sent=1');
		}
	} else 
		header('Location: /pms/');
?>