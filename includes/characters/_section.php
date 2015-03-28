<?
	function newItemized($type, $name, $system) {
		global $currentUser, $mysql, $systems;

		if ($system == 'custom') 
			return false;
		if ($systems->verifySystem($system)) 
			return false;

		$itemCheck = $mysql->prepare("SELECT itemID FROM charAutocomplete WHERE type = :type AND LOWER(searchName) = :searchName");
		$itemCheck->bindValue(':type', $type);
		$itemCheck->bindValue(':searchName', sanitizeString($name, 'search_format'));
		$itemCheck->execute();
		if ($itemCheck->rowCount()) {
			$itemID = $itemCheck->fetchColumn();
			$inSystem = $mysql->query("SELECT system FROM system_charAutocomplete_map WHERE system = '{$system}' AND itemID = {$itemID}");
			if ($inSystem->rowCount() == 0) {
				try {
					$addItem = $mysql->prepare("INSERT INTO userAddedItems (itemType, itemID, addedBy, addedOn, system) VALUES (:itemType, :itemID, {$currentUser->userID}, NOW(), '{$system}')");
					$addItem->bindValue(':itemType', $type);
					$addItem->bindValue(':itemID', $itemID);
					$addItem->execute();
				} catch (Exception $e) {}
			}
		} else {
			try {
				$addItem = $mysql->prepare("INSERT INTO userAddedItems (itemType, name, addedBy, addedOn, system) VALUES (:itemType, :name, {$currentUser->userID}, NOW(), '{$system}')");
				$addItem->bindValue(':itemType', $type);
				$addItem->bindValue(':name', sanitizeString($name, 'rem_dup_spaces'));
				$addItem->execute();
			} catch (Exception $e) {}
		}

		return true;
	}
?>