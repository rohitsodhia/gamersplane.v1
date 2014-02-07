<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$updateChar = $mysql->prepare('UPDATE custom_characters SET charSheet = :charSheet WHERE characterID = '.$characterID);
			$updateChar->execute(array(':charSheet' => sanitizeString($_POST['charSheet'])));
			addCharacterHistory($characterID, 'editedChar');
		}
		header('Location: '.SITEROOT.'/characters/custom/sheet/'.$characterID);
	} else { header('Location: '.SITEROOT.'/403'); }
?>