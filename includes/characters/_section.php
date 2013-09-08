<?
	function getSkill($skillName) {
		global $mysql;

		$skillCheck = $mysql->prepare('SELECT skillID FROM skillsList WHERE LOWER(searchName) = :searchName');
		$skillCheck->bindValue(':searchName', sanitizeString($skillName, 'search_format'));
		$skillCheck->execute();
		if ($skillCheck->rowCount()) return $skillCheck->fetchColumn();
		else {
			$userID = intval($_SESSION['userID']);
			$addNewSkill = $mysql->prepare("INSERT INTO skillsList (name, userDefined) VALUES (:name, $userID)");
			$addNewSkill->bindValue(':name', $skillName);
			$addNewSkill->execute();
			return $mysql->lastInsertId();
		}
	}

	function getFeat($featName) {
		global $mysql;
		
		$featCheck = $mysql->prepare('SELECT featID FROM featsList WHERE LOWER(searchName) = :searchName');
		$featCheck->bindValue(':searchName', sanitizeString($featName, 'search_format'));
		$featCheck->execute();
		if ($featCheck->rowCount()) return $featCheck->fetchColumn();
		else {
			$userID = intval($_SESSION['userID']);
			$addNewFeat = $mysql->prepare("INSERT INTO featsList (name, userDefined) VALUES (:name, $userID)");
			$addNewFeat->bindValue(':name', $name);
			$addNewFeat->execute();
			return $mysql->lastInsertId();
		}
	}
?>