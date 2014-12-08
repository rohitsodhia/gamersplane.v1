<?
	$currentUser->checkACP('autocomplete');

	$updateName = $mysql->prepare('UPDATE userAddedItems SET name = :name WHERE uItemID = '.intval($_POST['uItemID']));
	$updateName->bindParam(':name', $_POST['name']);
	$updateName->execute();
	$newItemInfo = $mysql->query('SELECT * FROM userAddedItems WHERE uItemID = '.intval($_POST['uItemID']));
	$newItemInfo = $newItemInfo->fetch();

	if ($_POST['action'] == 'add') {
		try {
			$addNewItem = $mysql->prepare("INSERT INTO charAutocomplete SET type = '{$newItemInfo['itemType']}', name = :name, searchName = :searchName, userDefined = {$newItemInfo['addedBy']}");
			$addNewItem->bindParam(':name', $_POST['name']);
			$addNewItem->bindParam(':searchName', sanitizeString($_POST['name'], 'search_format'));
			$addNewItem->execute();
			$itemID = $mysql->lastInsertId();
			$action = 'approved';
		} catch (Exception $e) {
			$findItem = $mysql->prepare("SELECT itemID FROM charAutocomplete WHERE searchName = :searchName");
			$findItem->bindParam(':searchName', sanitizeString($_POST['name'], 'search_format'));
			$findItem->execute();
			$itemID = $findItem->fetchColumn();
			$action = 'duplicate';
		}
		$addSystemRequest = $mysql->query("INSERT INTO userAddedItems SET itemType = '{$newItemInfo['itemType']}', itemID = {$itemID}, addedBy = {$newItemInfo['addedBy']}, addedOn = '{$newItemInfo['addedOn']}', systemID = {$newItemInfo['systemID']}");
		$mysql->query("UPDATE userAddedItems SET itemID = {$itemID}, systemID = NULL, action = '{$action}', actedBy = {$currentUser->userID}, actedOn = NOW() WHERE uItemID = ".intval($_POST['uItemID']));
	} elseif ($_POST['action'] == 'reject') {
		$mysql->query("UPDATE userAddedItems SET action = 'rejected', actedBy = {$currentUser->userID}, actedOn = NOW() WHERE uItemID = ".intval($_POST['uItemID']));
	}
?>