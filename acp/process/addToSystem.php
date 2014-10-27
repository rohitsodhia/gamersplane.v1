<?
	$userID = $_SESSION['userID'];

	$acpPermissions = $mysql->query("SELECT permission FROM acpPermissions WHERE userID = $userID");
	$acpPermissions = $acpPermissions->fetchAll(PDO::FETCH_COLUMN);
	if (sizeof($acpPermissions) == 0) { header('Location: /'); exit; }
	elseif (!in_array('autocomplete', $acpPermissions) && !in_array('all', $acpPermissions)) { header('Location: /acp/'); exit; }

	$updateName = $mysql->prepare('UPDATE userAddedItems SET name = :name WHERE uItemID = '.intval($_POST['uItemID']));
	$updateName->bindParam(':name', $_POST['name']);
	$updateName->execute();
	$newSystemItem = $mysql->query('SELECT * FROM userAddedItems WHERE uItemID = '.intval($_POST['uItemID']));
	$newSystemItem = $newSystemItem->fetch();

	if ($_POST['action'] == 'add') {
		$addSystemRequest = $mysql->query("INSERT INTO system_charAutocomplete_map SET systemID = {$newSystemItem['systemID']}, itemID = {$newSystemItem['itemID']}");
		$mysql->query("UPDATE userAddedItems SET action = 'approved', actedBy = {$userID}, actedOn = NOW() WHERE uItemID = ".intval($_POST['uItemID']));
	} elseif ($_POST['action'] == 'reject') {
		$mysql->query("UPDATE userAddedItems SET action = 'rejected', actedBy = {$userID}, actedOn = NOW() WHERE uItemID = ".intval($_POST['uItemID']));
	}
?>