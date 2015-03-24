<?
	$currentUser->checkACP('autocomplete');

	$updateName = $mysql->prepare('UPDATE userAddedItems SET name = :name WHERE uItemID = '.intval($_POST['uItemID']));
	$updateName->bindParam(':name', $_POST['name']);
	$updateName->execute();
	$newSystemItem = $mysql->query('SELECT * FROM userAddedItems WHERE uItemID = '.intval($_POST['uItemID']));
	$newSystemItem = $newSystemItem->fetch();

	if ($_POST['action'] == 'add') {
		$addSystemRequest = $mysql->query("INSERT INTO system_charAutocomplete_map SET system = {$newSystemItem['system']}, itemID = {$newSystemItem['itemID']}");
		$mysql->query("UPDATE userAddedItems SET action = 'approved', actedBy = {$currentUser->userID}, actedOn = NOW() WHERE uItemID = ".intval($_POST['uItemID']));
	} elseif ($_POST['action'] == 'reject') {
		$mysql->query("UPDATE userAddedItems SET action = 'rejected', actedBy = {$currentUser->userID}, actedOn = NOW() WHERE uItemID = ".intval($_POST['uItemID']));
	}
?>