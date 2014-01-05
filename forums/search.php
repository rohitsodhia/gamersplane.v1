<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	
	$search = $_GET['search'];
	if ($search == 'newPosts') {
		$checkPostsSince = $mysql->query('SELECT attemptStamp FROM loginRecords WHERE userID = '.$userID.' AND attemptStamp < NOW() - INTERVAL 3 HOUR ORDER BY attemptStamp DESC LIMIT 1');
		$checkPostsSince = $checkPostsSince->fetchColumn();
		if (strtotime('-3 Days') > strtotime($checkPostsSince)) $checkPostsSince = date('Y-m-d H:i:s', strtotime('-3 Days'));
		$showBy = 'threads';
		$searchResults = $mysql->query('SELECT t.threadID, t.forumID, ti.title tTitle, ti.datePosted, ti.authorID, u.username, f.title fTitle FROM threads t LEFT JOIN posts p USING (threadID) LEFT JOIN threads_relPosts rp USING (threadID) LEFT JOIN posts ti ON rp.firstPostID = ti.postID LEFT JOIN users u ON ti.authorID = u.userID LEFT JOIN forums f USING (forumID) LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = '.$userID.' LEFT JOIN forums_readData_forums_c rdf ON t.forumID = rdf.forumID AND rdf.userID = '.$userID.' LEFT JOIN ('.sql_forumPermissions($userID, 'read').') p ON t.forumID = p.forumID WHERE p.read = 1 AND p.postID > IFNULL(rdt.lastRead, 0) AND p.postID > rdf.cLastRead GROUP BY t.threadID ORDER BY rp.lastPostID DESC');
	} elseif ($search == 'latestPosts') {
		$checkPostsSince = $mysql->query('SELECT attemptStamp FROM loginRecords WHERE userID = '.$userID.' AND attemptStamp < NOW() - INTERVAL 3 HOUR ORDER BY attemptStamp DESC LIMIT 1');
		if ($checkPostsSince->rowCount()) {
			$checkPostsSince = $checkPostsSince->fetchColumn();
			if (strtotime('-3 Days') > strtotime($checkPostsSince)) $checkPostsSince = date('Y-m-d H:i:s', strtotime('-3 Days'));
		} else $checkPostsSince = date('Y-m-d H:i:s', strtotime('-3 Hour'));
		$showBy = 'threads';
		$searchResults = $mysql->query('SELECT t.threadID, t.forumID, f.title fTitle, t.locked, t.sticky, fp.title, fp.postID fp_postID, fp.datePosted fp_datePosted, fp.authorID fp_authorID, fpu.username fp_username, lp.postID lp_postID, lp.datePosted lp_datePosted, lp.authorID lp_authorID, lpu.username lp_username, pc.numPosts, np.newPosts FROM threads t LEFT JOIN posts p USING (threadID) LEFT JOIN threads_relPosts rp USING (threadID) LEFT JOIN posts fp ON rp.firstPostID = fp.postID LEFT JOIN users fpu ON fp.authorID = fpu.userID LEFT JOIN posts lp ON rp.lastPostID = lp.postID LEFT JOIN users lpu ON lp.authorID = lpu.userID LEFT JOIN (SELECT threadID, COUNT(*) numPosts FROM posts GROUP BY threadID) pc ON t.threadID = pc.threadID LEFT JOIN forums f USING (forumID) LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = '.$userID.' LEFT JOIN forums_readData_forums_c rdf ON t.forumID = rdf.forumID AND rdf.userID = '.$userID.' LEFT JOIN forums_readData_newPosts np ON t.threadID = np.threadID AND np.userID = '.$userID.' LEFT JOIN ('.sql_forumPermissions($userID, 'read').') p ON t.forumID = p.forumID WHERE p.read = 1 AND p.datePosted > NOW() - INTERVAL 1 YEAR GROUP BY t.threadID ORDER BY rp.lastPostID DESC');
	} else { header('Location: '.SITEROOT.'/forums'); exit; }
	
	$numItems = 0;
	if ($showBy == 'threads') {
		$threadContent = '		<div class="tableDiv threadTable">
			<div class="tr headerTR headerbar hbDark">
				<div class="td icon">&nbsp;</div>
				<div class="td threadInfo">Thread</div>
				<div class="td numPosts"># of Posts</div>
				<div class="td lastPost">Last Post</div>
			</div>
			<div class="sudoTable forumList hbdMargined">'."\n";
		
		if ($searchResults->rowCount()) {
			foreach ($searchResults as $threadInfo) {
				$threadInfo['fp_datePosted'] = switchTimezone($_SESSION['timezone'], $threadInfo['fp_datePosted']);
				$threadInfo['lp_datePosted'] = switchTimezone($_SESSION['timezone'], $threadInfo['lp_datePosted']);
				$forumIcon = $threadInfo['newPosts']?'new':'old';
				$threadContent .= '				<div class="tr">
					<div class="td icon"><div class="forumIcon'.($forumIcon == 'new'?' newPosts':'').'" title="'.($forumIcon == 'new'?'New':'No new').' posts in thread" alt="'.($forumIcon == 'new'?'New':'No new').' posts in thread"></div></div>
					<div class="td threadInfo">'."\n";
				if ($forumIcon == 'new') $threadContent .= "						<a href=\"".SITEROOT."/forums/thread/{$threadInfo['threadID']}?view=newPost\"><img src=\"".SITEROOT."/images/forums/newPost.png\" title=\"View new posts\" alt=\"View new posts\"></a>\n";
				if ($threadInfo['numPosts'] > PAGINATE_PER_PAGE) {
					$threadContent .= "						<div class=\"paginateDiv\">\n";
					$url = SITEROOT.'/forums/thread/'.$threadInfo['threadID'];
					$numPages = ceil($threadInfo['numPosts'] / PAGINATE_PER_PAGE);
					if ($numPages <= 4) for ($count = 1; $count <= $numPages; $count++) $threadContent .= "\t\t\t\t\t\t\t<a href=\"$url?page=$count\">$count</a>\n";
					else {
						$threadContent .= "\t\t\t\t\t\t\t<a href=\"$url?page=1\">1</a>\n";
						$threadContent .= "\t\t\t\t\t\t\t<div>...</div>\n";
						for ($count = ($numPages - 2); $count <= $numPages; $count++) $threadContent .= "\t\t\t\t\t\t\t<a href=\"$url?page=$count\">$count</a>\n";
					}
					$threadContent .= "\t\t\t\t\t\t</div>\n";
				}
				$threadContent .= "						<a href=\"".SITEROOT."/forums/thread/{$threadInfo['threadID']}\">{$threadInfo['title']}</a><br>
						<span class=\"threadAuthor\">by <a href=\"{SITEROOT}/ucp/{$threadInfo['fp_authorID']}\" class=\"username\">{$threadInfo['fp_username']}</a> on <span>".date('M j, Y g:i a', $threadInfo['fp_datePosted'])."</span></span>
					</div>
					<div class=\"td numPosts\">".($threadInfo['numPosts']?$threadInfo['numPosts']:0)."</div>
					<div class=\"td lastPost\">
						<a href=\"".SITEROOT."/ucp/{$threadInfo['lp_authorID']}\" class=\"username\">{$threadInfo['lp_username']}</a><br><span>".date('M j, Y g:i a', $threadInfo['lp_datePosted'])."</span>
					</div>
				</div>\n";
			}
			$numItems = $searchResults->rowCount();
		} else $threadContent .= "\t\t\t\t<div class=\"tr noThreads\">No threads yet</div>\n";
		$threadContent .= "			</div>
		</div>\n";
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Search Results</h1>
		
		<p id="rules">Be sure to read and follow the <a href="<?=SITEROOT?>/forums/rules">guidelines for our forums</a>.</p>
	
<?=$threadContent?>
				
		<div id="forumLinks">
			<div id="forumOptions">
			</div>
<? 
	if ($numItems > PAGINATE_PER_PAGE) {
		$spread = 2;
		echo "\t\t\t<div id=\"paginateDiv\" class=\"paginateDiv\">";
		$numPages = ceil($numItems / PAGINATE_PER_PAGE);
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
	}
 ?>
			<br class="clear">
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>