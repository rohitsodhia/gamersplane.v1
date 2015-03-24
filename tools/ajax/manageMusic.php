<?
	$checkPrivilage = $mysql->query("SELECT userID FROM privilages WHERE userID = {$currentUser->userID} AND privilage = 'manageMusic'");
	if ($checkPrivilage->rowCount()) {
		$songID = $_POST['songID'];
		$action = $_POST['action'];
		$song = $mongo->music->findOne(array('_id' => new MongoId($songID)));
		if ($song && $action == 'toggleApproval') 
			$mongo->music->update(array('_id' => new MongoId($songID)), array('$set' => array('approved' => !$song['approved'])));
		elseif ($song && $action == 'reject') 
			$mongo->music->remove(array('_id' => new MongoId($songID)));
	}
?>