<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$mysql->query('UPDATE custom_characters SET charSheet = "'.sanatizeString($_POST['charSheet']).'" WHERE characterID = '.$characterID);
			addCharacterHistory($characterID, 'editedChar');
		header('Location: '.SITEROOT.'/characters/custom/sheet/'.$characterID);
	} else { header('Location: '.SITEROOT.'/403'); }
?>