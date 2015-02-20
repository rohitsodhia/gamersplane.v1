<?
	addPackage('forum');

	$forumID = intval($pathOptions[0]);
	$forumManager = new ForumManager($forumID);

	if (!$forumManager->displayCheck()) { header('Location: /forums'); exit; }
	
/*	if ($currentUser->userID) {
		// Get lastRead for current forum; if none, create
		$cLastRead = $mysql->query("SELECT cLastRead FROM forums_readData_forums_c WHERE forumID = $forumID AND userID = $currentUser->userID");
		if ($cLastRead->rowCount()) $lastReadID = $cLastRead->fetchColumn();
		else {
			$lastReadID = $mysql->query('SELECT MAX(postID) FROM posts');
			$lastReadID = $lastReadID->fetchColumn();
			$mysql->query("INSERT INTO forums_readData_forums (userID, forumID, lastRead) VALUES ($currentUser->userID, $forumID, $lastReadID)");
		}

		// Check if admin of current forum
		$forumAdmin = $mysql->query("SELECT forumID FROM forumAdmins WHERE userID = {$currentUser->userID} AND forumID IN (0".(($forumID != 0)?', '.implode(', ', $heritage):'').')');
		$forumAdmin = $forumAdmin->rowCount()?TRUE:FALSE;
	} else {
		$forumAdmin = false;
	}*/
	
	if ($forumManager->getForumProperty($forumID, 'gameID')) {
		$gameID = $forumManager->getForumProperty($forumID, 'gameID');
		$fixedGameMenu = true;
	} else $gameID = false;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Forum<?=$forumID?' - '.$forumManager->getForumProperty($forumID, 'title'):'s'?></h1>
		
		<div id="topLinks" class="clearfix hbMargined">
			<div class="floatRight alignRight">
				<div><? if ($forumID == 0) echo '<a href="/forums/search?search=latestPosts">Unread Posts</a>'; ?></div>
				<div><? if ($forumManager->getForumProperty($forumID, 'permissions[admin]')) echo "<a href=\"/forums/acp/{$forumID}/\">Administrative Control Panel</a>"; ?></div>
			</div>
			<div class="floatLeft alignLeft">
<?	$forumManager->displayBreadcrumbs(); ?>
				<div>Be sure to read and follow the <a href="/forums/rules">guidelines for our forums</a>.</div>
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
<? if ($loggedIn) { ?>
				<a href="/forums/process/read/<?=$forumID?>/">Mark Forum As Read</a>
<? } ?>
			</div>
<?	ForumView::displayPagination($forumManager->getForumProperty($forumID, 'threadCount'), $_GET['page']); ?>
			<br class="clear">
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>