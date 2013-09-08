<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$updates = array();
			$newSkill = array();
			$skill = array();
			$weapon = array();
			$armor = array();
			$numVals = array('characterID', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_misc', 'ref_base', 'ref_misc', 'will_base', 'will_misc', 'vitality', 'wounds', 'subdual', 'stress', 'ac_class', 'ac_armor', 'ac_dex', 'ac_misc', 'initiative_class', 'initiative_misc', 'bab', 'unarmed_misc', 'melee_misc', 'ranged_misc', 'actionDie_total', 'knowledge_misc', 'request_misc', 'gear_misc');
			$textVals = array('name', 'codename', 'class', 'talent', 'specialty', 'actionDie_dieType', 'items', 'notes');
			foreach ($_POST as $key => $value) {
				if (in_array($key, $textVals)) $updates['spycraft2_characters`.`'.$key] = sanatizeString($value);
				elseif (in_array($key, $numVals)) $updates['spycraft2_characters`.`'.$key] = intval($value);
			}
						
			if (sizeof($_POST['skills'])) { foreach ($_POST['skills'] as $skillID => $skillInfo) {
				$ranks = intval($skillInfo['ranks']);
				$misc = intval($skillInfo['misc']);
				$error = sanatizeString($skillInfo['error']);
				$threat = sanatizeString($skillInfo['threat']);
				$mysql->query("UPDATE spycraft2_skills SET ranks = $ranks, misc = $misc, error = '$error', threat = '$threat' WHERE characterID = characterID AND skillID = $skillID");
			} }
			
			if (sizeof($_POST['focusForte'])) {
				$fortes = array();
				foreach ($_POST['focusForte'] as $focusID => $forte) $fortes[] = $focusID;
				$mysql->query("UPDATE spycraft2_focuses SET forte = 1 WHERE characterID = characterID AND focusID IN (".implode(', ', $fortes).")");
				$mysql->query("UPDATE spycraft2_focuses SET forte = 0 WHERE characterID = characterID AND focusID NOT IN (".implode(', ', $fortes).")");
			}
			
			if (sizeof($_POST['weapons'])) { foreach ($_POST['weapons'] as $weaponKey => $indivWeapon) {
				foreach ($indivWeapon as $key => $value) $indivWeapon[$key] = sanatizeString($value);
				if (strlen($indivWeapon['name']) && strlen($indivWeapon['ab']) && strlen($indivWeapon['damage'])) {
					if (substr($weaponKey, 0, 4) == 'new_') $mysql->query("INSERT INTO spycraft2_weapons (characterID, name, ab, damage, recoil, et, `range`, type, size, notes) VALUES ($characterID, '{$indivWeapon['name']}', '{$indivWeapon['ab']}', '{$indivWeapon['damage']}', '{$indivWeapon['recoil']}', '{$indivWeapon['et']}', '{$indivWeapon['range']}', '{$indivWeapon['type']}', '{$indivWeapon['size']}', '{$indivWeapon['notes']}')");
					else $mysql->query("UPDATE spycraft2_weapons SET name = '{$indivWeapon['name']}', ab = '{$indivWeapon['ab']}', damage = '{$indivWeapon['damage']}', recoil = '{$indivWeapon['recoil']}', et = '{$indivWeapon['et']}', `range` = '{$indivWeapon['range']}', type = '{$indivWeapon['type']}', size = '{$indivWeapon['size']}', notes = '{$indivWeapon['notes']}' WHERE weaponID = ".intval($weaponKey));
				}
			} }
			
			if (sizeof($_POST['armors'])) { foreach ($_POST['armors'] as $armorKey => $indivArmor) {
				foreach ($indivArmor as $key => $value) $indivArmor[$key] = sanatizeString($value);
				if (strlen($indivArmor['name']) && strlen($indivArmor['reduction']) && strlen($indivArmor['resist'])) {
					if (substr($armorKey, 0, 4) == 'new_') $mysql->query("INSERT INTO spycraft2_armors (characterID, name, reduction, resist, penalty, `check`, speed, dc, notes) VALUES ($characterID, '{$indivArmor['name']}', '{$indivArmor['reduction']}', '{$indivArmor['resist']}', '{$indivArmor['penalty']}', '{$indivArmor['check']}', '{$indivArmor['speed']}', '{$indivArmor['dc']}', '{$indivArmor['notes']}')");
					else $mysql->query("UPDATE spycraft2_armors SET name = '{$indivArmor['name']}', reduction = '{$indivArmor['reduction']}', resist = '{$indivArmor['resist']}', penalty = {$indivArmor['penalty']}, `check` = '{$indivArmor['check']}', speed = '{$indivArmor['speed']}', dc = '{$indivArmor['dc']}', notes = '{$indivArmor['notes']}' WHERE armorID = ".intval($armorKey));
				}
			} }
			
			$mysql->query('UPDATE spycraft2_characters, characters SET '.setupUpdates($updates).' WHERE spycraft2_characters.characterID = '.$characterID.' AND characters.characterID = spycraft2_characters.characterID AND characters.characterID = '.$characterID);
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
		}
		
		header('Location: '.SITEROOT.'/characters/spycraft2/sheet/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>