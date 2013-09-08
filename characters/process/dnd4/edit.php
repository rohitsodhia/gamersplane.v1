<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		$updates = array();
		if ($charCheck->rowCount()) {
			$numVals = array('str', 'con', 'dex', 'int', 'wis', 'cha', 'ac_armor', 'ac_class', 'ac_feats', 'ac_enh', 'ac_misc', 'fort_class', 'fort_feats', 'fort_enh', 'fort_misc', 'ref_class', 'ref_feats', 'ref_enh', 'ref_misc', 'will_class', 'will_feats', 'will_enh', 'will_misc', 'init_misc', 'hp', 'surges', 'speed_base', 'speed_armor', 'speed_item', 'speed_misc', 'ap', 'piSkill', 'ppSkill');
			$textVals = array('name', 'race', 'alignment', 'class', 'paragon', 'epic', 'weapons', 'armor', 'items', 'notes');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";
			$alignment = in_array($value, array('g', 'lg', 'e', 'ce', 'u'))?$value:'u';
			$updates[] = "`alignment` = '$alignment'";
			
			if (sizeof($_POST['attacks'])) {
				$count = 1;
				$mysql->query('DELETE FROM dnd4_attacks WHERE characterID = '.$characterID);
				$addAttack = $mysql->prepare("INSERT INTO dnd4_attacks SET characterID = :characterID, attackID = :attackID, ability = :ability, stat = :stat, class = :class, prof = :prof, feat = :feat, enh = :enh, misc = :misc");
				foreach ($_POST['attacks'] as $attackID => $attackInfo) {
					if (strlen($attackInfo['ability'])) {
						$addAttack->bindValue(':characterID', $characterID);
						$addAttack->bindValue(':attackID', $count++);
						$addAttack->bindValue(':ability', $attackInfo['ability']);
						$addAttack->bindValue(':stat', $attackInfo['stat']);
						$addAttack->bindValue(':class', $attackInfo['class']);
						$addAttack->bindValue(':prof', $attackInfo['prof']);
						$addAttack->bindValue(':feat', $attackInfo['feat']);
						$addAttack->bindValue(':enh', $attackInfo['enh']);
						$addAttack->bindValue(':misc', $attackInfo['misc']);
						$addAttack->execute();
					}
				}
			}
			
			$updateSkill = $mysql->prepare("UPDATE dnd4_skills SET ranks = :ranks, misc = :misc WHERE characterID = :characterID AND skillID = :skillID");
			if (sizeof($_POST['skills'])) { foreach ($_POST['skills'] as $skillID => $skillInfo) {
				$updateSkill->bindValue('ranks', intval($skillInfo['ranks']));
				$updateSkill->bindValue(':misc', intval($skillInfo['misc']));
				$updateSkill->bindValue(':characterID', $characterID);
				$updateSkill->bindValue(':skillID', $skillID);
				$updateSkill->execute();
			} }
			
			$updateChar = $mysql->prepare('UPDATE dnd4_characters SET '.implode($updates, ', ').' WHERE characterID = :characterID');
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
 			$updateChar->bindValue(':characterID', $characterID);
			$updateChar->execute();
			updateCharacterHistory($characterID, 'editedChar');
		}
		
		header('Location: '.SITEROOT.'/characters/dnd4/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>