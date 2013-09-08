<?
	checkLogin();
	
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	if (isset($_POST['save']) || isset($_POST['deleteAction']) || isset($_POST['cancel'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (isset($_POST['cancel'])) header('Location: '.SITEROOT.'/characters/marvel/sheet/'.$characterID);
		$stoneInfo = $mysql->query('SELECT unusedStones, totalStones FROM marvel_characters WHERE characterID = '.$characterID);
		list($unusedStones, $totalStones) = $stoneInfo->fetch();
		
		$actionID = intval($_POST['actionID']);
		$actionInfo = $mysql->query('SELECT actions.actionID, actions.name, actions.cost, playerActions.playerActionID, playerActions.level, playerActions.offset, playerActions.stonesSpent, playerActions.details FROM marvel_actions AS actions LEFT JOIN marvel_playerActions AS playerActions ON actions.actionID = playerActions.actionID AND playerActions.characterID = '.$characterID.' WHERE actions.actionID = '.$actionID);
		$actionInfo = $actionInfo->fetch();
		
		if ($actionInfo['playerActionID']) {
			if (isset($_POST['alterStones'])) {
				$unusedStones = formatStones($unusedStones + $actionInfo['stonesSpent']);
				$updates['unusedStones'] = $unusedStones;
			}
			$totalStones = formatStones($totalStones - $actionInfo['stonesSpent']);
			$updates['totalStones'] = $totalStones;
			$mysql->query('UPDATE marvel_characters SET '.setupUpdates($updates).' WHERE characterID = '.$characterID);
		}
		
		if (isset($_POST['deleteAction'])) {
			$mysql->query('DELETE FROM marvel_playerActions WHERE playerActionID = '.$actionInfo['playerActionID']);
		} else {
			$stonesSpent = ($actionInfo['cost'] >= 0)?$marvel_cost[$_POST['level'] + $actionInfo['cost'] + $_POST['offset']]:abs($actionInfo['cost']);
			if ($actionInfo['playerActionID']) {
				$updates = array('level' => intval($_POST['level']), 'offset' => intval($_POST['offset']), 'details' => sanatizeString($_POST['details']), 'stonesSpent' => formatStones($stonesSpent));
				
				$mysql->query('UPDATE marvel_playerActions SET '.setupUpdates($updates).' WHERE playerActionID = '.$actionInfo['playerActionID']);
			} else {
				$inserts = array('characterID' => $characterID, 'actionID' => $actionID, 'level' => intval($_POST['level']), 'offset' => intval($_POST['offset']), 'details' => sanatizeString($_POST['details']), 'stonesSpent' => formatStones($stonesSpent));
				
				$mysql->query('INSERT INTO marvel_playerActions '.setupInserts($inserts);
			}
			
			unset($updates);
			if (isset($_POST['alterStones'])) {
				$unusedStones = formatStones($unusedStones - $stonesSpent);
				$updates['unusedStones'] = $unusedStones;
			}
			$totalStones = formatStones($totalStones + $stonesSpent);
			$updates['totalStones'] = $totalStones;
			$mysql->query('UPDATE marvel_characters SET '.setupUpdates($updates).' WHERE characterID = '.$characterID);
		}
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");;
		
		header('Location: '.SITEROOT.'/characters/marvel/sheet/'.$characterID);
	} else { header('Location: '.SITEROOT.'/403'); }
?>