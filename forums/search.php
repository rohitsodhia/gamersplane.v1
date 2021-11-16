<? $responsivePage=true;
	addPackage('forum');

	$search = $_GET['search'];
	$forumSearch = new ForumSearch($search);
	$searchText = $_GET['q'];
	$gameID = $_GET['gameID'];
	$forumSearch->searchText($searchText, $gameID);
	$forumSearch->findThreads($_GET['page']);

	require_once(FILEROOT.'/header.php');
?>
	<?if($search=="text"){?>
		<h1 class="headerbar forumSearch"><?=$forumSearch->displayHeader();?><form class="forumSearchForm" method="get" action="/forums/search/?search=text"><input type="hidden" name="gameID" value="<?=$gameID?>"/><input type="hidden" name="search" value="text"/><input name="q" type="text" value="<?=htmlspecialchars($searchText)?>" placeholder="Search..."/></form></h1>
	<?}
	else{
		$forumSearch->displayHeader();
	}
	?>

		<p id="rules" class="mob-hide">Be sure to read and follow the <a href="/forums/rules/">guidelines for our forums</a>.</p>

		<div id="forumLinks">
			<div id="forumOptions">
			</div>
			<? $forumSearch->displayPagination(); ?>
			<br class="clear">
		</div>

<?=$forumSearch->displayResults()?>

		<div id="forumLinks">
			<div id="forumOptions">
			</div>
			<? $forumSearch->displayPagination(); ?>
			<br class="clear">
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>