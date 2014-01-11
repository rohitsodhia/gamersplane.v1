<?
	checkLogin();

	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$updates = array();
			$numVals = array('totalXP', 'spentXP', 'brawn', 'agility', 'intellect', 'cunning', 'willpower', 'presence', 'defense_melee', 'defense_ranged', 'soak', 'strain_max', 'strain_current', 'wounds_max', 'wounds_current');
			$textVals = array('name', 'species', 'career', 'specialization', 'items', 'motivations', 'obligations', 'notes');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";
			
			$updateSkill = $mysql->prepare("UPDATE sweote_skills SET rank = :rank, career = :career WHERE characterID = $characterID AND skillID = :skillID");
			if (sizeof($_POST['skills'])) { foreach ($_POST['skills'] as $skillID => $skillInfo) {
				$updateSkill->bindValue(':rank', intval($skillInfo['rank']));
				$updateSkill->bindValue(':career', isset($skillInfo['career'])?1:0);
				$updateSkill->bindValue(':skillID', $skillID);
				$updateSkill->execute();
			} }
			
			if (sizeof($_POST['weapons'])) {
				$count = 1;
				$mysql->query('DELETE FROM sweote_weapons WHERE characterID = '.$characterID);
				$addWeapon = $mysql->prepare("INSERT INTO sweote_weapons (characterID, weaponID, name, skill, damage, `range`, critical, notes) VALUES ($characterID, :weaponID, :name, :skill, :damage, :range, :critical, :notes)");
				foreach ($_POST['weapons'] as $weaponKey => $indivWeapon) {
					if (strlen($indivWeapon['name']) && strlen($indivWeapon['skill']) && strlen($indivWeapon['damage'])) {
						$addWeapon->bindValue(':weaponID', $count++);
						$addWeapon->bindValue(':name', $indivWeapon['name']);
						$addWeapon->bindValue(':skill', $indivWeapon['skill']);
						$addWeapon->bindValue(':damage', $indivWeapon['damage']);
						$addWeapon->bindValue(':range', $indivWeapon['range']);
						$addWeapon->bindValue(':critical', $indivWeapon['critical']);
						$addWeapon->bindValue(':notes', $indivWeapon['notes']);
						$addWeapon->execute();
					}
				}
			}
			
			$updateChar = $mysql->prepare('UPDATE sweote_characters SET '.implode($updates, ', ')." WHERE characterID = $characterID");
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
			$updateChar->execute();
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
		}
		
		header('Location: '.SITEROOT.'/characters/sweote/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>