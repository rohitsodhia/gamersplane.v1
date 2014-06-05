<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$updates = array();
			$numVals = array('str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_misc', 'ref_base', 'ref_misc', 'will_base', 'will_misc', 'vitality', 'wounds', 'speed', 'ac_armor', 'ac_dex', 'ac_size', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc', 'actionDie_total', 'inspiration_misc', 'education_misc');
			$textVals = array('name', 'codename', 'class', 'department', 'actionDie_dieType', 'items', 'notes');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";
						
			$updateSkill = $mysql->prepare("UPDATE spycraft_skills SET ranks = :ranks, misc = :misc, error = :error, threat = :threat WHERE characterID = :characterID AND skillID = :skillID");
			if (sizeof($_POST['skills'])) { foreach ($_POST['skills'] as $skillID => $skillInfo) {
				$updateSkill->bindValue(':ranks', intval($skillInfo['ranks']));
				$updateSkill->bindValue(':misc', intval($skillInfo['misc']));
				$updateSkill->bindValue(':error', sanitizeString($skillInfo['error']));
				$updateSkill->bindValue(':threat', sanitizeString($skillInfo['threat']));
				$updateSkill->bindValue(':characterID', $characterID);
				$updateSkill->bindValue(':skillID', $skillID);
				$updateSkill->execute();
			} }
			
			if (sizeof($_POST['weapons'])) {
				$count = 1;
				$mysql->query('DELETE FROM spycraft_weapons WHERE characterID = '.$characterID);
				$addWeapon = $mysql->prepare("INSERT INTO spycraft_weapons (characterID, weaponID, name, ab, damage, error, threat, `range`, type, size, notes) VALUES (:characterID, :weaponID, :name, :ab, :damage, :error, :threat, :range, :type, :size, :notes)");
				foreach ($_POST['weapons'] as $weaponKey => $indivWeapon) {
					if (strlen($indivWeapon['name']) && strlen($indivWeapon['ab']) && strlen($indivWeapon['damage'])) {
						$addWeapon->bindValue(':characterID', $characterID);
						$addWeapon->bindValue(':weaponID', $count++);
						$addWeapon->bindValue(':name', $indivWeapon['name']);
						$addWeapon->bindValue(':ab', $indivWeapon['ab']);
						$addWeapon->bindValue(':damage', $indivWeapon['damage']);
						$addWeapon->bindValue(':error', $indivWeapon['error']);
						$addWeapon->bindValue(':threat', $indivWeapon['threat']);
						$addWeapon->bindValue(':range', $indivWeapon['range']);
						$addWeapon->bindValue(':type', $indivWeapon['type']);
						$addWeapon->bindValue(':size', $indivWeapon['size']);
						$addWeapon->bindValue(':notes', $indivWeapon['notes']);
						$addWeapon->execute();
					}
				}
			}
			
			if (sizeof($_POST['armors'])) {
				$count = 1;
				$mysql->query('DELETE FROM spycraft_armors WHERE characterID = '.$characterID);
				$addArmor = $mysql->prepare("INSERT INTO spycraft_armors (characterID, armorID, name, def, resist, `check`, type, maxDex, speed, notes) VALUES (:characterID, :armorID, :name, :def, :resist, :check, :type, :maxDex, :speed, :notes)");
				foreach ($_POST['armors'] as $armorKey => $indivArmor) {
					if (strlen($indivArmor['name']) && strlen($indivArmor['def'])) {
						$addArmor->bindValue(':characterID', $characterID);
						$addArmor->bindValue(':armorID', $count++);
						$addArmor->bindValue(':name', $indivArmor['name']);
						$addArmor->bindValue(':def', $indivArmor['def']);
						$addArmor->bindValue(':resist', $indivArmor['resist']);
						$addArmor->bindValue(':check', $indivArmor['check']);
						$addArmor->bindValue(':type', $indivArmor['type']);
						$addArmor->bindValue(':maxDex', $indivArmor['maxDex']);
						$addArmor->bindValue(':speed', $indivArmor['speed']);
						$addArmor->bindValue(':notes', $indivArmor['notes']);
						$addArmor->execute();

					}
				}
			}
			
			$updateChar = $mysql->prepare('UPDATE spycraft_characters SET '.implode($updates, ', ').' WHERE characterID = :characterID');
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
 			$updateChar->bindValue(':characterID', $characterID);
			$updateChar->execute();
			addCharacterHistory($characterID, 'editedChar');
		}
		
		header('Location: /characters/spycraft/'.$characterID);
	} else header('Location: /403');
?>