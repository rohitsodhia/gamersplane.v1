<?
	checkLogin();
	
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$mysql->query('UPDATE marvel_characters SET notes = "'.sanatizeString($_POST['notes']).'" WHERE characterID = '.$characterID);
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");;
		
		header('Location: '.SITEROOT.'/characters/marvel/sheet/'.$characterID);
	} else { header('Location: '.SITEROOT.'/403'); }
?>