<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$updates = array();
		$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_magic', 'fort_race', 'fort_misc', 'ref_base', 'ref_magic', 'ref_race', 'ref_misc', 'will_base', 'will_magic', 'will_race', 'will_misc', 'hp', 'ac_armor', 'ac_shield', 'ac_dex', 'ac_class', 'ac_natural', 'ac_deflection', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc');
		$textVals = array('name', 'race', 'class', 'dr', 'skills', 'feats', 'weapons', 'armor', 'items', 'spells', 'notes');
		foreach ($_POST as $key => $value) {
			if ($key == 'alignment') $updates['pathfinder_characters`.`alignment'] = in_array($value, array('lg', 'ng', 'cg', 'ln', 'tn', 'cn', 'le', 'ne', 'ce'))?$value:'tn';
			elseif (in_array($key, $textVals)) $updates['pathfinder_characters`.`'.$key] = sanitizeString($value);
			elseif (in_array($key, $numVals)) $updates['pathfinder_characters`.`'.$key] = intval($value);
		}
		
		$mysql->query('UPDATE pathfinder_characters, characters SET '.setupUpdates($updates).' WHERE pathfinder_characters.characterID = '.$characterID.' AND characters.characterID = pathfinder_characters.characterID AND characters.characterID = '.$characterID);
		header('Location: '.SITEROOT.'/characters/pathfinder/sheet/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>