<?
	$forumID = intval($pathOptions[0]);
	$forumType = 'c';
	$heritage = array();
	
	// Get current title and type
	if ($forumID != 0) {
		$forumInfo = $mysql->query('SELECT title, forumType, heritage FROM forums WHERE forumID = '.$forumID);
		list($forumTitle, $forumType, $heritage) = $forumInfo->fetch(PDO::FETCH_NUM);
		$heritage = explode('-', $heritage);
		foreach ($heritage as $key => $hForumID) {
			$hForumID = intval($hForumID);
			$heritage[$key] = $hForumID;
		}
	}
	
	// If not root, get current permissions
	if ($forumID != 0) {
		$permissions = retrievePermissions($currentUser->userID, $forumID, array('read', 'moderate', 'createThread'));
		if ($permissions[$forumID]['read'] == 0) { header('Location: /forums'); exit; }
	}
	
	if ($currentUser->userID) {
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
	}
	
	// Get children
	$forumStructure = array();
	$forumInfos = $mysql->query("SELECT forumID, forumType FROM forums WHERE parentID = $forumID ORDER BY `order`");
	if ($forumInfos->rowCount()) {
		$forumIDs = array();
		$categoryIDs = array();
		foreach ($forumInfos as $forumInfo) {
			if ($forumInfo['forumType'] == 'c') {
				$categoryIDs[] = $forumInfo['forumID'];
				$forumStructure[$forumInfo['forumID']] = array('type' => 'c', 'children' => array());
			} else {
				$forumIDs[] = $forumInfo['forumID'];
				$forumStructure[$forumInfo['forumID']] = array('type' => 'f');
			}
		}

		if (sizeof($categoryIDs) > 0) {
			$forumInfos = $mysql->query('SELECT forumID, parentID FROM forums WHERE parentID IN ('.implode(', ', $categoryIDs).') ORDER BY `order`');
			foreach ($forumInfos as $forumInfo) {
				$forumIDs[] = $forumInfo['forumID'];
				$forumStructure[$forumInfo['parentID']]['children'][$forumInfo['forumID']] = array('type' => 'f');
//				if (!isset($forumRD[$forumInfo['forumID']])) $forumRD[$forumInfo['forumID']] = 0;
			}
		}
		if ($forumID != 0 && $forumType == 'f') $forumIDs[] = $forumID;

 		$permissions = retrievePermissions($currentUser->userID, $forumIDs, array('read', 'moderate', 'createThread'));
		foreach ($forumStructure as $iForumID => $forumInfo) {
			if ($forumInfo['type'] == 'c') {
				foreach ($forumInfo['children'] as $cForumID => $cInfo) if (!$permissions[$cForumID]['read']) unset($forumStructure[$iForumID]['children'][$cForumID], $forumIDs[array_search($cForumID, $forumIDs)]);
				if (!sizeof($forumStructure[$iForumID]['children'])) unset($forumStructure[$iForumID], $categoryIDs[array_search($iForumID, $categoryIDs)]);
			} elseif (!$permissions[$iForumID]['read']) unset($forumStructure[$iForumID], $forumIDs[array_search($iForumID, $forumIDs)]);
		}

//		if ($forumType == 'c' && (!sizeof($forumIDs) && !sizeof($categoryIDs))) { header('Location: /403'); exit; }
		
		$queryWhere = '';
		foreach (array_merge($forumIDs, $categoryIDs) as $lForumID) $queryWhere .= 'forums.heritage LIKE "%'.sql_forumIDPad($lForumID).'%" OR ';
		$forumInfos = array();
		$indivLatestPosts = array();
		$rForumInfos = $mysql->query("SELECT forums.forumID, forums.title, forums.description, forums.heritage, threadCount.numThreads, numPosts.numPosts, latestPosts.postID lpPostID, latestPosts.datePosted, latestPosts.authorID, latestPosts.username, IF(newPosts.forumID IS NOT NULL AND threadCount.numThreads, IFNULL(newPosts.newPosts,1), 0) newPosts FROM forums LEFT JOIN (SELECT forumID, COUNT(threadID) numThreads FROM threads GROUP BY forumID) AS threadCount ON threadCount.forumID = forums.forumID LEFT JOIN (SELECT threads.forumID, COUNT(*) numPosts FROM posts, threads WHERE posts.threadID = threads.threadID GROUP BY threads.forumID) numPosts ON forums.forumID = numPosts.forumID LEFT JOIN (SELECT lastPost.forumID, lastPost.postID, lastPost.authorID, users.username, lastPost.datePosted FROM (SELECT forumID, postID, authorID, datePosted FROM (SELECT postID, forumID, authorID, datePosted FROM posts, threads WHERE posts.threadID = threads.threadID ORDER BY posts.datePosted DESC) lastPost GROUP BY forumID) lastPost, users WHERE users.userID = lastPost.authorID) AS latestPosts ON forums.forumID = latestPosts.forumID LEFT JOIN (SELECT forumID, newPosts FROM forums_readData_newPosts WHERE userID = ".($currentUser->userID?$currentUser->userID:0)." GROUP BY forumID) newPosts ON forums.forumID = newPosts.forumID WHERE ".substr($queryWhere, 0, -4).' AND forums.forumID != '.$forumID.' ORDER BY forums.heritage');
		foreach ($rForumInfos as $forumInfo) {
			$indivLatestPosts[$forumInfo['forumID']] = $forumInfo['lpPostID'];
			$forumInfos[$forumInfo['forumID']] = $forumInfo;
		}
		
/*		$children = array();
		$hcInfos = $mysql->query('SELECT p.forumID pID, c.forumID cID FROM forums p LEFT JOIN forums c ON c.heritage LIKE CONCAT(p.heritage, "-%") WHERE p.forumID IN ('.implode(', ', array_merge($forumIDs, $categoryIDs)).')');
		foreach ($hcInfos as $hcInfo) {
			$children[$hcInfo['pID']][] = $hcInfo['cID'];
		}*/
	}
	
	if ($forumID != 0 && $forumType != 'c') {
		$numThreads = $mysql->query('SELECT COUNT(*) FROM threads WHERE forumID = '.$forumID);
		$numThreads = $numThreads->fetchColumn();
		$threads = $mysql->query("SELECT threads.threadID, threads.locked, threads.sticky, first.title, first.postID fp_postID, first.datePosted fp_datePosted, first.authorID fp_authorID, tAuthor.username fp_username, last.postID lp_postID, last.datePosted lp_datePosted, last.authorID lp_authorID, lAuthor.username lp_username, postCount.numPosts, IFNULL(rd.lastRead, 0) lastRead FROM threads INNER JOIN threads_relPosts relPosts ON relPosts.threadID = threads.threadID INNER JOIN posts first ON relPosts.firstPostID = first.postID INNER JOIN posts last ON relPosts.lastPostID = last.postID INNER JOIN users tAuthor ON first.authorID = tAuthor.userID INNER JOIN users lAuthor ON last.authorID = lAuthor.userID LEFT JOIN (SELECT threadID, COUNT(*) AS numPosts FROM posts GROUP BY threadID) postCount ON threads.threadID = postCount.threadID LEFT JOIN forums_readData_threads rd ON threads.threadID = rd.threadID AND rd.userID = {$currentUser->userID} WHERE threads.forumID = {$forumID} ORDER BY threads.sticky DESC, last.datePosted DESC");
	}
	
	$gameID = FALSE;
	$isGM = FALSE;
	if ($heritage[0] == 2) {
		$gameInfo = $mysql->query("SELECT gameID FROM games WHERE forumID = ".intval($heritage[1]));
		$gameID = $gameInfo->fetchColumn();
		$fixedGameMenu = TRUE;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Forum<?=$forumID?' - '.$forumTitle/*.($forumInfo['gameID']?' (Game)':'')*/:'s'?></h1>
		
		<div id="topLinks" class="clearfix hbMargined">
			<div class="floatRight alignRight">
				<div><? if ($forumID == 0) echo '<a href="/forums/search?search=latestPosts">Latest Posts</a>'; ?></div>
				<div><? if ($forumAdmin) echo '<a href="/forums/acp/'.$forumID.'">Administrative Control Panel</a>'; ?></div>
			</div>
			<div class="floatLeft alignLeft">
				<div id="breadcrumbs">
<? if ($forumID != 0) { ?>
					<a href="/forums">Index</a><?=$forumID != 0?' > ':''?>
<?
		$breadcrumbs = $mysql->query('SELECT forumID, title FROM forums WHERE forumID IN ('.implode(',', $heritage).')');
		$breadcrumbForums = array();
		foreach ($breadcrumbs as $forumInfo) $breadcrumbForums[$forumInfo['forumID']] = printReady($forumInfo['title']);
		$fCounter = 1;
		foreach ($heritage as $hForumID) {
			echo "\t\t\t\t\t<a href=\"/forums/{$hForumID}\">{$breadcrumbForums[$hForumID]}</a>".($fCounter != sizeof($heritage)?' > ':'')."\n";
			$fCounter++;
		}
	} else echo "\t\t\t\t\t&nbsp;\n";
?>
				</div>
				<div>Be sure to read and follow the <a href="/forums/rules">guidelines for our forums</a>.</div>
			</div>
		</div>
<?
	$forumIcon = '';
	
	if (sizeof($forumStructure)) {
		$curCat = '';
		$tableOpen = FALSE;
		foreach ($forumStructure as $iForumID => $info) {
			if ($info['type'] == 'c' && sizeof($info['children'])) {
				if ($tableOpen) {
					$tableOpen = FALSE;
					echo "\t\t\t</div>\n\t\t</div>\n";
				}
?>
		<div class="tableDiv">
			<div class="clearfix"><h2 class="wingDiv redWing">
				<div><?=($forumID == 0)?$forumInfos[$iForumID]['title']:'Subforums'?></div>
				<div class="wing dlWing"></div>
				<div class="wing drWing"></div>
			</h2></div>
			<div class="tr headerTR headerbar hbDark">
				<div class="td icon">&nbsp;</div>
				<div class="td name">Forum</div>
				<div class="td numThreads"># of Threads</div>
				<div class="td numPosts"># of Posts</div>
				<div class="td lastPost">Last Post</div>
			</div>
			<div class="sudoTable forumList hbdMargined">
<?
				if ($firstTable) $firstTable = FALSE;
				foreach ($info['children'] as $cForumID => $cInfo) {
					$forumInfo = $forumInfos[$cForumID];
					$cHeritage = explode('-', $forumInfo['heritage']);
					foreach ($cHeritage as $key => $hForumID) $cHeritage[$key] = intval($hForumID);
//					$forumIcon = checkNewPosts_new($cForumID, $readData, $permissionsList, $children[$cForumID])?'new':'old';
					$forumIcon = $forumInfo['newPosts']?'new':'old';
?>
				<div class="tr<?=$forumInfo['numPosts']?'':' noPosts'?>">
					<div class="td icon"><div class="forumIcon<?=$forumIcon == 'new'?' newPosts':''?>" title="<?=$forumIcon == 'new'?'New':'No new'?> posts in forum" alt="<?=$forumIcon == 'new'?'New':'No new'?> posts in forum"></div></div>
					<div class="td name">
						<a href="/forums/<?=$forumInfo['forumID']?>"><?=printReady($forumInfo['title'])?></a>
<?=($forumInfo['description'] != '')?"\t\t\t\t\t\t<div class=\"description\">".printReady($forumInfo['description'])."</div>\n":''?>
					</div>
					<div class="td numThreads"><?=$forumInfo['numThreads']?$forumInfo['numThreads']:0?></div>
					<div class="td numPosts"><?=$forumInfo['numPosts']?$forumInfo['numPosts']:0?></div>
					<div class="td lastPost">
<?
					if ($forumInfo['username']) echo "\t\t\t\t\t\t<a href=\"/ucp/{$forumInfo['authorID']}\" class=\"username\">{$forumInfo['username']}</a><br><span class=\"convertTZ\">".date('M j, Y g:i a', strtotime($forumInfo['datePosted']))."</span>\n";
					else echo "\t\t\t\t\t\t</span>No Posts Yet!</span>\n";
?>
					</div>
				</div>
<?
				}
				echo "\t\t\t</div>\n\t\t</div>\n";
			} elseif ($info['type'] == 'f') {
				if (!$tableOpen) {
					$tableOpen = TRUE;
?>
		<div class="tableDiv">
			<div class="clearfix"><h2 class="wingDiv redWing">
				<div><?=($forumID == 0)?$forumInfos[$iForumID]['title']:'Subforums'?></div>
				<div class="wing dlWing"></div>
				<div class="wing drWing"></div>
			</h2></div>
			<div class="tr headerTR headerbar hbDark">
				<div class="td icon">&nbsp;</div>
				<div class="td name">Forum</div>
				<div class="td numThreads"># of Threads</div>
				<div class="td numPosts"># of Posts</div>
				<div class="td lastPost">Last Post</div>
			</div>
			<div class="sudoTable forumList hbdMargined">
<?
					if ($firstTable) $firstTable = FALSE;
				}
				$forumInfo = $forumInfos[$iForumID];
				$fHeritage = explode('-', $forumInfo['heritage']);
				foreach ($fHeritage as $key => $hForumID) $fHeritage[$key] = intval($hForumID);
//				$forumIcon = checkNewPosts_new($iForumID, $readData, $permissionsList, $children[$iForumID])?'new':'old';
				$forumIcon = $forumInfo['newPosts']?'new':'old';
?>
				<div class="tr<?=$forumInfo['numPosts']?'':' noPosts'?>">
					<div class="td icon"><div class="forumIcon<?=$forumIcon == 'new'?' newPosts':''?>" title="<?=$forumIcon == 'new'?'New':'No new'?> posts in forum" alt="<?=$forumIcon == 'new'?'New':'No new'?> posts in forum"></div></div>
					<div class="td name">
						<a href="/forums/<?=$forumInfo['forumID']?>"><?=printReady($forumInfo['title'])?></a>
<?=($forumInfo['description'] != '')?"\t\t\t\t\t\t<div class=\"description\">".printReady($forumInfo['description'])."</div>\n":''?>
					</div>
					<div class="td numThreads"><?=$forumInfo['numThreads']?$forumInfo['numThreads']:0?></div>
					<div class="td numPosts"><?=$forumInfo['numPosts']?$forumInfo['numPosts']:0?></div>
					<div class="td lastPost">
<?
					if ($forumInfo['username']) echo "\t\t\t\t\t\t<a href=\"/ucp/{$forumInfo['authorID']}\" class=\"username\">{$forumInfo['username']}</a><br><span class=\"convertTZ\">".date('M j, Y g:i a', strtotime($forumInfo['datePosted']))."</span>\n";
					else echo "\t\t\t\t\t\t</span>No Posts Yet!</span>\n";
?>
					</div>
				</div>
<?
			}
		}
	}
	if ($tableOpen) echo "\t\t\t</div>\n\t\t</div>\n";
?>
		
<?
	if ($forumID != 0 && $forumType != 'c') {
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
	}
 ?>
			<br class="clear">
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>