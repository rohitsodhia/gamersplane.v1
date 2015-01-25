<?
	require_once(FILEROOT.'/includes/forums/ForumManager.class.php');
	require_once(FILEROOT.'/includes/forums/ForumPermissions.class.php');
	require_once(FILEROOT.'/includes/forums/Forum.class.php');
	require_once(FILEROOT.'/includes/forums/Thread.class.php');

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
				<div><? if ($forumID == 0) echo '<a href="/forums/search?search=latestPosts">Latest Posts</a>'; ?></div>
				<div><? if ($forumManager->getForumProperty($forumID, 'permissions[admin]')) echo '<a href="/forums/acp/'.$forumID.'">Administrative Control Panel</a>'; ?></div>
			</div>
			<div class="floatLeft alignLeft">
<?	$forumManager->displayBreadcrumbs(); ?>
				<div>Be sure to read and follow the <a href="/forums/rules">guidelines for our forums</a>.</div>
			</div>
		</div>
<?
	$forumManager->displayForum();

	if ($forumID && $forumManager->getForumProperty($forumID, 'forumType') == 'f') {
		$forumManager->getThreads();
		$forumManager->displayThreads();
	}
?>
<?
/*	if ($forumID != 0 && $forumType != 'c') {
?>
		<div class="tableDiv threadTable<?=$firstTable?' firstTableDiv':''?>">
<?		if ($permissions[$forumID]['createThread']) { ?>
			<div id="newThread" class="clearfix"><a href="/forums/newThread/<?=$forumID?>" class="fancyButton">New Thread</a></div>
<? } ?>
			<div class="tr headerTR headerbar hbDark">
				<div class="td icon">&nbsp;</div>
				<div class="td threadInfo">Thread</div>
				<div class="td numPosts"># of Posts</div>
				<div class="td lastPost">Last Post</div>
			</div>
			<div class="sudoTable forumList hbdMargined">
<?
		if ($firstTable) $firstTable = FALSE;
		
		if ($threads->rowCount()) { foreach ($threads as $threadInfo) {
//			if (!isset($threadRD[$threadInfo['threadID']]) && $threadInfo['lp_postID'] > $markedRead) $threadRD[$threadInfo['threadID']] = array('forumID' => $forumID, 'lastRead' => 0, 'lastPost' => $threadInfo['lp_postID']);
//			elseif (isset($threadRD[$threadInfo['threadID']])) $threadRD[$threadInfo['threadID']]['lastPost'] = $threadInfo['lp_postID'];
			$forumIcon = ($threadInfo['lp_postID'] > $lastReadID && $threadInfo['lp_postID'] > $threadInfo['lastRead']) && $loggedIn?'new':'old';
?>
				<div class="tr">
					<div class="td icon"><div class="forumIcon<?=$threadInfo['sticky']?' sticky':''?><?=$forumIcon == 'new'?' newPosts':''?>" title="<?=$forumIcon == 'new'?'New':'No new'?> posts in thread" alt="<?=$forumIcon == 'new'?'New':'No new'?> posts in thread"></div></div>
					<div class="td threadInfo">
<?
			if ($forumIcon == 'new') {
?>
						<a href="/forums/thread/<?=$threadInfo['threadID']?>?view=newPost"><img src="/images/forums/newPost.png" title="View new posts" alt="View new posts"></a>
<?
			}
			if ($threadInfo['numPosts'] > PAGINATE_PER_PAGE) {
?>
						<div class="paginateDiv">
<?
				$url = '/forums/thread/'.$threadInfo['threadID'];
				$numPages = ceil($threadInfo['numPosts'] / PAGINATE_PER_PAGE);
				if ($numPages <= 4) for ($count = 1; $count <= $numPages; $count++) echo "\t\t\t\t\t\t\t<a href=\"$url?page=$count\">$count</a>\n";
				else {
					echo "\t\t\t\t\t\t\t<a href=\"$url?page=1\">1</a>\n";
					echo "\t\t\t\t\t\t\t<div>...</div>\n";
					for ($count = ($numPages - 2); $count <= $numPages; $count++) echo "\t\t\t\t\t\t\t<a href=\"$url?page=$count\">$count</a>\n";
				}
				echo "\t\t\t\t\t\t</div>\n";
			}
?>
						<a href="/forums/thread/<?=$threadInfo['threadID']?>"><?=$threadInfo['title']?></a><br>
						<span class="threadAuthor">by <a href="/ucp/<?=$threadInfo['fp_authorID']?>" class="username"><?=$threadInfo['fp_username']?></a> on <span class="convertTZ"><?=date('M j, Y g:i a', strtotime($threadInfo['fp_datePosted']))?></span></span>
					</div>
					<div class="td numPosts"><?=$threadInfo['numPosts']?$threadInfo['numPosts']:0?></div>
					<div class="td lastPost">
						<a href="/ucp/<?=$threadInfo['lp_authorID']?>" class="username"><?=$threadInfo['lp_username']?></a><br><span class="convertTZ"><?=date('M j, Y g:i a', strtotime($threadInfo['lp_datePosted']))?></span>
					</div>
				</div>
<?
		} } else echo "\t\t\t\t<div class=\"tr noThreads\">No threads yet</div>\n";
		echo "			</div>
		</div>\n";
	}
?>
				
		<div id="forumLinks">
			<div id="forumOptions">
<? if ($loggedIn) { ?>
				<a href="/forums/process/read/<?=$forumID?>/">Mark Forum As Read</a>
<? } ?>
			</div>
<? 
	if ($numThreads > PAGINATE_PER_PAGE) {
		$spread = 2;
		echo "\t\t\t<div id=\"paginateDiv\" class=\"paginateDiv\">";
		$numPages = ceil(numThreads / PAGINATE_PER_PAGE);
		if (isset($_GET['page'])) $currentPage = $_GET['page'];
		else $currentPage = 1;
		$firstPage = $currentPage - $spread;
		if ($firstPage < 1) $firstPage = 1;
		$lastPage = $currentPage + $spread;
		if ($lastPage > $numPages) $lastPage = $numPages;
		echo "\t\t\t<div class=\"currentPage\">$currentPage of $numPages</div>\n";
		if (($currentPage - $spread) > 1) echo "\t\t\t\t<a href=\"?page=1\">&lt;&lt; First</a>\n";
		if ($currentPage > 1) echo "\t\t\t\t<a href=\"?page=".($currentPage - 1)."\">&lt;</a>\n";
		for ($count = $firstPage; $count <= $lastPage; $count++) echo "\t\t\t\t<a href=\"?page=$count\"".(($count == $currentPage)?' class="currentPage"':'').">$count</a>\n";
		if ($currentPage < $numPages) echo "\t\t\t\t<a href=\"?page=".($currentPage + 1)."\">&gt;</a>\n";
		if (($currentPage + $spread) < $numPages) echo "\t\t\t\t<a href=\"?page=$numPages\">Last &gt;&gt;</a>\n";
		echo "\t\t\t</div>\n";
	}*/
 ?>
			<br class="clear">
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>