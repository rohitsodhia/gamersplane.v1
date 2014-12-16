<?
	require_once('connect.php');
	require_once('variables.php');
//	require_once('classes.php');
	require_once('functions.php');
	require_once('rhocode.php');
	
	startSession();
	
//	require_once(FILEROOT.'/blog/wp-blog-header.php');
//	header_remove('X-Pingback');

	require_once('Mobile_Detect.php');
	$mobileDetect = new Mobile_Detect();

	require_once('User.class.php');
?>