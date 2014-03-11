<?
	checkLogin(0);
	
	if (isset($_POST['favorite'])) {
	} elseif (isset($_POST['unfavorite'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$favExists = $mysql->query('SELECT updateDate FROM characterLibrary_favorites WHERE userID = '.$userID.' AND characterID = '.$characterID);
		if (!$favExists->rowCount()) {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: '.SITEROOT.'/403');
		} else {
			$mysql->query("DELETE FROM characterLibrary_favorites WHERE userID = $userID AND characterID = $characterID");
			
			if (isset($_POST['modal'])) echo 'unfavorited';
			else header('Location: '.SITEROOT.'/characters/my?unfavorited=1');
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: '.SITEROOT.'/403');
	}
?>