<?php
	$checkPrivilage = $mysql->query("SELECT userID FROM privilages WHERE userID = {$currentUser->userID} AND privilage = 'manageMusic'");
	if ($checkPrivilage->rowCount()) {
		$songID = $_POST['songID'];
		$action = $_POST['action'];
		$song = $mongo->music->findOne(['_id' => genMongoId($songID)]);
		if ($song && $action == 'toggleApproval') {
			$mongo->music->update(
				['_id' => genMongoId($songID)],
				['$set' => ['approved' => !$song['approved']]]
			);
		} elseif ($song && $action == 'reject') {
			$mongo->music->deleteOne(['_id' => genMongoId($songID)]);
		}
	}
?>
