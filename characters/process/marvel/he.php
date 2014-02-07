<?
	checkLogin();
	
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$mysql->query('UPDATE marvel_characters SET '.setupUpdates(array('normName' => sanitizeString($_POST['normName']), 'superName' => sanitizeString($_POST['superName']), 'health_max' => intval($_POST['maxHealth']), 'health_current' => intval($_POST['maxHealth']), 'energy_max' => intval($_POST['maxEnergy']), 'energy_current' => intval($_POST['maxEnergy']))).' WHERE characterID = '.$characterID);
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");;
		
		header('Location: '.SITEROOT.'/characters/marvel/sheet/'.$characterID);
	} else { header('Location: '.SITEROOT.'/403'); }
?>