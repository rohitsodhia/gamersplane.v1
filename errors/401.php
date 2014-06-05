<?
	header($_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized');
	$loggedIn = checkLogin(0);
	
	$errorPage = TRUE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
			<h1 class="headerbar">Unauthorized Entry (401 Error)</h1>
			<p>Try logging in <b>correctly</b> if you want to access this part of the site!</p>
			<p>Please <a href="/login">login</a> or <a href="/register">register</a> to view this page.</p>
<? require_once(FILEROOT.'/footer.php'); ?>