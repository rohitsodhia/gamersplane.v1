<?
	$currentUser->checkACP('music');

	function uploadLogo($_id, $logoFile) {
		if ($logoFile['error'] == 0 && $logoFile['size'] > 15 && $logoFile['size'] < 1048576) {
			$logoExt = trim(end(explode('.', strtolower($logoFile['name']))));
			if ($logoExt == 'jpeg') $logoExt = 'jpg';
			if (in_array($logoExt, array('jpg', 'gif', 'png'))) {
				$maxWidth = 300;
				$maxHeight = 300;
				
				list($imgWidth, $imgHeight, $imgType) = getimagesize($logoFile['tmp_name']);
				if ($imgWidth >= $maxWidth && $imgHeight >= $maxHeight) {
					if (image_type_to_mime_type($imgType) == 'image/jpeg' || image_type_to_mime_type($imgType) == 'image/pjpeg') $tempImg = imagecreatefromjpeg($logoFile['tmp_name']);
					elseif (image_type_to_mime_type($imgType) == 'image/gif') $tempImg = imagecreatefromgif($logoFile['tmp_name']);
					elseif (image_type_to_mime_type($imgType) == 'image/png') $tempImg = imagecreatefrompng($logoFile['tmp_name']);
					
					$xRatio = $maxWidth / $imgWidth;
					$yRatio = $maxHeight / $imgHeight;
					
					if ($imgWidth <= $maxWidth && $imgHeight <= $maxHeight) {
						$finalWidth = $imgWidth;
						$finalHeight = $imgHeight;
					} elseif (($xRatio * $imgHeight) < $maxHeight) {
						$finalWidth = $maxWidth;
						$finalHeight = ceil($xRatio * $imgHeight);
					} else {
						$finalWidth = ceil($yRatio * $imgWidth);
						$finalHeight = $maxHeight;
					}
					
					$tempColor = imagecreatetruecolor($finalWidth, $finalHeight);
					imagealphablending($tempColor, false);
					imagesavealpha($tempColor,true);
					imagecopyresampled($tempColor, $tempImg, 0, 0, 0, 0, $finalWidth, $finalHeight, $imgWidth, $imgHeight);
					
					$destination = FILEROOT.'/images/links/'.$_id.'.'.$logoExt;
					foreach (glob(FILEROOT.'/images/links/'.$_id.'.*') as $oldFile) unlink($oldFile);
					if ($logoExt == 'jpg') imagejpeg($tempColor, $destination, 100);
					elseif ($logoExt == 'gif') imagegif($tempColor, $destination);
					elseif ($logoExt == 'png') imagepng($tempColor, $destination, 0);
					imagedestroy($tempImg);
					imagedestroy($tempColor);

					return $logoExt;
				}
			} elseif ($logoExt == 'svg') {
				foreach (glob(FILEROOT.'/images/links/'.$_id.'.*') as $oldFile) unlink($oldFile);
				move_uploaded_file($logoFile['tmp_name'], FILEROOT."/images/links/{$_id}.svg");

				return 'svg';
			}
		}
		return null;
	}

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