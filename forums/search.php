<? $responsivePage=true;
	addPackage('forum');

	$search = $_GET['search'];
	$forumSearch = new ForumSearch($search);
	$forumSearch->findThreads($_GET['page']);

	require_once(FILEROOT.'/header.php');
?>
	<?=$forumSearch->displayHeader()?>

		<p id="rules" class="mob-hide">Be sure to read and follow the <a href="/forums/rules/">guidelines for our forums</a>.</p>

<?=$forumSearch->displayResults()?>

		<div id="forumLinks">
			<div id="forumOptions">
			</div>
<?	ForumView::displayPagination($forumSearch->getResultsCount(), $forumSearch->getPage(), array('search' => $search)); ?>
			<br class="clear">
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>