<?
	checkLogin();
	
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	if (isset($_POST['submit'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$mysql->query('UPDATE marvel_characters SET '.setupUpdates(array('unusedStones' => formatStones($_POST['startingStones']), 'normName' => sanatizeString($_POST['normName']), 'superName' => sanatizeString($_POST['superName']))).' characterID = '.$characterID);
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");;
		
		header('Location: '.SITEROOT.'/characters/marvel/sheet/'.intval($pathOptions[2]));
	} else header('Location: '.SITEROOT.'/403');
?>