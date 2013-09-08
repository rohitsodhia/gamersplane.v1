<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$name = sanatizeString(preg_replace('/\s+/', ' ', trim(strtolower($_POST['name']))));
			$skill = $mysql->query('SELECT skillID FROM skillsList WHERE name = "'.$name.'"');
			$stat_1 = sanatizeString($_POST['stat_1']);
			$stat_2 = sanatizeString($_POST['stat_2']);
			$statBonus_1 = intval($_POST['statBonus_1']);
			$statBonus_2 = intval($_POST['statBonus_2']);
			if ($skill->rowCount()) $skillID = $skill->fetchColumn();
			else {
				$mysql->query('INSERT INTO skillsList (name, userDefined) VALUES ("'.$name.'", '.intval($_SESSION['userID']).')');
				$skillID = $mysql->lastInsertId();
			}
			$addSkill = $mysql->query("INSERT INTO spycraft2_skills (characterID, skillID, stat_1, stat_2) VALUES ($characterID, $skillID, '$stat_1', '$stat_2')");
			if ($addSkill->getResult()) {
				echo "\t\t\t\t\t<div id=\"skill_{$skillID}\" class=\"skill tr clearfix\">\n";
				echo "\t\t\t\t\t\t<span class=\"skill_name textLabel medText\">".mb_convert_case($name, MB_CASE_TITLE)."</span>\n";
				echo "\t\t\t\t\t\t<span class=\"skill_total textLabel lrBuffer addStat_{$stat_1}".(strlen($stat_2)?" addStat_{$stat_2}":'')." shortNum\">".showSign($statBonus_1).(strlen($stat_2) ? '/'.showSign($statBonus_2) : '')."</span>\n";
				echo "\t\t\t\t\t\t<span class=\"skill_stat textLabel lrBuffer alignCenter shortText\">".ucwords($stat_1).(strlen($stat_2) ? '/'.$stat_2 : '')."</span>\n";
				echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[$skillID}][ranks]\" value=\"0\" class=\"skill_ranks shortNum lrBuffer\">\n";
				echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillID}][misc]\" value=\"0\" class=\"skill_misc shortNum lrBuffer\">\n";
				echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{skillID}][error]\" value=\"\" class=\"skill_error medNum lrBuffer\">\n";
				echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillID}][threat]\" value=\"\" class=\"skill_threat medNum lrBuffer\">\n";
				echo "\t\t\t\t\t\t<input type=\"image\" name=\"skill{$skillID}_remove\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$skillID}\" class=\"skill_remove lrBuffer\">\n";
				echo "\t\t\t\t\t</div>\n";
			}
		}
	}
?>