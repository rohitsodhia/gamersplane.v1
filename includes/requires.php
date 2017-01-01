<?
	require_once('vendor/autoload.php');

	$envs = explode("\n", file_get_contents('.env'));
	foreach ($envs as $env) {
		$env = trim($env);
		if (strlen($env)) {
			putenv($env);
		}
	}

	require_once('connect.php');
	require_once('variables.php');
//	require_once('classes.php');
	require_once('functions.php');
	require_once('rhocode.php');

	startSession();

//	require_once(FILEROOT.'/blog/wp-blog-header.php');
//	header_remove('X-Pingback');

	// require_once('Mobile_Detect.php');
	// $mobileDetect = new Mobile_Detect();

	require_once('User.class.php');
	require_once('HistoryLogger.class.php');
	require_once('HistoryCache.class.php');
?>
