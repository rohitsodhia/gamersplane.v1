<?
	$mapID = intval($_POST['mapID']);
	$info = sanitizeString($_POST['info']);
	
	if (isset($_POST['save'])) {
		$mapCheck = $mysql->query("SELECT p.primaryGM FROM maps m, players p WHERE m.gameID = p.gameID AND p.userID = {$currentUser->userID} AND m.mapID = $mapID");
		if ($mapCheck->rowCount()) {
			$updateMap = $mysql->prepare("UPDATE maps SET info = :info where mapID = $mapID");
			$updateMap->execute(array(':info' => $info));
			echo json_encode(array('success' => true));
		} else echo json_encode(array('error' => 'Invalid user'));
	} else echo json_encode(array('error' => 'Failed submit'));
?>