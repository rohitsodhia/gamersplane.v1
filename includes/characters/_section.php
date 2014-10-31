<?
	function newItemized($type, $name, $system) {
		global $currentUser, $mysql, $systems;

		if ($system == 'custom') return false;

		$itemCheck = $mysql->prepare("SELECT itemID FROM charAutocomplete WHERE type = :type AND LOWER(searchName) = :searchName");
		$itemCheck->bindValue(':type', $type);
		$itemCheck->bindValue(':searchName', sanitizeString($name, 'search_format'));
		$itemCheck->execute();
		$systemID = $systems->getSystemID($system);
		if ($itemCheck->rowCount()) {
			$itemID = $itemCheck->fetchColumn();
			$inSystem = $mysql->query("SELECT systemID FROM system_charAutocomplete_map WHERE systemID = {$systemID} AND itemID = {$itemID}");
			if ($inSystem->rowCount() == 0) {
				try {
					$addItem = $mysql->prepare("INSERT INTO userAddedItems (itemType, itemID, addedBy, addedOn, systemID) VALUES (:itemType, :itemID, {$currentUser->userID}, NOW(), {$systemID})");
					$addItem->bindValue(':itemType', $type);
					$addItem->bindValue(':itemID', $itemID);
					$addItem->execute();
				} catch (Exception $e) {}
			}
		} else {
			try {
				$addItem = $mysql->prepare("INSERT INTO userAddedItems (itemType, name, addedBy, addedOn, systemID) VALUES (:itemType, :name, {$currentUser->userID}, NOW(), {$systemID})");
				$addItem->bindValue(':itemType', $type);
				$addItem->bindValue(':name', sanitizeString($name, 'rem_dup_spaces'));
				$addItem->execute();
			} catch (Exception $e) {}
		}

		return true;
	}
?>