<?
	$loggedIn = checkLogin();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Add Music</h1>
		
		<form method="post" action="/tools/process/addMusic.php" class="hbMargined">
			<div class="tr">
				<label for="url">URL:</label>
				<input id="url" type="text" name="url">
			</div>
			<div class="note">We currently only accept YouTube and Sound Cloud links. If you have another music service you want to link us, please <a href="/contact/">contact us.</a>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>