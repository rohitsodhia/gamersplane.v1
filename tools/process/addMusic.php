<?
	if (isset($_POST['submit'])) {
		$errors = array();

		$url = sanitizeString($_POST['url']);
		$title = sanitizeString($_POST['title']);
		$lyrics = $_POST['lyrics'] == 'yes'?true:false;
		$genres = array();
		foreach ($_POST['genre'] as $genre => $unnecessary) $genres[] = $genre;
		$notes = $_POST['notes'];

		if (strlen($url) == 0) 
			$errors['noURL'] = 1;
		else {
			preg_match('#https?://(?:www\.)?(.*?)\.([\w\.]*)(?:/.*)?#', $url, $matches);
			$domain = $matches[1].'.'.$matches[2];
			if (!in_array($domain, array('youtube.com', 'soundcloud.com'))) 
				$errors['invalidURL'] = 1;
			else {
				$duplicates = $mongo->music->findOne(array('url' => $url));
				if ($duplicates != null) 
					$errors['dupURL'] = 1;
			}
		}
		if (strlen($title) == 0) 
			$errors['noTitle'] = 1;
		if (sizeof($genres) == 0) 
			$errors['noGenres'] = 1;

		if (sizeof($errors)) 
			echo json_encode($errors);
		else {
			$mongo->music->insert(array(
				'userID' => $currentUser->userID,
				'username' => $currentUser->username,
				'url' => $url,
				'title' => $title,
				'lyrics' => $lyrics,
				'genres' => $genres,
				'notes' => $notes,
				'approved' => false
			));

			mail('contact@gamersplane.com', 'New Music', "New Music:\n\rusername: {$currentUser->username},\n\rurl => $url,\n\rtitle => $title", 'From: noone@gamersplane.com');
		}
	}
?>