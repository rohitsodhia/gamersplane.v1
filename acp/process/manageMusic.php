<?
	$currentUser->checkACP('music');

	try {
		$_id = genMongoId($_POST['mongoID']);
	} catch (Exception $e) {
		return false;
	}
	$action = $_POST['action'];
	if ($action == 'approve' || $action == 'unapprove') {
		$mongo->music->updateOne(
			['_id' => $_id],
			['$set' => ['approved' => $action == 'approve' ? true : false]]
		);
		if (isset($_POST['modal'])) {
			echo $action == 'approve'?'Unapprove':'Approve';
		} else {
			header('Location: /acp/music/');
		}
	} elseif ($action == 'delete') {
		$mongo->music->removeOne(['_id' => $_id]);
		if (isset($_POST['modal'])) {
			echo 'deleted';
		} else {
			header('Location: /acp/music/');
		}
	} elseif ($action == 'edit') {
		$data = $_POST;
		$data['genres'] = array_keys($data['genre']);
		unset($data['mongoID'], $data['action'], $data['genre'], $data['modal'], $data['add']);
		$mongo->music->updateOne(
			['_id' => $_id],
			['$set' => $data]
		);
	}
?>
