<?
	$pmID = intval($_POST['pmID']);
	
	if (isset($_POST['delete'])) {
		$recipientCheck = $mysql->query('SELECT recipientID, senderID FROM pms WHERE pmID = '.$pmID);
		list($recipientID, $senderID) = $recipientCheck->fetch(PDO::FETCH_NUM);
		
		if ($recipientID == $currentUser->userID || $senderID == $currentUser->userID) {
			$mysql->query('DELETE FROM pms WHERE pmID = '.$pmID);
			
			if (isset($_POST['modal'])) echo 1;
			else header('Location: /pms/?deleteSuc=1');
		} else {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: /403');
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: /pms');
	}
?>