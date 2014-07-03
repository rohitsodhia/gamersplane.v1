<?
	checkLogin(1);
	
	if ($_POST['submit']) {
		unset($_SESSION['errors']);
		unset($_SESSION['errorVals']);
		unset($_SESSION['errorTime']);

		$url = sanitizeString($_POST['url']);
		$title = sanitizeString($_POST['title']);
		$genres = $_POST['genre'];

		if (strlen($url) == 0) $_SESSION['errors']['noURL'] = 1;
		if (strlen($title) == 0) $_SESSION['errors']['noTitle'] = 1;
		if (sizeof($title) == 0) $_SESSION['errors']['noGenres'] = 1;
	}
	
	if (!isset($_POST['ajax'])) header('Location: /tools/cards');
?>