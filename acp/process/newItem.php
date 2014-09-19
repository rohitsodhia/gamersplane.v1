<?
	$loggedIn = checkLogin(0);
	$userID = $_SESSION['userID'];

	$acpPermissions = $mysql->query("SELECT permission FROM acpPermissions WHERE userID = $userID");
	$acpPermissions = $acpPermissions->fetchAll(PDO::FETCH_COLUMN);
	if (sizeof($acpPermissions) == 0) { header('Location: /'); exit; }
	elseif (!in_array('autocomplete', $acpPermissions) && !in_array('all', $acpPermissions)) { header('Location: /acp/'); exit; }

	$newItemInfo = $mysql->query('SELECT * FROM newItemized WHERE newItemID = '.intval($_POST['newItemID']));
	$newItemInfo = $newItemInfo->fetch();
	if ($_POST['action'] == 'add') {
		$addNewItem = $mysql->prepare("INSERT INTO {$newItemInfo['itemType']}sList SET name = :name, searchName = :searchName, userDefined = {$newItemInfo['addedBy']}");
		$addNewItem->bindParam(':name', $_POST['name']);
		$addNewItem->bindParam(':searchName', sanitizeString($_POST['name'], 'search_format'));
		$addNewItem->execute();
		$itemID = $mysql->lastInsertId();
		$newItemInfo = $mysql->query("UPDATE newItemized SET name = NULL, itemID = {$itemID} WHERE newItemID = ".intval($_POST['newItemID']));
	} elseif ($_POST['action'] == 'reject') {
		$mysql->query('DELETE FROM newItemized WHERE newItemID = '.intval($_POST['newItemID']));
	}
?>