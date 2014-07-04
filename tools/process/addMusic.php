<?
	checkLogin(1);
	
	if (isset($_POST['submit'])) {
		$errors = array();

		$url = sanitizeString($_POST['url']);
		$title = sanitizeString($_POST['title']);
		$genres = $_POST['genre'];

		if (strlen($url) == 0) $errors['noURL'] = 1;
		if (strlen($title) == 0) $errors['noTitle'] = 1;
		if (sizeof($genres) == 0) $errors['noGenres'] = 1;

		if (sizeof($errors)) echo json_encode($errors);
		else {

		}
	}
?>