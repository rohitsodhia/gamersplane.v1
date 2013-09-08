<?
	checkLogin();
	
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$unusedStones = $mysql->query('SELECT unusedStones FROM marvel_characters WHERE characterID = '.$characterID);
		$unusedStones = $unusedStones->fetchColumn();
		
		if ($_POST['change'] == 'add') $updates = setupUpdates(array('unusedStones' => $unusedStones + formatStones(intval($_POST['white']) + intval($_POST['red']) / 3)));
		elseif ($_POST['change'] == 'sub') $updates = setupUpdates(array('unusedStones' => $unusedStones - formatStones(intval($_POST['white']) + intval($_POST['red']) / 3)));
		$mysql->query("UPDATE marvel_characters SET $updates WHERE characterID = $characterID");
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");;
		
		header('Location: '.SITEROOT.'/characters/marvel/sheet/'.$characterID);
	} else { header('Location: '.SITEROOT.'/403'); }
?>