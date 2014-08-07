<?
	$pathAction = '';
	require_once('../includes/requires.php');

	$skills = array('Boating', 'Driving', 'Fighting', 'Lockpicking', 'Piloting', 'Riding', 'Shooting', 'Swimming', 'Stealth', 'Throwing', 'Gambling', 'Healing', 'Investigation', 'Notice', 'Repair', 'Streetwise', 'Survival', 'Taunt', 'Tracking', 'Guts', 'Intimidation', 'Persuasion', 'Climbing');
	foreach ($skills as $skill) {
		$skillSearch = sanitizeString($skill, 'search_format');
		$skillID = $mysql->query("SELECT skillID FROM skillsList WHERE name = '{$skill}'");
		if ($skillID->rowCount()) $skillID = $skillID->fetchColumn();
		else { try {
			$mysql->query("INSERT INTO skillsList SET name = '{$skill}', searchName = '{$skillSearch}'");
			$skillID = $mysql->lastInsertId();
		} catch (Exception $e) {} }
		try { $mysql->query("INSERT INTO system_skill_map SET systemID = 15, skillID = {$skillID}"); } catch (Exception $e) {}
	}
	$mysql->query("UPDATE systems SET enabled = 1");
?>
