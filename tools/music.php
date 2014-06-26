<?
	$loggedIn = checkLogin(0);
	require_once(FILEROOT.'/includes/tools/Music_consts.class.php');
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Music and Clips</h1>
		<div class="hbMargined">
			<div class="sideWidget left">
				<h2>Filter by Genre</h2>
				<form method="get">
					<ul class="clearfix">
<?
	foreach (Music_consts::getGameTypes() as $type) {
		$cleanType = preg_replace('/[^A-za-z]/', ' ', $cleanType);
?>
						<li><input id="genre_<?=$cleanType?>" type="checkbox" name="genre[<?=$type?>]"> <label for="genre_<?=$cleanType?>"><?=$type?></label>
<?	} ?>
					</ul>
				</form>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>