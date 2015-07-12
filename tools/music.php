<?
	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">Music and Clips</h1>

		<div id="info" class="hbMargined">
			<p>Taking a tabletop game to another level is as simple as setting the mood. Eerie winds blowing through a forest, upbeat tempos when the group catches the boss, light techno as the runners make way way through the tunnels. Add that extra depth that puts players at ease or off tilt.</p>
			<p>If you're looking for that little edge to bring your game over the top, find it here! If you know a song or audio clip that works perfectly in a game, share it with everyone else. We're currently only accepting YouTube and SoundCloud links. If you have another service you think we should be using, <a href="/contact/">get in touch</a>.</p>
		</div>

		<div class="sideWidget left">
			<h2>Filter</h2>
			<form method="get">
				<h3>Genre</h3>
				<ul class="clearfix">
					<li ng-repeat="genre in genres">
						<label>
							<pretty-checkbox linked-array="filter.genres" linked-key="{{genre}}"></pretty-checkbox>
							<div class="labelText">{{genre}}</div>
						</label>
					</li>
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
		if (isset($_GET['filter'], $_GET['lyrics']['has'])) 
			$filter['lyrics'] = true;
		elseif (isset($_GET['filter'], $_GET['lyrics']['none'])) 
			$filter['lyrics'] = false;
	}
	$result = $mongo->music->find(array_merge($filter, array('approved' => true)))->sort(array('genres' => 1, 'title' => 1));
	if (sizeof($result)) {
?>
			<ul class="hbAttachedList">
<?		foreach ($result as $song) { ?>
				<li<?=!$song['approved']?' class="unapproved"':''?> data-id="<?=$song['_id']?>">
					<div class="clearfix">
						<a href="<?=$song['url']?>" target="_blank" class="song"><?=$song['title']?><?=$song['lyrics']?'<img src="/images/tools/quote.png" title="Has Lyrics" alt="Has Lyrics">':''?></a
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
<?	require_once(FILEROOT.'/footer.php'); ?>