<?
	$responsivePage=true;
	addPackage('forum');

	$forumID = intval($pathOptions[0]);
	$forumManager = new ForumManager($forumID);

	if (!$forumManager->displayCheck()) { header('Location: /forums'); exit; }

	if ($forumManager->getForumProperty($forumID, 'gameID')) {
		$gameID = $forumManager->getForumProperty($forumID, 'gameID');
		$fixedGameMenu = true;
	} else
		$gameID = false;

	if ($forumID)
		$dispatchInfo['title'] = $forumManager->getForumProperty($forumID, 'title').' | '.$dispatchInfo['title'];
?>
<?	require_once(FILEROOT.'/header.php'); ?>
<?	if ($forumID == 0 || $gameID) { ?>
		<h1 class="headerbar forumSearch"><span class="searchTitle"><?$forumManager->addForumIcon($forumID)?> <?=$forumID?$forumManager->getForumProperty($forumID, 'title'):'Forums'?></span><form class="forumSearchForm" method="get" action="/forums/search/?search=text"><input type="hidden" name="search" value="text"/><input type="hidden" name="gameID" value="<?=$gameID?>"/><input name="q" type="text" placeholder="Search..."/></form></h1>
<?	} else { ?>
	<h1 class="headerbar"><?$forumManager->addForumIcon($forumID)?> <?=$forumID?$forumManager->getForumProperty($forumID, 'title'):'Forums'?></h1>
<?	}?>

		<div id="topLinks" class="hbMargined">
<?	$forumManager->displayBreadcrumbs(); ?>
			<div class="flexWrapper">
				<div class="alignLeft">
					<div class="mob-hide">Be sure to read and follow the <a href="/forums/rules">guidelines for our forums</a>.</div>
				</div>
				<div class="alignRight">
<?	if ($forumID == 0) { ?>
					<div><a href="/forums/search/?search=latestPosts">Latest Posts</a>/<a href="/forums/search/?search=unreadPosts">Unread Posts</a></div>
					<div><a href="/forums/search/?search=latestGamePosts">Latest Game Posts</a></div>
					<div><a href="/forums/search/?search=latestPublicPosts">Latest in Public Games</a></div>
<?	} ?>
					<div><? if ($forumManager->getForumProperty($forumID, 'permissions[admin]')) echo "<a href=\"/forums/acp/{$forumID}/\">Administrative Control Panel</a>"; ?></div>
				</div>
			</div>
		</div>
<?
	$forumManager->displayForum();

	if ($forumID && $forumManager->getForumProperty($forumID, 'forumType') == 'f') {
		$forumManager->getThreads($_GET['page']);
		$forumManager->displayThreads();
	}
?>

		<div id="forumLinks">
			<div id="forumOptions">
<?	if ($loggedIn) { ?>
				<p><a href="/forums/process/read/<?=$forumID?>/">Mark Forum As Read</a></p>
<?
	}
	if ($forumID) {
		$isSubbed = $mysql->query("SELECT userID FROM forumSubs WHERE userID = {$currentUser->userID} AND type = 'f' AND ID = {$forumID}");
?>
				<p><a id="forumSub" href="/forums/process/subscribe/?forumID=<?=$forumID?>"><?=$isSubbed->rowCount()?'Unsubscribe from':'Subscribe to'?> forum</a></p>
<?
	}
	if ($loggedIn) {
?>
				<p><a href="/forums/subscriptions/">Manage Subscriptions</a></p>
<?	} ?>
			</div>
<?	ForumView::displayPagination($forumManager->getForumProperty($forumID, 'threadCount'), $_GET['page'], array()); ?>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>