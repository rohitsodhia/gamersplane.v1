<?
	addPackage('tools');

	$packageRoot = FILEROOT.'/includes/forums/';
	require_once($packageRoot.'ForumManager.class.php');
	require_once($packageRoot.'ForumPermissions.class.php');
	require_once($packageRoot.'Forum.class.php');
	require_once($packageRoot.'ThreadManager.class.php');
	require_once($packageRoot.'Thread.class.php');
	require_once($packageRoot.'ForumPoll.class.php');
	require_once($packageRoot.'ForumSearch.class.php');
	require_once($packageRoot.'Post.class.php');
	require_once($packageRoot.'ForumView.class.php');
?>