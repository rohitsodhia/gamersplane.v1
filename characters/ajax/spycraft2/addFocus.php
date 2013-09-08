<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$name = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['name'])));
			$focus = $mysql->query('SELECT focusID FROM spycraft2_focusesList WHERE name = "'.$name.'"');
			if ($focus->rowCount()) $focusID = $focus->fetchColumn();
			else {
				$mysql->query('INSERT INTO spycraft2_focusesList (name, userDefined) VALUES ("'.$name.'", '.intval($_SESSION['userID']).')');
				$focusID = $mysql->lastInsertId();
			}
			$addFocus = $mysql->query("INSERT INTO spycraft2_focuses (characterID, focusID) VALUES ($characterID, $focusID)");
			if ($addFocus->getResult()) {
				echo "\t\t\t\t\t<div id=\"focus_$focusID\" class=\"focus tr clearfix\">\n";
				echo "\t\t\t\t\t\t<input type=\"checkbox\" name=\"focusForte[$focusID]\" class=\"shortNum\">\n";
				echo "\t\t\t\t\t\t<span class=\"focus_name textLabel\">".mb_convert_case($name, MB_CASE_TITLE)."</span>\n";
				echo "\t\t\t\t\t\t<input type=\"image\" name=\"focusRemove_$focusID\" src=\"".SITEROOT."/images/cross.jpg\" value=\"$focusID\" class=\"focus_remove lrBuffer\">\n";
				echo "\t\t\t\t\t</div>\n";
			}
		}
	}
?>