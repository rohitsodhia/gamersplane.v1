<?
	$ext = '';
	$fileUploaded = false;
	
	if (isset($_POST['submit'])) {
		if ($_POST['deleteAvatar']) unlink(FILEROOT."/ucp/avatars/{$currentUser->userID}.jpg");
		if ($_FILES['avatar']['error'] == 0 && $_FILES['avatar']['size'] > 15 && $_FILES['avatar']['size'] < 1048576) {
			$ext = trim(end(explode('.', strtolower($_FILES['avatar']['name']))));
			if ($ext == 'jpeg') $ext = 'jpg';
			if (in_array($ext, array('jpg', 'gif', 'png'))) {
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
				
				$destination = FILEROOT.'/ucp/avatars/'.$currentUser->userID.'.'.$ext;
				foreach (glob(FILEROOT.'/ucp/avatars/'.$currentUser->userID.'.*') as $oldFile) unlink($oldFile);
				if ($ext == 'jpg') imagejpeg($tempColor, $destination, 100);
				elseif ($ext == 'gif') imagegif($tempColor, $destination);
				elseif ($ext == 'png') imagepng($tempColor, $destination, 0);
				imagedestroy($tempImg);
				imagedestroy($tempColor);
				$fileUploaded = true;
			}
		}
		
		$showAvatars = isset($_POST['showAvatars'])?1:0;
		if ($_POST['gender'] == 'n') $gender = '';
		else $gender = $_POST['gender'] == 'm'?'m':'f';
		$birthday = intval($_POST['year']).'-'.intval($_POST['month']).'-'.intval($_POST['day']);
		$showAge = isset($_POST['showAge'])?1:0;
		$location = sanitizeString($_POST['location']);
		$aim = sanitizeString($_POST['aim']);
		$gmail = sanitizeString($_POST['gmail']);
		$twitter = sanitizeString($_POST['twitter']);
		$stream = sanitizeString($_POST['stream']);
		$games = sanitizeString($_POST['games']);
		$newGameMail = $_POST['newGameMail']?1:0;
		
		$updateUser = $mysql->prepare("UPDATE users SET showAvatars = :showAvatars, ".($fileUploaded?'avatarExt = :avatarExt, ':'')."gender = :gender, birthday = :birthday, showAge = :showAge, location= :location, aim = :aim, gmail = :gmail, twitter = :twitter, stream = :stream, games = :games, newGameMail = :newGameMail  WHERE userID = :userID");
		$updates = array(
			'showAvatars' => $showAvatars,
			'gender' => $gender,
			'birthday' => $birthday,
			'showAge' => $showAge,
			'location'=> $location,
			'aim' => $aim,
			'gmail' => $gmail,
			'twitter' => $twitter,
			'stream' => $stream,
			'games' => $games,
			'newGameMail' => $newGameMail,
			'userID' => $currentUser->userID
		);
		if ($fileUploaded) $updates['avatarExt'] = $ext;
		$updateUser->execute($updates);
		
		header('Location: /ucp/cp/?updated=1');
	} else header('Location: /user');
?>