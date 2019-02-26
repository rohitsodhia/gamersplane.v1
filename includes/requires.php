<?
	require_once(dirname(__FILE__).'/../vendor/autoload.php');

	$envs = explode("\n", file_get_contents(dirname(__FILE__).'/../.env'));
	foreach ($envs as $env) {
		$env = trim($env);
		if (strlen($env)) {
			putenv($env);
		}
	}

	require_once(dirname(__FILE__).'/connect.php');
	require_once(dirname(__FILE__).'/variables.php');
//	require_once('classes.php');
	require_once(dirname(__FILE__).'/functions.php');
	require_once(dirname(__FILE__).'/rhocode.php');

	startSession();

//	require_once(FILEROOT.'/blog/wp-blog-header.php');
//	header_remove('X-Pingback');

	// require_once('Mobile_Detect.php');
	// $mobileDetect = new Mobile_Detect();

	require_once('User.class.php');
	require_once('HistoryLogger.class.php');
	require_once('HistoryCache.class.php');
?>
