<?
	checkLogin();
	
	if (isset($_POST['create'])) {
		$userID = intval($_SESSION['userID']);
		$_POST['system'] = intval($_POST['system']);
		$errors = '?';
		$systemShort = $mysql->query('SELECT shortName FROM systems WHERE systemID = '.$_POST['system']);
		if ($systemShort->rowCount() == 0) $errors .= 'invalidType=1&';
		else {
			$systemShort = $systemShort->fetchColumn();
		}
		if (strcmp(filterString($_POST['label']), $_POST['label']) || $_POST['label'] == '') $errors .= 'invalidLabel=1&';
		
		if ($errors != '?') header('Location: '.SITEROOT.'/characters/my/'.substr($errors, 0, -1));
		else {
			$mysql->query('INSERT INTO characters (userID, label, systemID) VALUES ('.$userID.', "'.sanatizeString($_POST['label']).'", '.$_POST['system'].')');
			$characterID = $mysql->lastInsertId();
			$mysql->query('INSERT INTO '.$systemShort.'_characters (characterID) VALUES ('.$characterID.')');
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action, additionalInfo) VALUES ($characterID, $userID, NOW(), 'created', {$_POST['system']})");
			
			if ($_POST['system'] != 8) $location = SITEROOT.'/characters/'.$systemShort.'/edit/'.$characterID.'?new=1';
			elseif ($_POST['system'] == 8) $location = SITEROOT.'/characters/marvel/new/'.$characterID;
			else $location = $_SESSION['lastURL'];
			header('Location: '.$location);
		}
	} else { header('Location: '.SITEROOT.'/403'); }
?>