<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$name = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['name'])));
			$powerType = sanatizeString($_POST['type']);
			$addPower = $mysql->query("INSERT INTO dnd4_powers (characterID, name, type) VALUES ($characterID, '$name', '$powerType')");
			if ($addPower->getResult()) {
				echo "\t\t\t\t\t<div id=\"power_".str_replace(' ', '_', $name)."\" class=\"power\">\n";
				echo "\t\t\t\t\t\t<span class=\"power_name\">".mb_convert_case($name, MB_CASE_TITLE)."</span>\n";
				echo "\t\t\t\t\t\t<input type=\"image\" name=\"removePower_".str_replace(' ', '_', $name)."\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$name}\" class=\"power_remove lrBuffer\">\n";
				echo "\t\t\t\t\t</div>\n";
			}
		}
	}
?>