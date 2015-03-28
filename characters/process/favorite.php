<?
	$characterID = intval($_POST['characterID']);
	$charCheck = $mysql->query("SELECT inLibrary FROM characterLibrary WHERE characterID = $characterID");
	if ($inLibrary = $charCheck->fetchColumn()) {
		if ($inLibrary) {
			$unfavorited = $mysql->query("DELETE FROM characterLibrary_favorites WHERE userID = {$currentUser->userID} AND characterID = $characterID");
			$state = $unfavorited->rowCount()?'unfavorited':'favorited';
			if ($state == 'favorited') $mysql->query("INSERT INTO characterLibrary_favorites SET userID = {$currentUser->userID}, characterID = $characterID");
			addCharacterHistory($characterID, ($state == 'favorited'?'charFavorited':'charUnfavorited'));
			echo $state;
		} else 
			echo 'not in library';
	} else 
		echo 0;
?>