<?
	$loggedIn = checkLogin(0);
	require_once(FILEROOT.'/includes/tools/Music_consts.class.php');

	if ($loggedIn) {
		$checkPrivilage = $mysql->query("SELECT userID FROM privilages WHERE userID = {$_SESSION['userID']} AND privilage = 'manageMusic'");
		if ($checkPrivilage->rowCount()) $manageMusic = true;
		else $manageMusic = false;
	} else $manageMusic = false;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Music and Clips</h1>

		<div class="sideWidget left">
			<h2>Filter</h2>
			<form method="get">
				<h3>Genre</h3>
				<ul class="clearfix">
<?
	$filter = array();
	foreach (Music_consts::getGenres() as $type) {
		$cleanType = preg_replace('/[^A-Za-z]/', '', $type);
		$selectedGenre = isset($_GET['filter'], $_GET['genres']) && array_key_exists($type, $_GET['genres']) !== false?true:false;
?>
					<li><input id="genre_<?=$cleanType?>" type="checkbox" name="genres[<?=$type?>]"<?=$selectedGenre?' checked="checked"':''?>> <label for="genre_<?=$cleanType?>"><?=$type?></label>
<?
		if ($selectedGenre) $filter['genres']['$in'][] = $type;
	}
	if (sizeof(Music_consts::getGenres()) == sizeof($filter['genres']['$in'])) unset($filter['genres']);
?>
				</ul>
				<h3>Lyrics?</h3>
				<ul class="clearfix">
					<li><input id="hasLyrics" type="checkbox" name="lyrics[has]"<?=isset($_GET['filter'], $_GET['lyrics']['has'])?' checked="checked"':''?>> <label for="hasLyrics">Has Lyrics</label></li>
					<li><input id="noLyrics" type="checkbox" name="lyrics[none]"<?=isset($_GET['filter'], $_GET['lyrics']['none'])?' checked="checked"':''?>> <label for="noLyrics">No Lyrics</label></li>
				</ul>
				<div class="alignCenter"><button name="filter" value="filter" class="fancyButton">Filter</button></div>
			</form>
		</div>
		<div class="mainColumn right">
<?	if ($loggedIn) { ?>
			<a id="addMusic" href="/tools/music/add/" class="fancyButton smallButton">Add Music</a>
<?	} ?>
<?
	if (!isset($_GET['filter'], $_GET['lyrics']['has'], $_GET['lyrics']['none'])) {
		if (isset($_GET['filter'], $_GET['lyrics']['has'])) $filter['lyrics'] = true;
		elseif (isset($_GET['filter'], $_GET['lyrics']['none'])) $filter['lyrics'] = false;
	}
	if ($manageMusic) $result = $mongo->music->find($filter)->sort(array('approved' => 1, 'genres' => 1, 'title' => 1));
	else $result = $mongo->music->find(array_merge($filter, array('approved' => true)))->sort(array('genres' => 1, 'title' => 1));
	if (sizeof($result)) {
?>
			<ul class="hbAttachedList">
<?		foreach ($result as $song) { ?>
				<li<?=!$song['approved']?' class="unapproved"':''?> data-id="<?=$song['_id']?>">
					<div class="clearfix">
						<a href="<?=$song['url']?>" target="_blank" class="song"><?=$song['title']?></a
						><div class="genres"><?=implode(', ', $song['genres'])?></div>
					</div>
<?			if (strlen($song['notes'])) { ?>
					<div class="notes"><?=printReady($song['notes'])?></div>
<?			} ?>
<?			if ($manageMusic) { ?>
					<div class="manageSong">
						<div><button type="submit" class="toggleApproval"><?=$song['approved']?'Unapprove':'Approve'?></button></div>
						<div><button type="submit" class="reject">Reject</button></div>
					</div>
<?			} ?>
				</li>
<?		} ?>
			</ul>
<?	} ?>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>