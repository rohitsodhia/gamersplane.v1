<?
	checkLogin(0);
	
	if (isset($_POST['delete'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$shortName = $mysql->query('SELECT systems.shortName FROM characters, systems WHERE characters.systemID = systems.systemID AND characters.userID = '.intval($_SESSION['userID']).' AND characters.characterID = '.$characterID);
		if (!$shortName->rowCount()) {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: '.SITEROOT.'/403');
		} else {
			$system = $shortName->fetchColumn();
			$mysql->query('DELETE FROM characters WHERE characterID = '.$characterID);
			$tables = $mysql->query("SHOW TABLES LIKE '{$shortName}_%'");
			while ($table = $tables->fetchColumn()) $mysql->query('DELETE FROM '.$table.' WHERE characterID = '.$characterID);
			
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'deleted')");
			
			if (isset($_POST['modal'])) echo 'deleted';
			else header('Location: '.SITEROOT.'/characters/my?delete=1');
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: '.SITEROOT.'/403');
	}
?>