<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$ext = '';
	
	if (isset($_POST['submit'])) {
		if ($_POST['deleteAvatar']) unlink(FILEROOT."/ucp/avatars/$userID.jpg");
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
				
				$destination = FILEROOT.'/ucp/avatars/'.$userID.'.'.$ext;
				foreach (glob(FILEROOT.'/ucp/avatars/'.$userID.'.*') as $oldFile) unlink($oldFile);
				if ($ext == 'jpg') imagejpeg($tempColor, $destination, 100);
				elseif ($ext == 'gif') imagegif($tempColor, $destination);
				elseif ($ext == 'png') imagepng($tempColor, $destination, 0);
				imagedestroy($tempImg);
				imagedestroy($tempColor);
			}
		}
		
		$showAvatars = isset($_POST['showAvatars'])?1:0;
		$validTimezones = array('Etc/GMT+12','Pacific/Apia','Pacific/Honolulu','America/Anchorage','America/Los_Angeles','America/Phoenix','America/Denver','America/Chihuahua','America/Managua','America/Regina','America/Mexico_City','America/Chicago','America/Indianapolis','America/Bogota','America/New_York','America/Caracas','America/Santiago','America/Halifax','America/St_Johns','America/Buenos_Aires','America/Godthab','America/Sao_Paulo','America/Noronha','Atlantic/Cape_Verde','Atlantic/Azores','Africa/Casablanca','Europe/London','Africa/Lagos','Europe/Berlin','Europe/Paris','Europe/Sarajevo','Europe/Belgrade','Africa/Johannesburg','Asia/Jerusalem','Europe/Istanbul','Europe/Helsinki','Africa/Cairo','Europe/Bucharest','Africa/Nairobi','Asia/Riyadh','Europe/Moscow','Asia/Baghdad','Asia/Tehran','Asia/Muscat','Asia/Tbilisi','Asia/Kabul','Asia/Karachi','Asia/Yekaterinburg','Asia/Calcutta','Asia/Katmandu','Asia/Colombo','Asia/Dhaka','Asia/Novosibirsk','Asia/Rangoon','Asia/Bangkok','Asia/Krasnoyarsk','Australia/Perth','Asia/Taipei','Asia/Singapore','Asia/Hong_Kong','Asia/Irkutsk','Asia/Tokyo','Asia/Seoul','Asia/Yakutsk','Australia/Darwin','Australia/Adelaide','Pacific/Guam','Australia/Brisbane','Asia/Vladivostok','Australia/Hobart','Australia/Sydney','Asia/Magadan','Pacific/Fiji','Pacific/Auckland','Pacific/Tongatapu');
		$timezone = in_array($_POST['timezone'], $validTimezones)?$_POST['timezone']:'Europe/London';
//		$timezone = preg_match('/[+-][01]\d{1}:\d{2}/', $_POST['timezone'])?$_POST['timezone']:'+00:00';
		$showTZ = isset($_POST['showTZ'])?1:0;
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
		
		$updateUser = $mysql->prepare("UPDATE users SET showAvatars = :showAvatars, avatarExt = :avatarExt, timezone = :timezone, showTZ = :showTZ, gender = :gender, birthday = :birthday, showAge = :showAge, location= :location, aim = :aim, gmail = :gmail, twitter = :twitter, stream = :stream, games = :games, newGameMail = :newGameMail  WHERE userID = :userID");
		$updateUser->execute(array(
			'showAvatars' => $showAvatars,
			'avatarExt' => $ext,
			'timezone' => $timezone,
			'showTZ' => $showTZ,
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
			'userID' => $userID
		));
		
		header('Location: '.SITEROOT.'/ucp/cp/?updated=1');
	} else header('Location: '.SITEROOT.'/user');
?>