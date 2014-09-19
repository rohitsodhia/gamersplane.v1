<?
	$loggedIn = checkLogin(0);
	$userID = $_SESSION['userID'];

	$acpPermissions = $mysql->query("SELECT permission FROM acpPermissions WHERE userID = $userID");
	$acpPermissions = $acpPermissions->fetchAll(PDO::FETCH_COLUMN);
	if (sizeof($acpPermissions) == 0) { header('Location: /'); exit; }
	elseif (!in_array('autocomplete', $acpPermissions) && !in_array('all', $acpPermissions)) { header('Location: /acp/'); exit; }

	$updateName = $mysql->prepare('UPDATE userAddedItems SET name = :name WHERE uItemID = '.intval($_POST['uItemID']));
	$updateName->bindParam(':name', $_POST['name']);
	$updateName->execute();
	$newItemInfo = $mysql->query('SELECT * FROM userAddedItems WHERE uItemID = '.intval($_POST['uItemID']));
	$newItemInfo = $newItemInfo->fetch();

	if ($_POST['action'] == 'add') {
		$addNewItem = $mysql->prepare("INSERT INTO {$newItemInfo['itemType']}sList SET name = :name, searchName = :searchName, userDefined = {$newItemInfo['addedBy']}");
		$addNewItem->bindParam(':name', $_POST['name']);
		$addNewItem->bindParam(':searchName', sanitizeString($_POST['name'], 'search_format'));
		$addNewItem->execute();
		$itemID = $mysql->lastInsertId();
		$addSystemRequest = $mysql->query("INSERT INTO userAddedItems SET itemType = '{$newItemInfo['itemType']}', itemID = {$itemID}, addedBy = {$newItemInfo['addedBy']}, addedOn = '{$newItemInfo['addedOn']}', systemID = {$newItemInfo['systemID']}, action = 'approved', actedBy = {$userID}, actedOn = NOW()");
		$mysql->query("UPDATE userAddedItems SET systemID = NULL WHERE uItemID = ".intval($_POST['uItemID']));
	} elseif ($_POST['action'] == 'reject') {
		$mysql->query("UPDATE userAddedItems SET action = 'rejected', actedBy = {$userID}, actedOn = NOW() WHERE uItemID = ".intval($_POST['uItemID']));
	}
?>