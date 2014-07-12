<?
	checkLogin(1);
	
	if (isset($_POST['submit'])) {
		$errors = array();

		$url = sanitizeString($_POST['url']);
		$title = sanitizeString($_POST['title']);
		$genres = array();
		foreach ($_POST['genre'] as $genre => $unnecessary) $genres[] = $genre;
		$notes = $_POST['notes'];

		if (strlen($url) == 0) $errors['noURL'] = 1;
		else {
			preg_match('#http://(?:www\.)?(.*?)\.([\w\.]*)(?:/.*)?#', $url, $matches);
			$domain = $matches[1].'.'.$matches[2];
			if (!in_array($domain, array('youtube.com', 'soundcloud.com'))) $errors['invalidURL'] = 1;
			else {
				$duplicates = $mongo->music->findOne(array('url' => $url));
				if ($duplicates != null) $errors['dupURL'] = 1;
			}
		}
		if (strlen($title) == 0) $errors['noTitle'] = 1;
		if (sizeof($genres) == 0) $errors['noGenres'] = 1;

		if (sizeof($errors)) echo json_encode($errors);
		else {
			$mongo->music->insert(array(
				'userID' => $_SESSION['userID'],
				'username' => $_SESSION['username'],
				'url' => $url,
				'title' => $title,
				'genres' => $genres,
				'notes' => $notes,
				'approved' => false
			));
		}
	}
?>