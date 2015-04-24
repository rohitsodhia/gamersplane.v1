<?
	$currentUser->checkACP('music');


	$levels = array(1 => 'Link', 'Affiliate');
	$action = $_POST['action'];
	if ($action == 'add') {
		$data = array('_id' => new MongoId());
		$data['title'] = $_POST['title'];
		$data['url'] = $_POST['url'];
		if (!strlen($data['title']) || !strlen($data['url'])) { header('Location: /acp/links/'); exit; }
		$data['level'] = $_POST['level'];
		if (!in_array($data['level'], array_keys($levels))) $data['level'] = 1;
		else $data['level'] = intval($data['level']);
		if (isset($_FILES['image'])) {
			$ext = uploadLogo($data['_id'], $_FILES['image']);
			if ($ext) $data['image'] = $ext;
		}
		$data['random'] = $mongo->execute('Math.random()');
		$data['random'] = $data['random']['retval'];

		$mongo->links->insert($data);
		if (isset($_POST['modal'])) echo 'added';
		else header('Location: /acp/links/');
	} elseif ($action == 'save') {
		$_id = new MongoId($_POST['mongoID']);
		if (!$_id) return false;

		$data['title'] = $_POST['title'];
		$data['url'] = $_POST['url'];
		if (!strlen($data['title']) || !strlen($data['url'])) { header('Location: /acp/links/'); exit; }
		$data['level'] = $_POST['level'];
		if (!in_array($data['level'], array_keys($levels))) $data['level'] = 1;
		else $data['level'] = intval($data['level']);
		if (isset($_FILES['image'])) {
			$ext = uploadLogo($_id, $_FILES['image']);
			if ($ext) $data['image'] = $ext;
		}

		$mongo->links->update(array('_id' => $_id), array('$set' => $data));
		if (isset($_POST['modal'])) {
			header('Content-Type: application/json');
			echo json_encode(array(
				'status' => 'updated',
				'image' => $ext
			));
		} else header('Location: /acp/links/');
	} elseif ($action == 'deleteImage') {
		$_id = new MongoId($_POST['mongoID']);
		if (!$_id) return false;

		foreach (glob(FILEROOT.'/images/links/'.$_id.'.*') as $oldFile) unlink($oldFile);
		if (isset($_POST['modal'])) {
			header('Content-Type: application/json');
			echo json_encode(array(
				'status' => 'imageDeleted'
			));
		} else header('Location: /acp/links/');
	} elseif ($action == 'delete') {
		$_id = new MongoId($_POST['mongoID']);
		if (!$_id) return false;

		$mongo->links->remove(array('_id' => $_id));
		if (isset($_POST['modal'])) {
			header('Content-Type: application/json');
			echo json_encode(array(
				'status' => 'deleted'
			));
		} else header('Location: /acp/music/');
	}
?>