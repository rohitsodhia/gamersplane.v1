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
			<div id="noURL" class="alert hideDiv">URL is required.</div>
			<div id="dupURL" class="alert hideDiv">This song is already in our system.</div>
			<div id="invalidURL" class="alert hideDiv">This URL isn't one we currently accept.</div>
			<div class="note">We currently only accept YouTube and Sound Cloud links. If you have another music service you want us to support, please <a href="/contact/">contact us.</a></div>
			<div class="tr">
				<label for="title">Title:</label>
				<input id="title" type="text" name="title">
			</div>
			<div id="noTitle" class="alert hideDiv">Title is required.</div>
			<div class="tr">
				<label>Has lyrics?</label>
				<input id="hasLyrics" type="radio" name="lyrics" value="yes"> <label for="hasLyrics" class="radioLabel">Yes</label>
				<input id="noLyrics" type="radio" name="lyrics" value="no"> <label for="noLyrics" class="radioLabel">No</label>
			</div>
			<div id="noTitle" class="alert hideDiv">Title is required.</div>
			<div class="tr">
				<label>Genres:</label>
				<div id="noGenres" class="alert hideDiv">At least one genre must be selected.</div>
			</div>
			<div id="genres" class="tr">
<?
	foreach (Music_consts::getGameTypes() as $type) {
		$cleanType = preg_replace('/[^A-za-z]/', '', $type);
?>
				<div>
					<input id="<?=$cleanType?>" type="checkbox" name="genre[<?=$type?>]">
					<label for="<?=$cleanType?>"><?=$type?></label>
				</div>
<?	} ?>
			</div>
			<div id="notesRow" class="tr">
				<label for="notes">Notes:</label>
				<textarea id="notes" name="notes"></textarea>
			</div>
			<div id="submitDiv"><button type="submit" name="submit" class="fancyButton">Submit</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>