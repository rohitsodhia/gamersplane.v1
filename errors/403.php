<?
	$responsivePage=true;
	header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');

	$errorPage = TRUE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
			<h1 class="headerbar"><i class="ra ra-locked-fortress"></i> 403 Error</h1>
			<p>Don't peek behind the DM screen!</p>
			<p>You might want to try looking somewhere else.</p>
<? require_once(FILEROOT.'/footer.php'); ?>