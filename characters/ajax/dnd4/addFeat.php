<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$name = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['name'])));
			$featID = $mysql->query('SELECT featID FROM featsList WHERE name = "'.$name.'"');
			if ($featID->rowCount()) $featID = $featID->fetchColumn();
			else {
				$mysql->query('INSERT INTO featsList (name, userDefined) VALUES ("'.$name.'", '.intval($_SESSION['userID']).')');
				$featID = $mysql->lastInsertId();
			}
			$addFeat = $mysql->query("INSERT INTO dnd4_feats (characterID, featID) VALUES ($characterID, $featID)");
			if ($addFeat->getResult()) {
				echo "\t\t\t\t\t<div id=\"feat_$featID\" class=\"feat tr clearfix\">\n";
				echo "\t\t\t\t\t\t<span class=\"feat_name textLabel\">".mb_convert_case($name, MB_CASE_TITLE)."</span>\n";
				echo "\t\t\t\t\t\t<a href=\"".SITEROOT."/characters/dnd4/featNotes/$characterID/$featID?modal=1\" id=\"featNotes_$featID\" class=\"feat_notes\">Notes</a>\n";
				echo "\t\t\t\t\t\t<input type=\"image\" name=\"featRemove_$featID\" src=\"".SITEROOT."/images/cross.jpg\" value=\"$featID\" class=\"feat_remove lrBuffer\">\n";
				echo "\t\t\t\t\t</div>\n";
			}
		}
	}
?>