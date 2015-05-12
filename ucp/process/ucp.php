<?
	$avatarExt = '';
	$fileUploaded = false;
	
	if (isset($_POST['submit'])) {
		if (isset($_POST['userID']) && intval($_POST['userID']) && $currentUser->checkACP('users')) {
			$user = new User(intval($_POST['userID']));
			if (!$user->userID) { header('Location: /ucp/'); exit; }
		} else 
			$user = $currentUser;

		if ($_POST['deleteAvatar']) 
			unlink(FILEROOT."/ucp/avatars/{$user->userID}.jpg");
		if ($_FILES['avatar']['error'] == 0 && $_FILES['avatar']['size'] > 15 && $_FILES['avatar']['size'] < 1048576) {
			$avatarExt = trim(end(explode('.', strtolower($_FILES['avatar']['name']))));
			if ($avatarExt == 'jpeg') 
				$avatarExt = 'jpg';
			if (in_array($avatarExt, array('jpg', 'gif', 'png'))) {
				$maxWidth = 150;
				$maxHeight = 150;
				
				list($imgWidth, $imgHeight, $imgType) = getimagesize($_FILES['avatar']['tmp_name']);
				if ($imgWidth >= $maxWidth && $imgHeight >= $maxHeight) {
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
					
					$destination = FILEROOT.'/ucp/avatars/'.$user->userID.'.'.$avatarExt;
					foreach (glob(FILEROOT.'/ucp/avatars/'.$user->userID.'.*') as $oldFile) 
						unlink($oldFile);
					if ($avatarExt == 'jpg') 
						imagejpeg($tempColor, $destination, 100);
					elseif ($avatarExt == 'gif') 
						imagegif($tempColor, $destination);
					elseif ($avatarExt == 'png') 
						imagepng($tempColor, $destination, 0);
					imagedestroy($tempImg);
					imagedestroy($tempColor);
					$fileUploaded = true;
				}
			} elseif ($avatarExt == 'svg') {
				foreach (glob(FILEROOT.'/ucp/avatars/'.$user->userID.'.*') as $oldFile) 
					unlink($oldFile);
				move_uploaded_file($_FILES['avatar']['tmp_name'], FILEROOT."/ucp/avatars/{$user->userID}.svg");
				$fileUploaded = true;
			}

			if ($avatarExt == '') $avatarExt = null;
			$user->updateUsermeta('avatarExt', $avatarExt, true);
		}

		$user->updateUsermeta('showAvatars', isset($_POST['showAvatars'])?1:0);
		if ($_POST['gender'] == 'n') 
			$gender = '';
		else 
			$gender = $_POST['gender'] == 'm'?'m':'f';
		$user->updateUsermeta('gender', $gender);
		$birthday = intval($_POST['year']).'-'.(intval($_POST['month']) <= 9?'0':'').intval($_POST['month']).'-'.(intval($_POST['day']) <= 9?'0':'').intval($_POST['day']);
		if (preg_match('/^[12]\d{3}-[01]\d-[0-3]\d$/', $birthday)) 
			$user->updateUsermeta('birthday', $birthday);
		$user->updateUsermeta('showAge', isset($_POST['showAge'])?1:0);
		$user->updateUsermeta('location', sanitizeString($_POST['location']));
		$user->updateUsermeta('twitter', sanitizeString($_POST['twitter']));
		$user->updateUsermeta('stream', sanitizeString($_POST['stream']));
		$user->updateUsermeta('games', sanitizeString($_POST['games']));
		$user->updateUsermeta('pmMail', intval($_POST['pmMail'])?1:0);
		$user->updateUsermeta('newGameMail', intval($_POST['newGameMail'])?1:0);
		$user->updateUsermeta('gmMail', intval($_POST['gmMail'])?1:0);

		$errors = '?';
		$oldPass = $_POST['oldPass'];
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		if (strlen($password1) && strlen($password2) ) {
			if (!$user->validate($oldPass) || $user->userID != $currentUser->userID) 
				$formErrors->addError('wrongPass');
			if (strlen($password1) < 6) 
				$formErrors->addError('passShort');
			if (strlen($password1) < 32) 
				$formErrors->addError('passLong');
			if ($password1 != $password2) 
				$formErrors->addError('passMismatch');

			if (!$formErrors->errorsExist())
				$user->updatePassword($password1);
		}

		if (in_array($_POST['postSide'], array('l', 'r', 'c'))) 
			$postSide = $_POST['postSide'];
		else 
			$postSide = 'l';
		$user->updateUsermeta('postSide', $postSide);

		header('Location: /ucp/'.($user->userID != $currentUser->userID?$user->userID.'/':'').($errors == '?'?'?updated=1':$errors));
	} else 
		header('Location: /user');
?>