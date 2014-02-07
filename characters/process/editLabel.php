<?
	checkLogin(0);
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$label = sanitizeString($_POST['label']);
		$labelCheck = $mysql->query("SELECT label FROM characters WHERE userID = $userID AND characterID = $characterID");
		
		if ($labelCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: '.SITEROOT.'/403');
		} elseif (strlen($label) == 0) {
			if (isset($_POST['modal'])) echo 'invalidLabel';
			else header("Location: {$_SESSION['lastURL']}?invalidLabel=1");
		} else {
			$updateLabel = $mysql->prepare("UPDATE characters SET label = :label WHERE characterID = $characterID");
			$updateLabel->execute(array(':label' => $label));
			addCharacterHistory($characterID, 'labelEdited', $userID);
			if (isset($_POST['modal'])) echo 'updated';
			else header('Location: '.SITEROOT.'/characters/my?label=1');
		}
	} elseif (isset($_POST['cancel'])) header('Location: '.SITEROOT.'/characters/my');
	else header('Location: '.SITEROOT.'/403');
?>