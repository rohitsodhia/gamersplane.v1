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
			$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_misc', 'ref_base', 'ref_misc', 'will_base', 'will_misc', 'vitality', 'wounds', 'speed', 'ac_armor', 'ac_dex', 'ac_size', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc', 'actionDie_total', 'inspiration_misc', 'education_misc');
			$textVals = array('name', 'codename', 'class', 'department', 'actionDie_dieType', 'items', 'notes');
			foreach ($_POST as $key => $value) {
				if (in_array($key, $textVals)) $updates['spycraft_characters`.`'.$key] = sanatizeString($value);
				elseif (in_array($key, $numVals)) $updates['spycraft_characters`.`'.$key] = intval($value);
			}
						
			if (sizeof($_POST['skills'])) { foreach ($_POST['skills'] as $skillID => $skillInfo) {
				$ranks = intval($skillInfo['ranks']);
				$misc = intval($skillInfo['misc']);
				$error = sanatizeString($skillInfo['error']);
				$threat = sanatizeString($skillInfo['threat']);
				$mysql->query("UPDATE spycraft_skills SET ranks = $ranks, misc = $misc, error = '$error', threat = 'threat' WHERE characterID = characterID AND skillID = $skillID");
			} }
			
			if (sizeof($_POST['weapons'])) { foreach ($_POST['weapons'] as $weaponKey => $indivWeapon) {
				foreach ($indivWeapon as $key => $value) $indivWeapon[$key] = sanatizeString($value);
				if (strlen($indivWeapon['name']) && strlen($indivWeapon['ab']) && strlen($indivWeapon['damage'])) {
					if (substr($weaponKey, 0, 4) == 'new_') $mysql->query("INSERT INTO spycraft_weapons (characterID, name, ab, damage, error, threat, `range`, type, size, notes) VALUES ($characterID, '{$indivWeapon['name']}', '{$indivWeapon['ab']}', '{$indivWeapon['damage']}', '{$indivWeapon['error']}', '{$indivWeapon['threat']}', '{$indivWeapon['range']}', '{$indivWeapon['type']}', '{$indivWeapon['size']}', '{$indivWeapon['notes']}')");
					else $mysql->query("UPDATE spycraft_weapons SET name = '{$indivWeapon['name']}', ab = '{$indivWeapon['ab']}', damage = '{$indivWeapon['damage']}', error = '{$indivWeapon['error']}', threat = '{$indivWeapon['threat']}', `range` = '{$indivWeapon['range']}', type = '{$indivWeapon['type']}', size = '{$indivWeapon['size']}', notes = '{$indivWeapon['notes']}' WHERE weaponID = ".intval($weaponKey));
				}
			} }
			
			if (sizeof($_POST['armors'])) { foreach ($_POST['armors'] as $armorKey => $indivArmor) {
				foreach ($indivArmor as $key => $value) $indivArmor[$key] = sanatizeString($value);
				if (strlen($indivArmor['name']) && strlen($indivArmor['def']) && strlen($indivArmor['maxDex'])) {
					if (substr($armorKey, 0, 4) == 'new_') $mysql->query("INSERT INTO spycraft_armors (characterID, name, def, resist, `check`, type, maxDex, speed, notes) VALUES ($characterID, '{$indivArmor['name']}', '{$indivArmor['def']}', '{$indivArmor['resist']}', '{$indivArmor['check']}', '{$indivArmor['type']}', '{$indivArmor['maxDex']}', '{$indivArmor['speed']}', '{$indivArmor['notes']}')");
					else $mysql->query("UPDATE spycraft_armors SET name = '{$indivArmor['name']}', def = '{$indivArmor['def']}', resist = '{$indivArmor['resist']}', `check` = '{$indivArmor['check']}', type = '{$indivArmor['type']}', maxDex = '{$indivArmor['maxDex']}', speed = '{$indivArmor['speed']}', notes = '{$indivArmor['notes']}' WHERE armorID = ".intval($armorKey));
				}
			} }
			
			$mysql->query('UPDATE spycraft_characters, characters SET '.setupUpdates($updates).' WHERE spycraft_characters.characterID = '.$characterID.' AND characters.characterID = spycraft_characters.characterID AND characters.characterID = '.$characterID);
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
		}
		
		header('Location: '.SITEROOT.'/characters/spycraft/sheet/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>