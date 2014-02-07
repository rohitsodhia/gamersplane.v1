<?
	checkLogin();
	
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	if (isset($_POST['save']) || isset($_POST['deleteModifier']) || isset($_POST['cancel'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (isset($_POST['cancel'])) header('Location: '.SITEROOT.'/characters/marvel/sheet/'.$characterID);
		$stoneInfo = $mysql->query('SELECT unusedStones, totalStones FROM marvel_characters WHERE characterID = '.$characterID);
		list($unusedStones, $totalStones) = $stoneInfo->fetch();
		
		$modifierID = intval($_POST['modifierID']);
		$modifierInfo = $mysql->query('SELECT modifiers.modifierID, modifiers.name, modifiers.cost, modifiers.costTo, playerModifiers.playerModifierID, playerModifiers.level, playerModifiers.offset, playerModifiers.extraStones, playerModifiers.timesTaken, playerModifiers.details, playerModifiers.stonesSpent FROM marvel_modifiers AS modifiers LEFT JOIN marvel_playerModifiers AS playerModifiers ON modifiers.modifierID = playerModifiers.modifierID AND playerModifiers.characterID = '.$characterID.' WHERE modifiers.modifierID = '.$modifierID);
		$modifierInfo = $modifierInfo->fetch();
		
		if ($modifierInfo['playerModifierID']) {
			if (isset($_POST['alterStones'])) {
				$unusedStones = formatStones($unusedStones + $modifierInfo['stonesSpent']);
				$updates['unusedStones'] = $unusedStones;
			}
			$totalStones = formatStones($totalStones - $modifierInfo['stonesSpent']);
			$updates['totalStones'] = $totalStones;
			$mysql->query('UPDATE marvel_characters SET '.setupUpdates($updates).' WHERE characterID = '.$characterID);
		}
		
		if (isset($_POST['deleteModifier'])) {
			$mysql->query('DELETE FROM marvel_playerModifiers WHERE playerModifierID = '.$modifierInfo['playerModifierID']);
		} else {
			$cost = formatStones($modifierInfo['cost']);
			$costTo = $modifierInfo['costTo'];
			$level = intval($_POST['level']);
			$offset = intval($_POST['offset']);
			$timesTaken = isset($_POST['timesTaken'])?intval($_POST['timesTaken']):1;
			$extraStones = intval($_POST['modifierOptionStones']);
			
			if ($modifierID == 23) { $stonesSpent = $level * $timesTaken / 3 + $extraStones; }
			elseif ($modifierID == 25) { $stonesSpent = $level * 2 + 1 + $extraStones; }
			elseif ($modifierID == 44 && $level == -1) { $stonesSpent = -2; }
			elseif ($modifierID == 44) { $stonesSpent = $marvel_cost[$level]; }
			elseif ($cost < 0) { $stonesSpent = abs($cost) * $timesTaken + $extraStones; }
			elseif ($costTo) { $stonesSpent = $marvel_cost[$cost + $charInfo[$costTo] + $offset] + $extraStones; }
			else { $stonesSpent = $marvel_cost[$level + $cost + $offset] + $extraStones; }
			
			if ($modifierInfo['playerModifierID']) {
				$updates = array('level' => $level, 'offset' => $offset, 'extraStones' => $extraStones, 'timesTaken' => $timesTaken, 'details' => sanitizeString($_POST['details']), 'stonesSpent' => formatStones($stonesSpent));
				
				$mysql->query('UPDATE marvel_playerModifiers SET '.setupUpdates($updates).' WHERE playerModifierID = '.$modifierInfo['playerModifierID']);
			} else {
				$inserts = array('characterID' => $characterID, 'modifierID' => $modifierInfo['modifierID'], 'level' => $level, 'offset' => $offset, 'extraStones' => $extraStones, 'timesTaken' => $timesTaken, 'details' => sanitizeString($_POST['details']), 'stonesSpent' => formatStones($stonesSpent));
				
				$mysql->query('INSERT INTO marvel_playerModifiers '.$mysql->setupInserts($inserts));
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