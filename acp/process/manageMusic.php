<?
	$currentUser->checkACP('music');

	$_id = new MongoId($_POST['mongoID']);
	if (!$_id) return false;
	$action = $_POST['action'];
	if ($action == 'approve' || $action == 'unapprove') {
		$mongo->music->update(array('_id' => $_id), array('$set' => array('approved' => $action == 'approve'?true:false)));
		if (isset($_POST['modal'])) echo $action == 'approve'?'Unapprove':'Approve';
		else header('Location: /acp/music/');
	} elseif ($action == 'delete') {
		if ($_id) {
			$mongo->music->remove(array('_id' => $_id));
			if (isset($_POST['modal'])) echo 'deleted';
			else header('Location: /acp/music/');
		}
	} elseif ($action == 'edit') {
		$data = $_POST;
		$data['genres'] = array_keys($data['genre']);
		unset($data['mongoID'], $data['action'], $data['genre'], $data['modal'], $data['add']);
		$mongo->music->update(array('_id' => $_id), array('$set' => $data));
	}
?>