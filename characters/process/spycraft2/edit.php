<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$updates = array();
			$numVals = array('str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_misc', 'ref_base', 'ref_misc', 'will_base', 'will_misc', 'vitality', 'wounds', 'subdual', 'stress', 'ac_class', 'ac_armor', 'ac_dex', 'ac_misc', 'initiative_class', 'initiative_misc', 'bab', 'unarmed_misc', 'melee_misc', 'ranged_misc', 'actionDie_total', 'knowledge_misc', 'request_misc', 'gear_misc');
			$textVals = array('name', 'codename', 'class', 'talent', 'specialty', 'actionDie_dieType', 'items', 'notes');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";

			$updateSkill = $mysql->prepare("UPDATE spycraft2_skills SET ranks = :ranks, misc = :misc, error = :error, threat = :threat WHERE characterID = :characterID AND skillID = :skillID");
			if (sizeof($_POST['skills'])) { foreach ($_POST['skills'] as $skillID => $skillInfo) {
				$updateSkill->bindValue(':ranks', intval($skillInfo['ranks']));
				$updateSkill->bindValue(':misc', intval($skillInfo['misc']));
				$updateSkill->bindValue(':error', sanitizeString($skillInfo['error']));
				$updateSkill->bindValue(':threat', sanitizeString($skillInfo['threat']));
				$updateSkill->bindValue(':characterID', $characterID);
				$updateSkill->bindValue(':skillID', $skillID);
				$updateSkill->execute();
			} }
			
			if (sizeof($_POST['focus_forte'])) {
				$fortes = array();
				foreach ($_POST['focus_forte'] as $focusID => $forte) $fortes[] = intval($focusID);
				$fortes = array_unique($fortes);
				$mysql->query("UPDATE spycraft2_focuses SET forte = 1 WHERE characterID = characterID AND focusID IN (".implode(', ', $fortes).")");
				$mysql->query("UPDATE spycraft2_focuses SET forte = 0 WHERE characterID = characterID AND focusID NOT IN (".implode(', ', $fortes).")");
			}
			
			if (sizeof($_POST['weapons'])) {
				$count = 1;
				$mysql->query('DELETE FROM spycraft2_weapons WHERE characterID = '.$characterID);
				$addWeapon = $mysql->prepare("INSERT INTO spycraft2_weapons (characterID, weaponID, name, ab, damage, recoil, et, `range`, type, size, notes) VALUES (:characterID, :weaponID, :name, :ab, :damage, :recoil, :et, :range, :type, :size, :notes)");
				foreach ($_POST['weapons'] as $weaponKey => $indivWeapon) {
					if (strlen($indivWeapon['name']) && strlen($indivWeapon['ab']) && strlen($indivWeapon['damage'])) {
						$addWeapon->bindValue(':characterID', $characterID);
						$addWeapon->bindValue(':weaponID', $count++);
						$addWeapon->bindValue(':name', $indivWeapon['name']);
						$addWeapon->bindValue(':ab', $indivWeapon['ab']);
						$addWeapon->bindValue(':damage', $indivWeapon['damage']);
						$addWeapon->bindValue(':recoil', $indivWeapon['recoil']);
						$addWeapon->bindValue(':et', $indivWeapon['et']);
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
				$mysql->query('DELETE FROM spycraft2_armors WHERE characterID = '.$characterID);
				$addArmor = $mysql->prepare("INSERT INTO spycraft2_armors (characterID, armorID, name, reduction, resist, penalty, `check`, speed, dc, notes) VALUES (:characterID, :armorID, :name, :reduction, :resist, :penalty, :check, :speed, :dc, :notes)");
				foreach ($_POST['armors'] as $armorKey => $indivArmor) {
					if (strlen($indivArmor['name']) && strlen($indivArmor['reduction'])) {
						$addArmor->bindValue(':characterID', $characterID);
						$addArmor->bindValue(':armorID', $count++);
						$addArmor->bindValue(':name', $indivArmor['name']);
						$addArmor->bindValue(':reduction', $indivArmor['reduction']);
						$addArmor->bindValue(':resist', $indivArmor['resist']);
						$addArmor->bindValue(':penalty', $indivArmor['penalty']);
						$addArmor->bindValue(':check', $indivArmor['check']);
						$addArmor->bindValue(':speed', $indivArmor['speed']);
						$addArmor->bindValue(':dc', $indivArmor['dc']);
						$addArmor->bindValue(':notes', $indivArmor['notes']);
						$addArmor->execute();

					}
				}
			}
			
			$updateChar = $mysql->prepare('UPDATE spycraft2_characters SET '.implode($updates, ', ').' WHERE characterID = :characterID');
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
 			$updateChar->bindValue(':characterID', $characterID);
			$updateChar->execute();
			addCharacterHistory($characterID, 'editedChar');
		}
		
		header('Location: '.SITEROOT.'/characters/spycraft2/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>