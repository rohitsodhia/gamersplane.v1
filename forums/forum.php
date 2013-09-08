<?
	$loggedIn = checkLogin(0);
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($pathOptions[0]);
	$forumType = 'c';
	
	if ($forumID != 0) {
		$forumInfo = $mysql->query('SELECT title, forumType FROM forums WHERE forumID = '.$forumID);
		list($forumTitle, $forumType) = $forumInfo->fetch();
	}
	
//	$permissions = retrievePermissions(array('read', 'moderate', 'createThread'), $userID, $forumID);
	if ($forumID != 0) {
		$permissions = retrievePermissions($userID, $forumID, array('read', 'moderate', 'createThread'));
		if ($permissions[$forumID]['read'] == 0) { header('Location: '.SITEROOT.'/forums'); exit; }
	}
	
	$forumRD = array();
/*	$mysql->query('SELECT forumData, threadData FROM forums_readData WHERE userID = '.$userID);
	if ($mysql->rowCount()) {
		list($forumRD, $threadRD) = $mysql->getList();
		$forumRD = unserialize($forumRD);
		$threadRD = unserialize($threadRD);
		if (!is_array($threadRD)) $threadRD = array();
	} else {
		$mysql->query("INSERT INTO forums_readData (userID) VALUES ($userID)");
		$mysql->query('SELECT MAX (postID) FROM posts');
		list($maxPostID) = $mysql->getList();
		$threadRD = array();
	}
	$markedRead = $forumRD[0];*/
	$cLastRead = $mysql->query('SELECT cLastRead FROM forums_readData_forums_c WHERE forumID = '.$forumID.' AND userID = '.$userID);
	if ($cLastRead->rowCount()) $markedRead = $cLastRead->fetchColumn();
	else {
		$maxPostID = $mysql->query('SELECT MAX(postID) FROM posts');
		$maxPostID = $maxPostID->fetchColumn();
		$forumRD = array(0 => $maxPostID);
		$mysql->query("INSERT INTO forums_readData_forums (userID, forumID, lastRead) VALUES ($userID, 0, $maxPostID)");
	}
	
	$heritage = $mysql->query('SELECT heritage FROM forums WHERE forumID = '.$forumID);
	$heritage = $heritage->fetchColumn();
	if ($heritage != '') $heritage = explode('-', $heritage);
	else $heritage = array();
	foreach ($heritage as $key => $hForumID) {
		$hForumID = intval($hForumID);
		$heritage[$key] = $hForumID;
		if (!isset($forumRD[$hForumID])) $forumRD[$hForumID] = 0;
		if ($forumRD[$hForumID] > $markedRead) $markedRead = $forumRD[$hForumID];
	}
	$forumAdmin = $mysql->query('SELECT forumID FROM forumAdmins WHERE userID = '.$userID.' AND forumID IN (0'.(($forumID != 0)?', '.implode(', ', $heritage):'').')');
	$forumAdmin = $forumAdmin->rowCount()?TRUE:FALSE;
	
	$forumInfos = $mysql->query('SELECT forumID, forumType FROM forums WHERE parentID = '.$forumID.' ORDER BY `order`');
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
//			if (!isset($forumRD[$forumInfo['forumID']])) $forumRD[$forumInfo['forumID']] = 0;
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
		
		$permissions = retrievePermissions($userID, $forumIDs, array('read', 'moderate', 'createThread'));
		foreach ($forumStructure as $iForumID => $forumInfo) {
			if ($forumInfo['type'] == 'c') {
				foreach ($forumInfo['children'] as $cForumID => $cInfo) if (!$permissions[$cForumID]['read']) unset($forumStructure[$iForumID]['children'][$cForumID], $forumIDs[array_search($cForumID, $forumIDs)]);
				if (!sizeof($forumStructure[$iForumID]['children'])) unset($forumStructure[$iForumID], $categoryIDs[array_search($iForumID, $categoryIDs)]);
			} elseif (!$permissions[$iForumID]['read']) unset($forumStructure[$iForumID], $forumIDs[array_search($iForumID, $forumIDs)]);
		}
		
		if ($forumID != 0 && (($forumType == 'f' && $permissions[$forumID]['read'] == 0) || ($forumType == 'c' && (!sizeof($forumIDs) && !sizeof($categoryIDs))))) { header('Location: '.SITEROOT.'/403'); exit; }
		
		$queryWhere = '';
		foreach (array_merge($forumIDs, $categoryIDs) as $lForumID) $queryWhere .= 'forums.heritage LIKE "%'.str_pad($lForumID, HERITAGE_PAD, 0, STR_PAD_LEFT).'%" OR ';
		
		$forumInfos = array();
		$indivLatestPosts = array();
		$permissionsList = array($forumID => array('read' => 1));
		$rForumInfos = $mysql->query('SELECT forums.forumID, forums.title, forums.description, forums.heritage, threadCount.numThreads, numPosts.numPosts, latestPosts.postID lpPostID, latestPosts.datePosted, latestPosts.authorID, latestPosts.username FROM forums LEFT JOIN (SELECT forumID, COUNT(threadID) numThreads FROM threads GROUP BY forumID) AS threadCount ON threadCount.forumID = forums.forumID LEFT JOIN (SELECT threads.forumID, COUNT(*) numPosts FROM posts, threads WHERE posts.threadID = threads.threadID GROUP BY threads.forumID) numPosts ON forums.forumID = numPosts.forumID LEFT JOIN (SELECT lastPost.forumID, lastPost.postID, lastPost.authorID, users.username, lastPost.datePosted FROM (SELECT forumID, postID, authorID, datePosted FROM (SELECT postID, forumID, authorID, datePosted FROM posts, threads WHERE posts.threadID = threads.threadID ORDER BY posts.datePosted DESC) lastPost GROUP BY forumID) lastPost, users WHERE users.userID = lastPost.authorID) AS latestPosts ON forums.forumID = latestPosts.forumID WHERE '.substr($queryWhere, 0, -4).' AND forums.forumID != '.$forumID.' ORDER BY forums.heritage');
		foreach ($rForumInfos as $forumInfo) {
			$indivLatestPosts[$forumInfo['forumID']] = $forumInfo['lpPostID'];
			if (in_array($forumInfo['forumID'], array_merge($forumIDs, $categoryIDs))) {
				$forumInfos[$forumInfo['forumID']] = $forumInfo;
				$permissionsList[$forumInfo['forumID']] = array('read' => 1);
			} else {
				$permissionsList[$forumInfo['forumID']] = retrievePermissions($userID, $forumInfo['forumID'], 'read', TRUE);
				if ($permissionsList[$forumInfo['forumID']]['read']) {
					foreach ($forumInfos as $cForumID => $cForumInfo) { if (strpos($forumInfo['heritage'], $cForumInfo['heritage']) !== FALSE && in_array($cForumID, $forumIDs)) {
						if ($forumInfo['lpPostID'] > $cForumInfo['lpPostID'] || $cForumInfo['lpPostID'] == NULL) {
							$forumInfos[$cForumID]['lpPostID'] = $forumInfo['lpPostID'];
							$forumInfos[$cForumID]['datePosted'] = $forumInfo['datePosted'];
							$forumInfos[$cForumID]['authorID'] = $forumInfo['authorID'];
							$forumInfos[$cForumID]['username'] = $forumInfo['username'];
						}
						$forumInfos[$cForumID]['numThreads'] += $forumInfo['numThreads'];
						$forumInfos[$cForumID]['numPosts'] += $forumInfo['numPosts'];
						break;
					} }
				}
			}
		}
		
		$children = array();
		$hcInfos = $mysql->query('SELECT p.forumID pID, c.forumID cID FROM forums p LEFT JOIN forums c ON c.heritage LIKE CONCAT(p.heritage, "-%") WHERE p.forumID IN ('.implode(', ', array_merge($forumIDs, $categoryIDs)).')');
		foreach ($hcInfos as $hcInfo) {
			$children[$hcInfo['pID']][] = $hcInfo['cID'];
		}
		
		$readData = array();
		$rdInfos = $mysql->query("SELECT f.forumID, IF(MAX(np.forumID) IS NOT NULL, 1, 0) newPosts FROM forums f LEFT JOIN forums c ON c.heritage LIKE CONCAT(f.heritage, '%') LEFT JOIN (SELECT forumID, userID FROM forums_readData_newPosts WHERE userID = $userID GROUP BY forumID) np ON c.forumID = np.forumID WHERE f.forumID IN (".implode(', ', $forumIDs).') GROUP BY f.forumID');
//		$mysql->query('SELECT f.forumID, IF(np.forumID IS NOT NULL, 1, 0) newPosts FROM forums f LEFT JOIN forums_readData_forums_c rdf ON f.forumID = rdf.forumID LEFT JOIN (SELECT t.forumID, MAX(r.lastPostID) lastPostID FROM threads t INNER JOIN threads_relPosts r USING (threadID) GROUP BY t.forumID) lp ON f.forumID = lp.forumID LEFT JOIN (SELECT forumID, userID FROM forums_readData_newPosts WHERE userID = '.$userID.' GROUP BY forumID) np ON f.forumID = np.forumID LEFT JOIN (SELECT t.forumID, MAX(rdt.lastRead) lastPostRead FROM threads t INNER JOIN threads_relPosts r USING (threadID) INNER JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = 2 GROUP BY t.forumID) lpr ON f.forumID = lpr.forumID WHERE rdf.userID = '.$userID.' AND f.forumID IN ('.implode(', ', array_merge($forumIDs, $categoryIDs)).')');
		foreach ($rdInfos as $rdInfo) $readData[$rdInfo['forumID']] = $rdInfo['newPosts'];
	}
	
	$threadsContent = '';
	if ($forumID != 0 && $forumType != 'c') {
		$numThreads = $mysql->query('SELECT COUNT(*) FROM threads WHERE forumID = '.$forumID);
		$numThreads = $numThreads->fetchColumn();
		$threads = $mysql->query('SELECT threads.threadID, threads.locked, threads.sticky, first.title, first.postID fp_postID, first.datePosted fp_datePosted, first.authorID fp_authorID, tAuthor.username fp_username, last.postID lp_postID, last.datePosted lp_datePosted, last.authorID lp_authorID, lAuthor.username lp_username, postCount.numPosts, IFNULL(rd.lastRead, 0) lastRead FROM threads INNER JOIN threads_relPosts relPosts ON relPosts.threadID = threads.threadID INNER JOIN posts first ON relPosts.firstPostID = first.postID INNER JOIN posts last ON relPosts.lastPostID = last.postID INNER JOIN users tAuthor ON first.authorID = tAuthor.userID INNER JOIN users lAuthor ON last.authorID = lAuthor.userID LEFT JOIN (SELECT threadID, COUNT(*) AS numPosts FROM posts GROUP BY threadID) postCount ON threads.threadID = postCount.threadID LEFT JOIN forums_readData_threads rd ON threads.threadID = rd.threadID AND rd.userID = '.$userID.' WHERE threads.forumID = '.$forumID.' ORDER BY threads.sticky DESC, last.datePosted DESC');
		
		$threadContent .= '		<div class="tableDiv threadTable'.($firstTable?' firstTableDiv':'').'">
			<h2 class="title"><div>Threads</div></h2>
			<table id="threadList">
				<tr>
					<td class="forumIcon">&nbsp;</td>
					<th class="threadName">Thread</th>
					<th class="numPosts"># of Posts</th>
					<th class="lastPost">Last Post</th>
				</tr>'."\n";
		
		if ($firstTable) $firstTable = FALSE;
		
		if ($threads->rowCount()) { foreach ($threads as $threadInfo) {
			$threadInfo['fp_datePosted'] = switchTimezone($_SESSION['timezone'], $threadInfo['fp_datePosted']);
			$threadInfo['lp_datePosted'] = switchTimezone($_SESSION['timezone'], $threadInfo['lp_datePosted']);
//			if (!isset($threadRD[$threadInfo['threadID']]) && $threadInfo['lp_postID'] > $markedRead) $threadRD[$threadInfo['threadID']] = array('forumID' => $forumID, 'lastRead' => 0, 'lastPost' => $threadInfo['lp_postID']);
//			elseif (isset($threadRD[$threadInfo['threadID']])) $threadRD[$threadInfo['threadID']]['lastPost'] = $threadInfo['lp_postID'];
			$forumIcon = ($threadInfo['lp_postID'] > $markedRead && $threadInfo['lp_postID'] > $threadInfo['lastRead'])?'new':'old';
			$threadContent .= '				<tr>
					<td class="forumIcon"><img src="'.SITEROOT.'/images/forum_'.($threadInfo['sticky']?'sticky_':'').$forumIcon.'.jpg" title="'.($forumIcon == 'new'?'New':'No new').' posts in forum" alt="'.($forumIcon == 'new'?'New':'No new').' posts in forum"></td>
					<td class="threadInfo">'."\n";
			if ($forumIcon == 'new') $threadContent .= "						<a href=\"".SITEROOT."/forums/thread/{$threadInfo['threadID']}?view=newPost\"><img src=\"".SITEROOT."/images/newPost.png\" title=\"View new posts\" alt=\"View new posts\"></a>\n";
			if ($threadInfo['numPosts'] > PAGINATE_PER_PAGE) {
				$threadContent .= "\t\t\t\t\t\t<div class=\"paginateDiv\">";
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
			$threadContent .= '						<a href="'.SITEROOT.'/forums/thread/'.$threadInfo['threadID'].'">'.$threadInfo['title']."</a><br>\n";
			$threadContent .= '						<span class="threadAuthor">by <a href="'.SITEROOT.'/ucp/'.$threadInfo['fp_authorID'].'" class="username">'.$threadInfo['fp_username'].'</a> on <span>'.date('M j, Y g:i a', $threadInfo['fp_datePosted'])."</span></span>\n";
			$threadContent .= '						</td>
					<td class="numPosts">'.$threadInfo['numPosts'].'</td>
					<td class="lastPost">
						<a href="'.SITEROOT.'/ucp/'.$threadInfo['lp_authorID'].'" class="username">'.$threadInfo['lp_username'].'</a><br><span>'.date('M j, Y g:i a', $threadInfo['lp_datePosted'])."</span>
					</td>
				</tr>\n";
		} } else $threadContent .= "\t\t\t<tr><td colspan=\"4\"><h2>No threads yet</h2></td></tr>\n";
		$threadContent .= "			</table>
		</div>\n";
	}
	
//	$mysql->query('UPDATE forums_readData SET forumData = "'.sanatizeString(serialize($forumRD)).'", threadData = "'.sanatizeString(serialize($threadRD)).'" WHERE userID = '.$userID);
	
	$gameID = FALSE;
	$isGM = FALSE;
	$fixedMenu = FALSE;
	if ($heritage[0] == 2) {
		$gameInfo = $mysql->query("SELECT gameID FROM games WHERE forumID = ".intval($heritage[1]));
		$gameInfo = $gameInfo->fetch();
		$gameID = $gameInfo['gameID'];
		$fixedMenu = TRUE;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Forum<?=$forumID?' - '.$forumTitle/*.($forumInfo['gameID']?' (Game)':'')*/:'s'?></h1>
		
<? if ($forumID != 0) { ?>
		<div id="breadcrumb">
			<a href="<?=SITEROOT?>/forums">Index</a><?=$forumID != 0?' > ':''?>
<?
		$breadcrumbs = $mysql->query('SELECT forumID, title FROM forums WHERE forumID IN ('.implode(',', $heritage).')');
		$breadcrumbForums = array();
		foreach ($breadcrumbs as $forumInfo) $breadcrumbForums[$forumInfo['forumID']] = printReady($forumInfo['title']);
		$fCounter = 1;
		foreach ($heritage as $hForumID) {
			echo "\t\t\t<a href=\"".SITEROOT.'/forums/'.$hForumID.'">'.$breadcrumbForums[$hForumID].'</a>'.($fCounter != sizeof($heritage)?' > ':'')."\n";
			$fCounter++;
		}
?>
		</div>
<? } ?>
		
		<div id="topLinkDiv">
<?
	if ($forumID == 0) echo "\t\t\t".'<a href="'.SITEROOT.'/forums/search?search=latestPosts">Latest Posts</a>'."\n";
	elseif ($permissions[$forumID]['createThread']) echo "\t\t\t".'<a id="newThread" href="'.SITEROOT.'/forums/newThread/'.$forumID.'"><img src="'.SITEROOT.'/images/spacer.gif" alt="Create New Thread"></a>'."\n";
?>
		</div>
		
<? if ($forumAdmin) echo "\t\t".'<a id="administrateLink" href="'.SITEROOT.'/forums/acp/'.$forumID.'">Administrative Control Panel</a>'."\n"; ?>
		<p id="rules">Be sure to read and follow the <a href="<?=SITEROOT?>/forums/rules">guidelines for our forums</a>.</p>
	
<?
	$firstTable = TRUE;
	$forumIcon = '';
	
	if (sizeof($forumStructure)) {
		$curCat = '';
		$tableOpen = FALSE;
		foreach ($forumStructure as $iForumID => $info) {
			if ($info['type'] == 'c' && sizeof($info['children'])) {
				if ($tableOpen) {
					$tableOpen = FALSE;
					echo "\t\t\t</table>\n\t\t</div>\n";
				}
?>
		<div class="tableDiv<?=$firstTable?' firstTableDiv':''?>">
			<h2 class="title"><div><?=($forumID == 0)?$forumInfos[$iForumID]['title']:'Subforums'?></div></h2>
			<table class="forumList">
				<tr>
					<td class="forumIcon">&nbsp;</td>
					<th class="forumName">Forum</th>
					<th class="numThreads"># of Threads</th>
					<th class="numPosts"># of Posts</th>
					<th class="lastPost">Last Post</th>
				</tr>
<?
				if ($firstTable) $firstTable = FALSE;
				foreach ($info['children'] as $cForumID => $cInfo) {
					$forumInfo = $forumInfos[$cForumID];
					$forumInfo['datePosted'] = switchTimezone($_SESSION['timezone'], $forumInfo['datePosted']);
					$cHeritage = explode('-', $forumInfo['heritage']);
					foreach ($cHeritage as $key => $hForumID) $cHeritage[$key] = intval($hForumID);
//					$forumIcon = checkNewPosts_new($cForumID, $readData, $permissionsList, $children[$cForumID])?'new':'old';
					$forumIcon = $readData[$cForumID]?'new':'old';
?>
				<tr<?=$forumInfo['numPosts']?'':' class="noPosts"'?>>
					<td class="forumIcon"><img src="<?=SITEROOT?>/images/forum_<?=$forumIcon?>.jpg" title="<?=$forumIcon == 'new'?'New':'No New'?> posts in forum" alt="<?=$forumIcon == 'new'?'New':'No new'?> posts in forum"></td>
					<td class="forumName">
						<a href="<?=SITEROOT?>/forums/<?=$forumInfo['forumID']?>"><?=printReady($forumInfo['title'])?></a>
<?=($forumInfo['description'] != '')?"\t\t\t\t\t\t<div class=\"description\">".printReady($forumInfo['description'])."</div>\n":''?>
					</td>
					<td class="numThreads"><?=$forumInfo['numThreads']?$forumInfo['numThreads']:0?></td>
					<td class="numPosts"><?=$forumInfo['numPosts']?$forumInfo['numPosts']:0?></td>
					<td class="lastPost">
<?
					if ($forumInfo['username']) echo "\t\t\t\t\t\t<a href=\"".SITEROOT.'/ucp/'.$forumInfo['authorID'].'" class="username">'.$forumInfo['username'].'</a><br><span>'.date('M j, Y g:i a', $forumInfo['datePosted'])."</span>\n";
					else echo "\t\t\t\t\t\t</span>No Posts Yet!</span>\n";
?>
					</td>
				</tr>
<?
				}
				echo "\t\t\t</table>\n\t\t</div>\n";
			} elseif ($info['type'] == 'f') {
				if (!$tableOpen) {
					$tableOpen = TRUE;
?>
		<div class="tableDiv<?=$firstTable?' firstTableDiv':''?>">
			<h2 class="title"><div><?=($forumID == 0)?$forumInfos[$iForumID]['title']:'Subforums'?></div></h2>
			<table class="forumList">
				<tr>
					<td class="forumIcon">&nbsp;</td>
					<th class="forumName">Forum</th>
					<th class="numThreads"># of Threads</th>
					<th class="numPosts"># of Posts</th>
					<th class="lastPost">Last Post</th>
				</tr>
<?
					if ($firstTable) $firstTable = FALSE;
				}
				$forumInfo = $forumInfos[$iForumID];
				$forumInfo['datePosted'] = switchTimezone($_SESSION['timezone'], $forumInfo['datePosted']);
				$fHeritage = explode('-', $forumInfo['heritage']);
				foreach ($fHeritage as $key => $hForumID) $fHeritage[$key] = intval($hForumID);
//				$forumIcon = checkNewPosts_new($iForumID, $readData, $permissionsList, $children[$iForumID])?'new':'old';
				$forumIcon = $readData[$iForumID]?'new':'old';
?>
				<tr<? echo $forumInfo['numPosts']?'':' class="noPosts"' ?>>
					<td class="forumIcon"><img src="<?=SITEROOT?>/images/forum_<?=$forumIcon?>.jpg" title="<?=$forumIcon == 'new'?'New':'No new'?> posts in forum" alt="<?=$forumIcon == 'new'?'New':'No new'?> posts in forum"></td>
					<td class="forumName">
						<a href="<?=SITEROOT?>/forums/<? echo $forumInfo['forumID']; ?>"><?=printReady($forumInfo['title']); ?></a>
<?=($forumInfo['description'] != '')?"\t\t\t\t\t\t<div class=\"description\">".printReady($forumInfo['description'])."</div>\n":''?>
					</td>
					<td class="numThreads"><? echo $forumInfo['numThreads']?$forumInfo['numThreads']:0; ?></td>
					<td class="numPosts"><? echo $forumInfo['numPosts']?$forumInfo['numPosts']:0; ?></td>
					<td class="lastPost">
<?
					if ($forumInfo['username']) echo "\t\t\t\t\t\t<a href=\"".SITEROOT.'/ucp/'.$forumInfo['authorID'].'" class="username">'.$forumInfo['username'].'</a><br><span>'.date('M j, Y g:i a', $forumInfo['datePosted'])."</span>\n";
					else echo "\t\t\t\t\t\tNo Posts Yet!</span>\n";
?>
					</td>
				</tr>
<?
			}
		}
	}
	if ($tableOpen) echo "\t\t\t</table>\n\t\t</div>\n";
?>
		
<?=$threadContent?>
				
		<div id="forumLinks">
			<div id="forumOptions">
<? if ($loggedIn) { ?>
				<a href="<?=SITEROOT?>/forums/process/read/<?=$forumID?>">Mark Forum As Read</a>
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