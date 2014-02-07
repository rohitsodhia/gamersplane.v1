<?
	checkLogin();
	
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	if (isset($_POST['start'])) {
		$userID = intval($_SESSION['userID']);
		$mysql->setTable('marvel_characters');
		$mysql->setWhere('characterID = '.intval($_SESSION['characterID']));
		$mysql->setUpdates(array('unusedStones' => formatStones($_POST['startingStones'])));
		$mysql->stdQuery('update', 'where');
		
		header('Location: '.SITEROOT.'/characters/marvel/new/guided?step=basic');
	} elseif ((isset($_POST['save']) || isset($_POST['deleteAction']) || isset($_POST['deleteModifier'])) && isset($_SESSION['characterID'])) {
		$characterID = intval($_SESSION['characterID']);
		
		$mysql->setTable('marvel_characters');
		$mysql->setWhere('characterID = '.$characterID);
		$mysql->stdQuery('select', 'where');
		
		$charInfo = $mysql->fetch();
		
		if ($_GET['step'] == 'basics') {
			$updates = array('normName' => sanitizeString($_POST['normName']), 'superName' => sanitizeString($_POST['superName']));
			
			$stonesSpent = 0;
			if ($_SESSION['stepDone']['basic']) {
				foreach (array('int', 'str', 'agi', 'spd', 'dur') as $stat) {
					$level = $charInfo[$stat] + (($charInfo['rule3'] && $charInfo[$stat] > 3 && $stat != 'int')?1:0);
				
					if ($stat == 'dur' && $charInfo['rule2']) { $stonesSpent += $marvel_cost[$level] * 2; }
					elseif ($stat == 'dur' && !$charInfo['rule2']) { $stonesSpent += $marvel_cost[$level] * 3; }
					elseif ($stat == 'int' && $charInfo['rule1']) { $stonesSpent += $marvel_cost[$level] * 2; }
					else { $stonesSpent += $marvel_cost[$level]; }
				}
			}
			
			$updates['int'] = intval($_POST['int']);
			$updates['str'] = intval($_POST['str']);
			$updates['agi'] = intval($_POST['agi']);
			$updates['spd'] = intval($_POST['spd']);
			$updates['dur'] = intval($_POST['dur']);
			
			$updates['rule1'] = $_POST['rule1']?TRUE:FALSE;
			$updates['rule2'] = $_POST['rule2']?TRUE:FALSE;
			$updates['rule3'] = $_POST['rule3']?TRUE:FALSE;
			
			foreach (array('int', 'str', 'agi', 'spd', 'dur') as $stat) {
				$level = $updates[$stat] + (($updates['rule3'] && $updates[$stat] > 3 && $stat != 'int')?1:0);
				
				if ($stat == 'dur' && $updates['rule2']) { $stonesSpent -= formatStones($marvel_cost[$level] * 2); }
				elseif ($stat == 'dur' && !$updates['rule2']) { $stonesSpent -= formatStones($marvel_cost[$level] * 3); }
				elseif ($stat == 'int' && $updates['rule1']) { $stonesSpent -= formatStones($marvel_cost[$level] * 2); }
				else { $stonesSpent -= $marvel_cost[$level]; }
			}
			$updates['`int`'] = $updates['int'];
			unset($updates['int']);
			$updates['unusedStones'] = formatStones($charInfo['unusedStones'] + $stonesSpent);
			$updates['totalStones'] = formatStones($charInfo['totalStones'] - $stonesSpent);
			
			$_SESSION['stepDone']['basic'] = 1;
			$mysql->setUpdates($updates);
			$mysql->stdQuery('update', 'where');
			
			header('Location: '.SITEROOT.'/characters/marvel/new/guided?step=basic'); exit;
		}
		
		if ($_GET['step'] == 'actions') {
			$actionID = intval($_POST['actionID']);
			$mysql->setTable('marvel_actions AS actions');
			$mysql->setSelectCols('actions.actionID', 'actions.name', 'actions.cost', 'playerActions.playerActionID', 'playerActions.level', 'playerActions.offset', 'playerActions.stonesSpent', 'playerActions.details');
		$mysql->setJoins('left', 'marvel_playerActions AS playerActions ON actions.actionID = playerActions.actionID && playerActions.characterID = '.$characterID);
			$mysql->setWhere('actions.actionID = '.$actionID);
			$mysql->stdQuery('select', 'selectCols', 'join', 'where');
			$actionInfo = $mysql->fetch();
			
			$unusedStones = $charInfo['unusedStones'];
			$totalStones = $charInfo['totalStones'];
			
			if ($actionInfo['playerActionID']) {
				$mysql->setTable('marvel_characters');
				$unusedStones = formatStones($unusedStones + $actionInfo['stonesSpent']);
				$totalStones = formatStones($totalStones - $actionInfo['stonesSpent']);
				$mysql->setUpdates(array('unusedStones' => $unusedStones, 'totalStones' => $totalStones));
				$mysql->setWhere('characterID = '.$characterID);
				$mysql->stdQuery('update', 'where');
			}
			
			$mysql->setTable('marvel_playerActions');
			if (isset($_POST['deleteAction'])) {
				$mysql->setWhere('playerActionID = '.$actionInfo['playerActionID']);
				$mysql->stdQuery('delete', 'where');
			} else {
				$stonesSpent = ($actionInfo['cost'] >= 0)?$marvel_cost[$_POST['level'] + $actionInfo['cost'] + $_POST['offset']]:abs($actionInfo['cost']);
				if ($actionInfo['playerActionID']) {
					$updates = array('level' => intval($_POST['level']), 'offset' => intval($_POST['offset']), 'details' => sanitizeString($_POST['details']), 'stonesSpent' => formatStones($stonesSpent));
					
					$mysql->setWhere('playerActionID = '.$actionInfo['playerActionID']);
					$mysql->setUpdates($updates);
					$mysql->stdQuery('update', 'where');
				} else {
					$inserts = array('characterID' => $characterID, 'actionID' => $actionID, 'level' => intval($_POST['level']), 'offset' => intval($_POST['offset']), 'details' => sanitizeString($_POST['details']), 'stonesSpent' => formatStones($stonesSpent));
					
					$mysql->setInserts($inserts);
					$mysql->stdQuery('insert');
				}
				
				$mysql->setTable('marvel_characters');
				$unusedStones = formatStones($unusedStones - $stonesSpent);
				$totalStones = formatStones($totalStones + $stonesSpent);
				$mysql->setUpdates(array('unusedStones' => $unusedStones, 'totalStones' => $totalStones));
				$mysql->setWhere('characterID = '.$characterID);
				$mysql->stdQuery('update', 'where');
			}
			
			header('Location: '.SITEROOT.'/characters/marvel/new/guided?step=actions'); exit;
		}
		
		if ($_GET['step'] == 'modifiers') {
			$modifierID = intval($_POST['modifierID']);
			$mysql->setTable('marvel_modifiers AS modifiers');
			$mysql->setSelectCols('modifiers.modifierID', 'modifiers.name', 'modifiers.cost', 'modifiers.costTo', 'playerModifiers.playerModifierID', 'playerModifiers.level', 'playerModifiers.offset', 'playerModifiers.extraStones', 'playerModifiers.timesTaken', 'playerModifiers.details', 'playerModifiers.stonesSpent');
		$mysql->setJoins('left', 'marvel_playerModifiers AS playerModifiers ON modifiers.modifierID = playerModifiers.modifierID && playerModifiers.characterID = '.$characterID);
			$mysql->setWhere('modifiers.modifierID = '.$modifierID);
			$mysql->stdQuery('select', 'selectCols', 'join', 'where');
			$modifierInfo = $mysql->fetch();
			
			$unusedStones = $charInfo['unusedStones'];
			$totalStones = $charInfo['totalStones'];
			
			if ($modifierInfo['playerModifierID']) {
				$mysql->setTable('marvel_characters');
				$unusedStones = formatStones($unusedStones + $modifierInfo['stonesSpent']);
				$totalStones = formatStones($totalStones - $modifierInfo['stonesSpent']);
				$mysql->setUpdates(array('unusedStones' => $unusedStones, 'totalStones' => $totalStones));
				$mysql->setWhere('characterID = '.$characterID);
				$mysql->stdQuery('update', 'where');
			}
			
			$mysql->setTable('marvel_playerModifiers');
			if (isset($_POST['deleteModifier'])) {
				$mysql->setWhere('playerModifierID = '.$modifierInfo['playerModifierID']);
				$mysql->stdQuery('delete', 'where');
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
					
					$mysql->setWhere('playerModifierID = '.$modifierInfo['playerModifierID']);
					$mysql->setUpdates($updates);
					$mysql->stdQuery('update', 'where');
				} else {
					$inserts = array('characterID' => $characterID, 'modifierID' => $modifierInfo['modifierID'], 'level' => $level, 'offset' => $offset, 'extraStones' => $extraStones, 'timesTaken' => $timesTaken, 'details' => sanitizeString($_POST['details']), 'stonesSpent' => formatStones($stonesSpent));
					
					$mysql->setInserts($inserts);
					$mysql->stdQUery('insert');
				}
				
				$mysql->setTable('marvel_characters');
				$unusedStones = formatStones($unusedStones - $stonesSpent);
				$totalStones = formatStones($totalStones + $stonesSpent);
				$mysql->setUpdates(array('unusedStones' => $unusedStones, 'totalStones' => $totalStones));
				$mysql->setWhere('characterID = '.$characterID);
				$mysql->stdQuery('update', 'where');
			}
			
			header('Location: '.SITEROOT.'/characters/marvel/new/guided?step=modifiers'); exit;
		}
		
		if ($_GET['step'] == 'challenges') {
			$unusedStones = $charInfo['unusedStones'];
			foreach ($_POST as $key => $value) {
				$keyParts = explode('_', $key);
				$challengeID = intval($keyParts[1]);
				
				if (($value > 0 || $value == 'on') && $keyParts[0] == 'challengeID' && $challengeID && $keyParts[2] != 'added') {
					$stones = intval($_POST['challengeStones_'.$challengeID]);
					
					$mysql->setTable('marvel_playerChallenges');
					$mysql->setInserts(array('characterID' => $characterID, 'challengeID' => $challengeID, 'stones' => $stones));
					$mysql->stdQuery('insert');
					
					$mysql->setTable('marvel_characters');
					$unusedStones += $stones;
					$mysql->setUpdates(array('unusedStones' => $unusedStones));
					$mysql->setWhere('characterID = '.$characterID);
					$mysql->stdQuery('update', 'where');
				} elseif ($keyParts[0] == 'challengeStones' && $challengeID && $keyParts[2] == 'added') {
					$mysql->setTable('marvel_playerChallenges');
					$mysql->setWhere('characterID = '.$characterID.' && challengeID = '.$challengeID);
					$mysql->setSelectCols('stones');
					$mysql->stdQuery('select', 'selectCols', 'where');
					list($stones) = $mysql->getList();
					$mysql->stdQuery('delete', 'where');
					
					$mysql->setTable('marvel_characters');
					$unusedStones -= $stones;
					$mysql->setUpdates(array('unusedStones' => $unusedStones));
					$mysql->setWhere('characterID = '.$characterID);
					$mysql->stdQuery('update', 'where');
				}
			}
			$_SESSION['stepDone']['challenges'] = 1;
	
			header('Location: '.SITEROOT.'/characters/marvel/new/guided?step=challenges'); exit;
		}
	} elseif (isset($_POST['startOver'])) { 
		$mysql->setTable('marvel_characters');
		$mysql->setUpdates(array('normName' => '', 'superName' => '', '`int`' => 0, 'str' => 0, 'agi' => 0, 'spd' => 0, 'dur' => 0, 'unusedStones' => 0, 'totalStones' => 0, 'rule1' => 0, 'rule2' => 0));
		$mysql->setWhere('characterID = '.intval($_SESSION['characterID']));
		$mysql->stdQuery('update', 'where');

		$mysql->setTable('marvel_playerActions');
		$mysql->stdQuery('delete', 'where');

		$mysql->setTable('marvel_playerModifiers');
		$mysql->stdQuery('delete', 'where');

		$mysql->setTable('marvel_playerChallenges');
		$mysql->stdQuery('delete', 'where');

		unset($_SESSION['stepDone']);
		
		header('Location: '.SITEROOT.'/characters/marvel/new/guided');
	} else { header('Location: '.SITEROOT.'/403'); }
?>