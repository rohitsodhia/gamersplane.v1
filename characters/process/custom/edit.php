<?
	checkLogin();
	
	print_r($_POST);
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		echo('UPDATE custom_characters SET charSheet = "'.sanatizeString($_POST['charSheet']).'" WHERE characterID = '.$characterID);
		$mysql->query('UPDATE custom_characters SET charSheet = "'.sanatizeString($_POST['charSheet']).'" WHERE characterID = '.$characterID);
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");;
		header('Location: '.SITEROOT.'/characters/custom/sheet/'.$characterID);
	} else { header('Location: '.SITEROOT.'/403'); }
?>