<?
	$loggedIn = checkLogin(0);
	require_once(FILEROOT.'/includes/tools/Music_consts.class.php');

	$checkPrivilage = $mysql->query("SELECT userID FROM privilages WHERE userID = {$userID} AND privilage = 'manageMusic'");
	if ($checkPrivilage->rowCount()) $manageMusic = true;
	else $manageMusic = false;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Music and Clips</h1>

		<div class="sideWidget left">
			<h2>Filter by Genre</h2>
			<form method="get">
				<ul class="clearfix">
<?
	$selectedGenres = array();
	foreach (Music_consts::getGameTypes() as $type) {
		$selectedGenre = isset($_GET['filter'], $_GET['genres']) && array_search($type, $_GET['genres']) !== false?true:false;
		$cleanType = preg_replace('/[^A-za-z]/', '', $type);
?>
					<li><input id="genre_<?=$cleanType?>" type="checkbox" name="genres[<?=$type?>]"> <label for="genre_<?=$cleanType?>"><?=$type?></label>
<?
		if ($selectedGenre) $selectedGenres[] = $type;
	}
?>
				</ul>
			</form>
			<div class="alignCenter"><button name="filter" value="filter" class="fancyButton">Filter</button></div>
		</div>
		<div class="mainColumn right">
<?	if ($loggedIn) { ?>
			<a id="addMusic" href="/tools/music/add/" class="fancyButton smallButton">Add Music</a>
<?	} ?>
<?
	$result = $mongo->music->find(array('approved' => true));
	if (sizeof($result)) {
?>
			<ul class="hbAttachedList">
<?		foreach ($result as $song) { ?>
				<li>
					<div class="clearfix">
						<a href="<?=$song['url']?>" target="_blank" class="song"><?=$song['title']?></a
						><div class="genres"><?=implode(', ', $song['genres'])?></div>
					</div>
					<div class="notes"><?=printReady($song['notes'])?></div>
<?			if ($manageMusic) { ?>
					<div class="manageSong">
					</div>
<?			} ?>
				</li>
<?		} ?>
			</ul>
<?	} ?>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>