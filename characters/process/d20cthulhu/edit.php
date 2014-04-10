<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$updates = array();
			$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_magic', 'fort_race', 'fort_misc', 'ref_base', 'ref_magic', 'ref_race', 'ref_misc', 'will_base', 'will_magic', 'will_race', 'will_misc', 'hp', 'ac_armor', 'ac_shield', 'ac_dex', 'ac_class', 'ac_natural', 'ac_deflection', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc');
			$textVals = array('name', 'race', 'class', 'dr', 'items', 'spells', 'notes');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";
			$alignment = in_array($value, array('lg', 'ng', 'cg', 'ln', 'tn', 'cn', 'le', 'ne', 'ce'))?$value:'tn';
			$updates[] = "`alignment` = '$alignment'";
			
			$updateSkill = $mysql->prepare("UPDATE dnd3_skills SET ranks = :ranks, misc = :misc WHERE characterID = :characterID AND skillID = :skillID");
			if (sizeof($_POST['skills'])) { foreach ($_POST['skills'] as $skillID => $skillInfo) {
				$updateSkill->bindValue(':ranks', intval($skillInfo['ranks']));
				$updateSkill->bindValue(':misc', intval($skillInfo['misc']));
				$updateSkill->bindValue(':characterID', $characterID);
				$updateSkill->bindValue(':skillID', $skillID);
				$updateSkill->execute();
			} }
			
			if (sizeof($_POST['weapons'])) {
				$count = 1;
				$mysql->query('DELETE FROM dnd3_weapons WHERE characterID = '.$characterID);
				$addWeapon = $mysql->prepare("INSERT INTO dnd3_weapons (characterID, weaponID, name, ab, damage, critical, `range`, type, size, notes) VALUES (:characterID, :weaponID, :name, :ab, :damage, :critical, :range, :type, :size, :notes)");
				foreach ($_POST['weapons'] as $weaponKey => $indivWeapon) {
					if (strlen($indivWeapon['name']) && strlen($indivWeapon['ab']) && strlen($indivWeapon['damage'])) {
						$addWeapon->bindValue(':characterID', $characterID);
						$addWeapon->bindValue(':weaponID', $count++);
						$addWeapon->bindValue(':name', $indivWeapon['name']);
						$addWeapon->bindValue(':ab', $indivWeapon['ab']);
						$addWeapon->bindValue(':damage', $indivWeapon['damage']);
						$addWeapon->bindValue(':critical', $indivWeapon['critical']);
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
				$mysql->query('DELETE FROM dnd3_armors WHERE characterID = '.$characterID);
				$addArmor = $mysql->prepare("INSERT INTO dnd3_armors (characterID, armorID, name, ac, maxDex, type, `check`, spellFailure, speed, notes) VALUES (:characterID, :armorID, :name, :ac, :maxDex, :type, :check, :spellFailure, :speed, :notes)");
				foreach ($_POST['armors'] as $armorKey => $indivArmor) {
					if (strlen($indivArmor['name']) && strlen($indivArmor['ac']) && strlen($indivArmor['maxDex'])) {
						$addArmor->bindValue(':characterID', $characterID);
						$addArmor->bindValue(':armorID', $count++);
						$addArmor->bindValue(':name', $indivArmor['name']);
						$addArmor->bindValue(':ac', $indivArmor['ac']);
						$addArmor->bindValue(':maxDex', $indivArmor['maxDex']);
						$addArmor->bindValue(':type', $indivArmor['type']);
						$addArmor->bindValue(':check', $indivArmor['check']);
						$addArmor->bindValue(':spellFailure', $indivArmor['spellFailure']);
						$addArmor->bindValue(':speed', $indivArmor['speed']);
						$addArmor->bindValue(':notes', $indivArmor['notes']);
						$addArmor->execute();
					}
				}
			}
			
			$updateChar = $mysql->prepare('UPDATE dnd3_characters SET '.implode($updates, ', ')." WHERE characterID = $characterID");
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
			$updateChar->execute();
			addCharacterHistory($characterID, 'editedChar');
		}
		
		header('Location: '.SITEROOT.'/characters/dnd3/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>