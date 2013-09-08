<?
	$loggedIn = checkLogin();
	
	$threadID = intval($pathOptions[2]);
	
	$type = $pathOptions[1];
	
	initializeMenu();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<div class="pageTitle">Send PM: Succesful</div>
		
		<p>Your PM was sent successfully.</p>
		<p>If you are not redirected automatically within a few seconds, <a href="<? echo SITEROOT.'/chat/thread/'.$threadID; ?>">click here</a>.</p>
		
		<input id="threadID" type="hidden" value="<? echo $threadID; ?>">
<? require_once(FILEROOT.'/footer.php'); ?>