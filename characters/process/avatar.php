<?
	if (isset($_POST['submit'])) {
		DEFINE(SYSTEM, $_POST['system']);
		$characterID = intval($_POST['characterID']);
		$charPermissions = false;
		if ($systems->verifySystem(SYSTEM)) {
		require_once(FILEROOT."/includes/packages/".SYSTEM."Character.package.php");
		$charClass = $systems->systemClassName(SYSTEM).'Character';
			$dispatchInfo['title'] = 'Edit '.$systems->getFullName(SYSTEM).' Character Sheet';
			if ($character = new $charClass($characterID)) {
				$character->load();
				$charPermissions = $character->checkPermissions($currentUser->userID);
			}
		}

		if ($charPermissions && (($_FILES['avatar'] && $_FILES['avatar']['error'] == 0 && $_FILES['avatar']['size'] > 15 && $_FILES['avatar']['size'] < 1048576) || $_POST['delete'])) {
			unlink(FILEROOT."/characters/avatars/{$characterID}.jpg");
			if (isset($_FILES['avatar'])) {
				$avatarExt = trim(end(explode('.', strtolower($_FILES['avatar']['name']))));
				if ($avatarExt == 'jpeg') $avatarExt = 'jpg';
				if (in_array($avatarExt, array('jpg', 'gif', 'png'))) {
					$maxWidth = 150;
					$maxHeight = 150;
					
					list($imgWidth, $imgHeight, $imgType) = getimagesize($_FILES['avatar']['tmp_name']);
					if (image_type_to_mime_type($imgType) == 'image/jpeg' || image_type_to_mime_type($imgType) == 'image/pjpeg') $tempImg = imagecreatefromjpeg($_FILES['avatar']['tmp_name']);
					elseif (image_type_to_mime_type($imgType) == 'image/gif') $tempImg = imagecreatefromgif($_FILES['avatar']['tmp_name']);
					elseif (image_type_to_mime_type($imgType) == 'image/png') $tempImg = imagecreatefrompng($_FILES['avatar']['tmp_name']);
					
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
					
					$destination = FILEROOT."/characters/avatars/{$characterID}.jpg";
					if (file_exists($destination)) unlink($destination);
					imagejpeg($tempColor, $destination, 100);
					imagedestroy($tempImg);
					imagedestroy($tempColor);
					$fileUploaded = true;
				}
			}
			if (isset($_POST['modal']) && $fileUploaded) echo 2;
			else if (isset($_POST['modal'])) echo 1;
			else header("Location: /characters/avatar/".SYSTEM."/{$characterID}/?modal=1");
			exit;
		}
	}

	if (isset($_POST['modal'])) echo 0;
	else header('Location: /characters/');
?>