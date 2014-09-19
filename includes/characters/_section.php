<?
	function newItemized($type, $name, $system) {
		global $mysql, $systems;
		$userID = intval($_SESSION['userID']);

		if ($system == 'custom') return false;

		$itemCheck = $mysql->prepare("SELECT {$type}ID FROM {$type}sList WHERE LOWER(searchName) = :searchName");
		$itemCheck->bindValue(':searchName', sanitizeString($name, 'search_format'));
		$itemCheck->execute();
		$systemID = $systems->getSystemID($system);
		if ($itemCheck->rowCount()) {
			$itemID = $itemCheck->fetchColumn();
			$inSystem = $mysql->query("SELECT systemID FROM system_{$type}_map WHERE systemID = $systemID AND {$type}ID = $itemID");
			if ($inSystem->rowCount() == 0) {
				$addItem = $mysql->prepare("INSERT INTO userAddedItems (itemType, itemID, addedBy, addedOn, systemID) VALUES (:itemType, :itemID, {$userID}, NOW(), {$systemID})");
				$addItem->bindValue(':itemType', $type);
				$addItem->bindValue(':itemID', $itemID);
				$addItem->execute();
			}
		} else {
			$addItem = $mysql->prepare("INSERT INTO userAddedItems (itemType, name, addedBy, addedOn, systemID) VALUES (:itemType, :name, {$userID}, NOW(), {$systemID})");
			$addItem->bindValue(':itemType', $type);
			$addItem->bindValue(':name', sanitizeString($name, 'rem_dup_spaces'));
			$addItem->execute();
		}

		return true;
	}
?>