<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);

	if (isset($_POST['send'])) {
		unset($_SESSION['errors']);
		unset($_SESSION['errorTime']);
		
		$pmID = intval($_POST['pmID']);
		$username = sanitizeString($_POST['username']);
		$title = sanitizeString($_POST['title']);
		$message = sanitizeString($_POST['message']);
		
/*		if ($pathOptions[1] == 'reply') {
			$mysql->setTable('pms');
			$mysql->setSelectCols('COUNT(*)');
			$mysql->setWhere('pmID = '.$pmID);
			$mysql->stdQuery('select', 'selectCols', 'where');
			
			if (!$mysql->numRow()) { header('Location: /unauthorized'); }
		}*/
		
		$recipientCheck = $mysql->prepare("SELECT userID FROM users WHERE username = :username");
		$recipientCheck->bindValue(':username', $username);
		$recipientCheck->execute();
		$recipientID = $recipientCheck->fetchColumn();
		
		if (!$recipientID) $_SESSION['errors']['invalidUser'] = TRUE;
		if (!strlen($title)) $_SESSION['errors']['noTitle'] = TRUE;
		if (!strlen($message)) $_SESSION['errors']['noMessage'] = TRUE;
		
		if ($_SESSION['errors']) {
			$_SESSION['errorVals']['username'] = $username;
			$_SESSION['errorVals']['title'] = $title;
			$_SESSION['message']['message'] = $message;
			$_SESSION['errorTime'] = time() + 300;
			
			header('Location: /pms/send/failed');
		} else {
			unset($_SESSION['errors']);
			unset($_SESSION['errorTime']);
			
			$sendMessage = $mysql->prepare('INSERT INTO pms SET recipientID = :recipientID, senderID = :senderID, title = :title, message = :message, datestamp = :datestamp, replyTo = :replyTo');
			$sendMessage->bindValue(':recipientID', $recipientID);
			$sendMessage->bindValue(':senderID', $userID);
			$sendMessage->bindValue(':title', $title);
			$sendMessage->bindValue(':message', $message);
			$sendMessage->bindValue(':datestamp', date('Y-m-d H:i:s'));
			$sendMessage->bindValue(':replyTo', $pmID);
			$sendMessage->execute();
			
			header('Location: /pms/?sent=1');
		}
	} else header('Location: /pms');
?>