<?
	$characterID = intval($_POST['characterID']);
	$charAllowed = $mysql->query("SELECT userID FROM characters WHERE characterID = $characterID AND userID = {$currentUser->userID}");
	if ($charAllowed->rowCount()) {
		$currentState = $mysql->query("SELECT inLibrary FROM characterLibrary WHERE characterID = $characterID");
		$currentState = $currentState->fetchColumn();
		addCharacterHistory($characterID, ($currentState?'removeFrom':'addTo').'Library');

		$mysql->query("INSERT INTO characterLibrary SET characterID = $characterID ON DUPLICATE KEY UPDATE inLibrary = inLibrary ^ 1");
		echo 1;
	} else 
		echo 0;
?>