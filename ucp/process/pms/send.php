<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	
	if (isset($_POST['post'])) {
		unset($_SESSION['errors']);
		unset($_SESSION['errorTime']);
		
		$pmID = intval($_POST['pmID']);
		$username = sanatizeString($_POST['username']);
		$title = sanatizeString($_POST['title']);
		$message = sanatizeString($_POST['message']);
		
/*		if ($pathOptions[1] == 'reply') {
			$mysql->setTable('pms');
			$mysql->setSelectCols('COUNT(*)');
			$mysql->setWhere('pmID = '.$pmID);
			$mysql->stdQuery('select', 'selectCols', 'where');
			
			if (!$mysql->numRow()) { header('Location: '.SITEROOT.'/unauthorized'); }
		}*/
		
		$recipientCheck = $mysql->query("SELECT userID FROM users WHERE username = '$username'");
		$recipientID = $recipientCheck->fetchColumn();
		
		if (!$recipientID) $_SESSION['errors']['invalidUser'] = TRUE;
		if (!strlen($title)) $_SESSION['errors']['noTitle'] = TRUE;
		if (!strlen($message)) $_SESSION['errors']['noMessage'] = TRUE;
		
		if ($_SESSION['errors']) {
			$_SESSION['errorVals']['username'] = $username;
			$_SESSION['errorVals']['title'] = $title;
			$_SESSION['message']['title'] = $message;
			$_SESSION['errorTime'] = time() + 300;
			
			header('Location: '.SITEROOT.'/ucp/pms/send/failed');
		} else {
			unset($_SESSION['errors']);
			unset($_SESSION['errorTime']);
			
			$sendMessage = $mysql->prepare('INSERT INTO pms SET recipientID = :recipientID, senderID = :senderID, title = :title, message = :message, datestamp = :datestamp, replyTo = :replyTo');
			$sendMessage->execute(array(
				':recipientID' => $recipientID,
				':senderID' => $userID,
				':title' => $title,
				':message' => $message,
				':datestamp' => date('Y-m-d H:i:s'),
				':replyTo' => $pmID
			));
			
			header('Location: '.SITEROOT.'/ucp/pms/?sent=1');
		}
	} else header('Location: '.SITEROOT.'/ucp/pms');
?>