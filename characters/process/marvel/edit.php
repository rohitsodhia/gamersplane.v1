<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$updates = array();
			$numVals = array('health_max', 'energy_max', 'int', 'str', 'agi', 'spd', 'dur');
			$textVals = array('normName', 'superName', 'notes');
			foreach ($_POST as $key => $value) {
				if (in_array($key, $textVals)) $updates[$key] = sanatizeString($value);
				elseif (in_array($key, $numVals)) {
					$updates[$key] = number_format(floatval($value), 1);
					if ($updates[$key] == intval($value)) $updates[$key] = intval($value);
				} elseif ($key == 'white') $updates['unusedStones'] = number_format(intval($_POST['white']) + intval($_POST['red']) / 3, 1);
			}
			
			foreach ($_POST['action'] as $key => $value) {
				$key = intval($key);
				$value['cost'] = number_format(floatval($value['cost']), 1);
				if ($value['cost'] == intval($value['cost'])) $value['cost'] = intval($value['cost']);
				$value['level'] = intval($value['level']);
				$value['details'] = sanatizeString($value['details']);
				$mysql->query("UPDATE marvel_actions SET level = {$value['level']}, details = '{$value['details']}', stonesSpent = {$value['cost']} WHERE characterID = $characterID AND actionID = $key");
			}
			
			foreach ($_POST['modifier'] as $key => $value) {
				$key = intval($key);
				$value['cost'] = number_format(floatval($value['cost']), 1);
				if ($value['cost'] == intval($value['cost'])) $value['cost'] = intval($value['cost']);
				$value['level'] = intval($value['level']);
				$value['details'] = sanatizeString($value['details']);
				$mysql->query("UPDATE marvel_modifiers SET level = {$value['level']}, details = '{$value['details']}', stonesSpent = {$value['cost']} WHERE characterID = $characterID AND modifierID = $key");
			}
			
			foreach ($_POST['challenge'] as $key => $value) {
				$key = intval($key);
				$value = intval($value['cost']);
				$mysql->query("UPDATE marvel_challenges SET stones = $value WHERE challengeID = $key");
			}
			
			$mysql->query('UPDATE marvel_characters SET '.setupUpdates($updates).' WHERE characterID = '.$characterID);
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
			header('Location: '.SITEROOT.'/characters/marvel/sheet/'.$characterID);
		} else header('Location: '.SITEROOT.'/403');
	} else header('Location: '.SITEROOT.'/403');
?>