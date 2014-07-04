<?
	$loggedIn = checkLogin();
	require_once(FILEROOT.'/includes/tools/Music_consts.class.php');
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Add Music</h1>
		
		<form method="post" action="/tools/process/music/add/" class="hbMargined">
			<div class="tr">
				<label for="url">URL:</label>
				<input id="url" type="text" name="url">
			</div>
			<div id="noURL" class="alert">URL is required.</div>
			<div id="invalidURL" class="alert">This URL isn't one we currently accept.</div>
			<div class="note">We currently only accept YouTube and Sound Cloud links. If you have another music service you want us to support, please <a href="/contact/">contact us.</a></div>
			<div class="tr">
				<label for="title">Title:</label>
				<input id="title" type="text" name="title">
			</div>
			<div id="noTitle" class="alert">Title is required.</div>
			<div class="tr">
				<label>Genres:</label>
				<div id="noGenes" class="alert">At least one genre must be selected.</div>
			</div>
			<div id="genres">
<?
	foreach (Music_consts::getGameTypes() as $type) {
		$cleanType = preg_replace('/[^A-za-z]/', '', $type);
?>
				<div>
					<input id="<?=$cleanType?>" type="checkbox" name="genre[<?$type?>]">
					<label for="<?=$cleanType?>"><?=$type?></label>
				</div>
<?	} ?>
			</div>
			<div id="submitDiv"><button type="submit" name="submit" class="fancyButton">Submit</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>