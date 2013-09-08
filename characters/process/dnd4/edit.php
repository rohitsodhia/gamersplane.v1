<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck= $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		$updates = array();
		if ($charCheck->rowCount()) {
			$numVals = array('str', 'con', 'dex', 'int', 'wis', 'cha', 'ac_armor', 'ac_class', 'ac_feats', 'ac_enh', 'ac_misc', 'fort_class', 'fort_feats', 'fort_enh', 'fort_misc', 'ref_class', 'ref_feats', 'ref_enh', 'ref_misc', 'will_class', 'will_feats', 'will_enh', 'will_misc', 'init_misc', 'hp', 'surges', 'speed_base', 'speed_armor', 'speed_item', 'speed_misc', 'ap', 'piSkill', 'ppSkill');
			$textVals = array('name', 'race', 'alignment', 'class', 'paragon', 'epic', 'weapons', 'armor', 'items', 'notes');
			foreach ($_POST as $key => $value) {
				if ($key == 'alignment') $updates['dnd4_characters`.`alignment'] = in_array($value, array('g', 'lg', 'e', 'ce', 'u'))?$value:'u';
				elseif (in_array($key, $textVals)) $updates['dnd4_characters`.`'.$key] = sanatizeString($value);
				elseif (in_array($key, $numVals)) $updates['dnd4_characters`.`'.$key] = intval($value);
			}
			
			if (sizeof($_POST['attacks'])) { foreach ($_POST['attacks'] as $attackID => $attackInfo) {
				foreach ($attackInfo as $key => $value) {
					if ($key == 'ability') $attackInfo[$key] = sanatizeString($value);
					else $attackInfo[$key] = intval($value);
				}
				if (substr($attackID, 0, 3) == 'new' && strlen($attackInfo['ability'])) $mysql->query("INSERT INTO dnd4_attacks SET characterID = $characterID, ability = '{$attackInfo['ability']}', stat = {$attackInfo['stat']}, class = {$attackInfo['class']}, prof = {$attackInfo['prof']}, feat = {$attackInfo['feat']}, enh = {$attackInfo['enh']}, misc = {$attackInfo['misc']}");
				else $mysql->query("UPDATE dnd4_attacks SET ability = '{$attackInfo['ability']}', stat = {$attackInfo['stat']}, class = {$attackInfo['class']}, prof = {$attackInfo['prof']}, feat = {$attackInfo['feat']}, enh = {$attackInfo['enh']}, misc = {$attackInfo['misc']} WHERE characterID = $characterID AND attackID = ".intval($attackID));
			} }
			
			if (sizeof($_POST['skills'])) { foreach ($_POST['skills'] as $skillID => $skillInfo) {
				$ranks = intval($skillInfo['ranks']);
				$misc = intval($skillInfo['misc']);
				$mysql->query("UPDATE dnd4_skills SET ranks = $ranks, misc = $misc WHERE characterID = characterID AND skillID = $skillID");
			} }
			
			$mysql->query('UPDATE dnd4_characters, characters SET '.setupUpdates($updates).' WHERE dnd4_characters.characterID = '.$characterID.' AND characters.characterID = dnd4_characters.characterID AND characters.characterID = '.$characterID);
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
		}
		
		header('Location: '.SITEROOT.'/characters/dnd4/sheet/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>