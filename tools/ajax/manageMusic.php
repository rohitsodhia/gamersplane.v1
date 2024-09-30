<?php
	$checkPrivilage = $mysql->query("SELECT userID FROM privilages WHERE userID = {$currentUser->userID} AND privilage IN ('all', 'manageMusic')");
	if ($checkPrivilage->rowCount()) {
		$songID = (int) $_POST['songID'];
		$action = $_POST['action'];
		$song = $mysql->query("SELECT approved FROM music WHERE id = {$songID}")->fetch();
		if ($song && $action == 'toggleApproval') {
			$mysql->query("UPDATE music SET approved = {$song['approved'] ? 0 : 1} WHERE id = {$songID}");
		} elseif ($song && $action == 'reject') {
			$mysql->query("DELETE FROM music WHERE id = {$songID}");
		}
	}
?>
