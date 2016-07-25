<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');

	require_once(FILEROOT.'/header.php');
	if ($loggedIn)
		include('home.php');
	else
		include('landing.php');
	require_once(FILEROOT.'/footer.php');
?>
