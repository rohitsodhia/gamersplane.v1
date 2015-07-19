<?
	if (isset($_POST['save'])) {
		$characterID = intval($_POST['characterID']);
		$label = sanitizeString($_POST['label']);
		$charType = in_array($_POST['charType'], $charTypes)?$_POST['charType']:'PC';
		$labelCheck = $mysql->query("SELECT label, charType FROM characters WHERE retired IS NULL AND userID = {$currentUser->userID} AND characterID = $characterID");
		
		if ($labelCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) 
				echo 0;
			else 
				header('Location: /403/');
		} elseif (strlen($label) == 0) {
			if (isset($_POST['modal'])) 
				echo 'invalidLabel';
			else 
				header("Location: {$_SESSION['lastURL']}?invalidLabel=1");
		} else {
			$updateLabel = $mysql->prepare("UPDATE characters SET label = :label, charType = :charType WHERE characterID = $characterID");
			$updateLabel->bindValue(':label', $label);
			$updateLabel->bindValue(':charType', $charType);
			$updateLabel->execute();
			addCharacterHistory($characterID, 'basicEdited', $currentUser->userID);
			if (isset($_POST['modal'])) 
				echo 'updated';
			else 
				header('Location: /characters/my?label=1');
		}
	} elseif (isset($_POST['cancel'])) 
		header('Location: /characters/my');
	else 
		header('Location: /403');
?>