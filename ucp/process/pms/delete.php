<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$pmID = intval($_POST['pmID']);
	
	if (isset($_POST['delete'])) {
		$recipientCheck = $mysql->query('SELECT recipientID FROM pms WHERE pmID = '.$pmID);
		$recipientID = $recipientCheck->fetchColumn();
		
		if ($recipientID == $userID) {
			$mysql->query('DELETE FROM pms WHERE pmID = '.$pmID);
			
			header('Location: '.SITEROOT.'/ucp/pms/?deleteSuc=1');
		} else { header('Location: '.SITEROOT.'/403'); }
	} else { header('Location: '.SITEROOT.'/ucp/pms'); }
?>