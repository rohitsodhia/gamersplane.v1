<?
	checkLogin();
	
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charInfo = $mysql->query('SELECT * FROM marvel_characters WHERE characterID = '.$characterID);
		$charInfo = $charInfo->fetch();
		
		$stonesSpent = 0;
		
		$updates['int'] = intval($_POST['int']);
		$updates['str'] = intval($_POST['str']);
		$updates['agi'] = intval($_POST['agi']);
		$updates['spd'] = intval($_POST['spd']);
		$updates['dur'] = intval($_POST['dur']);
		$updates['rule1'] = isset($_POST['rule1'])?1:0;
		$updates['rule2'] = isset($_POST['rule2'])?1:0;
		$updates['rule3'] = isset($_POST['rule3'])?1:0;
		
		foreach (array('int', 'str', 'agi', 'spd', 'dur') as $stat) {
			$level = $charInfo[$stat] + (($charInfo['rule3'] && $charInfo[$stat] > 3 && $stat != 'int')?1:0);
			
			if ($stat == 'dur' && $charInfo['rule2']) $stonesSpent += $marvel_cost[$level] * 2;
			elseif ($stat == 'dur' && !$charInfo['rule2']) $stonesSpent += $marvel_cost[$level] * 3;
			elseif ($stat == 'int' && $charInfo['rule1']) $stonesSpent += $marvel_cost[$level] * 2;
			else $stonesSpent += $marvel_cost[$level];
		}
		
		foreach (array('int', 'str', 'agi', 'spd', 'dur') as $stat) {
			$level = $updates[$stat] + (($updates['rule3'] && $updates[$stat] > 3 && $stat != 'int')?1:0);
			
			if ($stat == 'dur' && $updates['rule2']) $stonesSpent -= formatStones($marvel_cost[$level] * 2);
			elseif ($stat == 'dur' && !$updates['rule2']) $stonesSpent -= formatStones($marvel_cost[$level] * 3);
			elseif ($stat == 'int' && $updates['rule1']) $stonesSpent -= formatStones($marvel_cost[$level] * 2);
			else $stonesSpent -= $marvel_cost[$level];
		}
		$updates['`int`'] = $updates['int'];
		unset($updates['int']);
		
		if (isset($_POST['alterStones'])) $updates['unusedStones'] = formatStones($charInfo['unusedStones'] + $stonesSpent);
		$updates['totalStones'] = formatStones($charInfo['totalStones'] - $stonesSpent);
		
		$mysql->query('UPDATE marvel_characters SET '.setupUpdates($updates).' WHERE characterID = '.$characterID);
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");;
		
		header('Location: '.SITEROOT.'/characters/marvel/sheet/'.$characterID);
	} else { header('Location: '.SITEROOT.'/403'); }
?>