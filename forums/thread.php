<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	$loggedIn = checkLogin(0);
	
	$userID = intval($_SESSION['userID']);
	$threadID = intval($pathOptions[1]);
	if (!threadID) { header('Location: '.SITEROOT.'/forums'); exit; }
	
	$threadInfo = $mysql->query('SELECT threads.forumID, threads.threadID, threads.locked, threads.sticky, posts.title, relPosts.firstPostID, relPosts.lastPostID, numPosts.numPosts, forums.heritage FROM threads, forums, threads_relPosts relPosts, posts, (SELECT COUNT(*) numPosts FROM posts WHERE threadID = '.$threadID.') numPosts WHERE threads.threadID = '.$threadID.' AND threads.threadID = relPosts.threadID AND relPosts.firstPostID = posts.postID AND threads.forumID = forums.forumID');
	$threadInfo = $threadInfo->fetch();
	$threadInfo['heritage'] = explode('-', $threadInfo['heritage']);
	foreach ($threadInfo['heritage'] as $key => $value) $threadInfo['heritage'][$key] = intval($value);
	$permissions = retrievePermissions($userID, $threadInfo['forumID'], array('read', 'write', 'editPost', 'deletePost', 'deleteThread', 'moderate'), TRUE);
	
	if ($permissions['read'] == 0) { header('Location: '.SITEROOT.'/403'); exit; }
	
/*	$mysql->query('SELECT forumData, threadData FROM forums_readData WHERE userID = '.$userID);
	if ($mysql->rowCount()) {
		list($forumRD, $threadRD) = $mysql->getList();
		$forumRD = unserialize($forumRD);
		$threadRD = unserialize($threadRD);
	} else {
		$mysql->query("INSERT INTO forums_readData (userID) VALUES ($userID)");
		$mysql->query('SELECT MAX(postID) FROM posts');
		list($maxPostID) = $mysql->getList();
		$forumRD = array(0 => $maxPostID);
		$threadRD = array($threadID => array('forumID' => $threadInfo['forumID'], 'lastRead' => 0, 'lastPost' => 0));
	}
	
	$markedRead = $forumRD[0];
	foreach ($threadInfo['heritage'] as $hForumID) if ($markedRead < $forumRD[$hForumID]) $markedRead = $forumRD[$hForumID];*/
	
	if (isset($_GET['view']) && $_GET['view'] == 'newPost') {
//		$mysql->query('SELECT postID FROM posts WHERE postID > '.($threadRD[$threadID]['lastRead']?$threadRD[$threadID]['lastRead']:$markedRead).' AND threadID = '.$threadID.' LIMIT 1');
		$nextPost = $mysql->query('SELECT p.postID FROM posts p INNER JOIN threads t USING (threadID) LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = '.$userID.' LEFT JOIN forums_readData_forums rdf ON t.forumID = rdf.forumID AND rdf.userID = '.$userID.' WHERE p.postID > rdt.lastRead AND p.postID > rdf.lastRead LIMIT 1');
		if ($nextPost->rowCount()) $nextPost = $nextPost->fetchColumn();
		else $nextPost = $threadInfo['lastPostID'];
		header('Location: '.SITEROOT.'/forums/thread/'.$threadID.'?p='.$nextPost.'#p'.$nextPost); exit;
	}
	
/*	$threadRD[$threadID]['forumID'] = $threadInfo['forumID'];
	$threadRD[$threadID]['lastRead'] = $threadInfo['lastPostID'];
	$threadRD[$threadID]['lastPost'] = $threadInfo['lastPostID'];
	$mysql->query('UPDATE forums_readData SET threadData = "'.sanatizeString(serialize($threadRD)).'" WHERE userID = '.$userID);*/
	$mysql->query("INSERT INTO forums_readData_threads SET threadID = $threadID, userID = $userID, lastRead = {$threadInfo['lastPostID']} ON DUPLICATE KEY UPDATE lastRead = {$threadInfo['lastPostID']}");
	
	if (isset($_GET['p'])) {
		$post = intval($_GET['p']);
		$postsBefore = $mysql->query('SELECT COUNT(postID) FROM posts WHERE threadID = '.$threadID.' AND postID <= '.$post);
		$postsBefore = $postsBefore->fetchColumn();
		$page = $postsBefore?ceil($postsBefore / PAGINATE_PER_PAGE):1;
	} else $page = intval($_GET['page']);
	$page = $page > 0?$page:1;
	$start = ($page - 1) * PAGINATE_PER_PAGE;
	$posts = $mysql->query('SELECT posts.postID, posts.title, users.userID, posts.message, posts.datePosted, posts.lastEdit, posts.timesEdited, users.username, rolls.numRolls, draws.numDraws FROM posts LEFT JOIN users ON posts.authorID = users.userID LEFT JOIN (SELECT COUNT(*) AS numRolls, postID FROM rolls GROUP BY postID) AS rolls ON posts.postID = rolls.postID LEFT JOIN (SELECT COUNT(*) AS numDraws, postID FROM deckDraws GROUP BY postID) AS draws ON posts.postID = draws.postID WHERE posts.threadID = '.$threadID.' ORDER BY postID LIMIT '.$start.', '.PAGINATE_PER_PAGE);
	
	$gameID = FALSE;
	$isGM = FALSE;
	if ($threadInfo['heritage'][0] == 2) {
		$gameInfo = $mysql->query("SELECT games.gameID, systems.shortName system, gms.primary IS NOT NULL isGM FROM games INNER JOIN systems ON games.systemID = systems.systemID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE gms.userID = $userID) gms ON games.gameID = gms.gameID WHERE games.forumID = ".intval($threadInfo['heritage'][1]));
		$gameInfo = $gameInfo->fetch();
		$gameID = $gameInfo['gameID'];
		$isGM = $gameInfo['isGM'];
		$fixedMenu = TRUE;
	}
	
	$rolls = $mysql->query("SELECT posts.postID, rolls.roll, rolls.indivRolls, rolls.ra, rolls.reason, rolls.total, rolls.visibility FROM posts, rolls WHERE posts.threadID = $threadID AND rolls.postID = posts.postID ORDER BY rolls.rollID");
	$temp = array();
	foreach ($rolls as $rollInfo) $temp[$rollInfo['postID']][] = $rollInfo;
	$rolls = $temp;
	
	$draws = $mysql->query('SELECT posts.postID, deckDraws.drawID, deckDraws.type, deckDraws.cardsDrawn, deckDraws.reveals, deckDraws.reason FROM posts, deckDraws WHERE posts.threadID = '.$threadID.' AND deckDraws.postID = posts.postID');
	$temp = array();
	foreach ($draws as $drawInfo) $temp[$drawInfo['postID']][] = $drawInfo;
	$draws = $temp;
	
	$pollInfo = $mysql->query("SELECT poll, optionsPerUser, allowRevoting FROM forums_polls WHERE threadID = $threadID");
	$pollInfo = $pollInfo->rowCount()?$pollInfo->fetch():FALSE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=$threadInfo['title']?></h1>
		<div id="threadMenu" class="clearfix">
			<a href="<?=SITEROOT.'/forums/'.$threadInfo['forumID']?>">Back to the forums</a>
<? if ($permissions['moderate']) { ?>
			<div id="threadOptions"><form method="post" action="<?=SITEROOT?>/forums/process/modThread">
<?
	$sticky = $threadInfo['sticky']?'unsticky':'sticky';
	$lock = $threadInfo['locked']?'unlock':'lock';
?>
				<input type="hidden" name="threadID" value="<?=$threadID?>">
				<button type="submit" name="sticky" title="<?=ucwords($sticky)?> Thread" alt="<?=ucwords($sticky)?> Thread" class="<?=$sticky?>"></button>
				<button type="submit" name="lock" title="<?=ucwords($lock)?> Thread" alt="<?=ucwords($lock)?> Thread" class="<?=$lock?>"></button>
			</form></div>
<? } ?>
		</div>
<?
	if ($pollInfo) {
?>
		<form id="poll" method="post" action="<?=SITEROOT?>/forums/process/vote">
			<input type="hidden" name="threadID" value="<?=$threadID?>">
			<p id="poll_question"><?=printReady($pollInfo['poll'])?></p>
<? 
		$castVotes = $mysql->query("SELECT pv.pollOptionID FROM forums_pollVotes pv, forums_pollOptions po WHERE po.threadID = $threadID AND po.pollOptionID = pv.pollOptionID AND pv.userID = $userID");
		$temp = array();
		foreach ($castVotes as $voteInfo) $temp[] = $voteInfo['pollOptionID'];
		$castVotes = $temp;
		if (sizeof($castVotes) && $pollInfo['allowRevoting'] || sizeof($castVotes) == 0) echo "			<p>You may select ".($pollInfo['optionsPerUser'] > 1?'up to ':'')."<b>{$pollInfo['optionsPerUser']}</b> option".($pollInfo['optionsPerUser'] > 1?'s':'').".</p>\n";
		
		$votes = $mysql->query("SELECT po.pollOptionID, COUNT(po.pollOptionID) numVotes FROM forums_pollOptions po, forums_pollVotes pv WHERE po.threadID = $threadID AND po.pollOptionID = pv.pollOptionID GROUP BY po.pollOptionID");
		$numVotes = array();
		$totalVotes = 0;
		foreach ($votes as $voteInfo) { $numVotes[$voteInfo['pollOptionID']] = $voteInfo['numVotes']; $totalVotes += $voteInfo['numVotes']; }
?>
			<ul>
<?
		$options = $mysql->query("SELECT pollOptionID, `option` FROM forums_pollOptions WHERE threadID = $threadID ORDER BY pollOptionID");
		foreach ($options as $optionInfo) {
			echo "				<li class=\"clearfix\">\n";
			if (sizeof($castVotes) && $pollInfo['allowRevoting'] || sizeof($castVotes) == 0) {
				if ($pollInfo['optionsPerUser'] == 1) echo "					<div class=\"poll_input\"><input type=\"radio\" name=\"votes\" value=\"{$optionInfo['pollOptionID']}\"".(in_array($optionInfo['pollOptionID'], $castVotes)?' checked="checked"':'')."></div>\n";
				else echo "					<div class=\"poll_input\"><input type=\"checkbox\" name=\"votes[]\" value=\"{$optionInfo['pollOptionID']}\"".(in_array($optionInfo['pollOptionID'], $castVotes)?' checked="checked"':'')."></div>\n";
			}
			echo "					<div class=\"poll_option\">".printReady($optionInfo['option'])."</div>\n";
			if (sizeof($castVotes)) {
				if (!isset($numVotes[$optionInfo['pollOptionID']]))$numVotes[$optionInfo['pollOptionID']] = 0;
				echo "					<div class=\"poll_votesCast\" ".($numVotes[$optionInfo['pollOptionID']]?' style="width: '.(75 + floor($numVotes[$optionInfo['pollOptionID']]/$totalVotes*425)).'px"':'').">".$numVotes[$optionInfo['pollOptionID']].", ".floor($numVotes[$optionInfo['pollOptionID']]/$totalVotes*100)."%</div>\n";
			}
			echo "				</li>\n";
		}
?>
			</ul>
			<div id="poll_submit"><button type="submit" name="submit" class="btn_submit"></button></div>
		</form>
<?
	}
	
	$postCount = 1;
	$forumOptions = $mysql->query("SELECT showAvatars, postSide FROM users WHERE userID = $userID");
	$forumOptions = $forumOptions->fetch();
	if ($forumOptions['postSide'] == 'r' || $forumOptions['postSide'] == 'c') $postSide = 'Right';
	else $postSide = 'Left';
	
	if ($posts->rowCount()) {
		foreach ($posts as $postInfo) {
			$postInfo['datePosted'] = switchTimezone($_SESSION['timezone'], $postInfo['datePosted']);
			$postInfo['lastEdit'] = switchTimezone($_SESSION['timezone'], $postInfo['lastEdit']);
?>
		<div class="postBlock post<?=$postSide?>">
			<a name="p<?=$postInfo['postID']?>"></a>
			<div class="posterDetails">
<?			if (file_exists(FILEROOT."/ucp/avatars/{$postInfo['userID']}.jpg") && $forumOptions['showAvatars']) echo "				<div class=\"avatar\"><img src=\"".SITEROOT."/ucp/avatars/{$postInfo['userID']}.jpg\" width=\"100\"></div>\n"; ?>
				<p class="posterName"><a href="<?=SITEROOT.'/ucp/'.$postInfo['userID']?>" class="username"><?=$postInfo['username']?></a></p>
			</div>
			<div class="postContent">
				<div class="postPoint point<?=$postSide == 'Right'?'Left':'Right'?>"></div>
				<div class="postedOn"><?=date('M j, Y g:i a', $postInfo['datePosted'])?></div>
<?
			echo "\t\t\t\t<div class=\"subject\">".(strlen($postInfo['title'])?printReady($postInfo['title']):'&nbsp')."</div>\n";
			echo "\t\t\t\t<div class=\"post\">\n";
			echo BBCode2Html(printReady($postInfo['message']))."\n";
			if ($postInfo['timesEdited']) { echo "\t\t\t\t\t".'<div class="editInfoDiv">Last edited '.date('F j, Y g:i a', $postInfo['lastEdit']).', a total of '.$postInfo['timesEdited'].' time'.(($postInfo['timesEdited'] > 1)?'s':'')."</div>\n"; }
			echo "\t\t\t\t</div>\n";
			
			if (sizeof($rolls[$postInfo['postID']])) {
?>
				<h4>Rolls</h4>
<?
				$visText = array(1 => '[Hidden Roll/Result]', '[Hidden Dice &amp; Roll]', '[Everything Hidden]');
				$hidden = FALSE;
				$showAll = FALSE;
				foreach ($rolls[$postInfo['postID']] as $roll) {
					$showAll = $isGM || $userID == $postInfo['userID']?TRUE:FALSE;
					$hidden = FALSE;
					
					echo "\t\t\t\t<div class=\"rollInfo\">";
					echo "\t\t\t\t\t<div>";
					echo $showAll && $roll['visibility'] > 0?'<span class="hidden">'.$visText[$roll['visibility']].'</span> ':'';
					if ($roll['visibility'] <= 2) echo $roll['reason'];
					elseif ($showAll) { echo '<span class="hidden">'.$roll['reason']; $hidden = TRUE; }
					else echo 'Secret Roll';
					if ($roll['visibility'] <= 1) echo " - ({$roll['roll']}".($roll['ra']?', RA':'').')';
					elseif ($showAll) { echo ($hidden?'':'<span class="hidden">')." - ({$roll['roll']}".($roll['ra']?', RA':'').')'; $hidden = TRUE; }
					echo $hidden?'</span>':'';
					echo "</div>\n";
					if ($roll['visibility'] == 0) echo "\t\t\t\t\t<div class=\"indent\">{$roll['indivRolls']} = {$roll['total']}</div>\n";
					elseif ($showAll) echo "\t\t\t\t\t<div class=\"indent\"><span class=\"hidden\">{$roll['indivRolls']} = {$roll['total']}</span></div>\n";
					echo "\t\t\t\t</div>";
				}
	 		}
			
			if (sizeof($draws[$postInfo['postID']])) {
?>
				<h4>Deck Draws</h4>
<?
				foreach ($draws[$postInfo['postID']] as $draw) {
					echo "\t\t\t\t<div>".printReady($draw['reason'])."</div>\n";
					if ($postInfo['userID'] == $userID) {
						echo "\t\t\t\t<form method=\"post\" action=\"".SITEROOT."/forums/process/cardVis\">\n";
						echo "\t\t\t\t\t<input type=\"hidden\" name=\"drawID\" value=\"{$draw['drawID']}\">\n";
						$cardsDrawn = explode('~', $draw['cardsDrawn']);
						$count = 0;
						foreach ($cardsDrawn as $cardDrawn) {
							echo "\t\t\t\t\t<button type=\"submit\" name=\"position\" value=\"$count\">\n";
							echo "\t\t\t\t\t\t".'<img src="'.SITEROOT.'/images/cards/'.$draw['type'].'/'.$cardDrawn.'_mini.png" alt="'.cardText($cardDrawn, $draw['type']).'" title="'.cardText($cardDrawn, $draw['type']).'">'."\n";
							echo "\t\t\t\t\t\t<img src=\"".SITEROOT."/images/eye".($draw['reveals'][$count++] == 1?'':'_hidden').".png\" alt=\"Visible\" title=\"Visible\" height=\"25px\" class=\"eyeIcon\">\n";
							echo "\t\t\t\t\t</button>\n";
						}
						echo "\t\t\t\t</form>\n";
					} else {
						echo "\t\t\t\t<div class=\"indent\">\n";
						$cardsDrawn = explode('~', $draw['cardsDrawn']);
						$count = 0;
						foreach ($cardsDrawn as $cardDrawn) {
							if ($draw['reveals'][$count++] == 1) echo "\t\t\t\t\t".'<img src="'.SITEROOT.'/images/cards/'.$draw['type'].'/'.$cardDrawn.'_mini.png" alt="'.cardText($cardDrawn, $draw['type']).'" title="'.cardText($cardDrawn, $draw['type']).'">'."\n";
							else echo "\t\t\t\t\t<img src=\"".SITEROOT."/images/cards/back.png\" alt=\"Hidden Card\" title=\"Hidden Card\">\n";
						}
						echo "\t\t\t\t</div>\n";
					}
				}
	 		}
?>
			</div>
			<div class="postActions">
<?
			echo "				<div>\n";
			if ($permissions['write']) echo "					<a href=\"".SITEROOT."/forums/post/{$threadID}?quote={$postInfo['postID']}\">Quote</a>\n";
			if (($postInfo['userID'] == $userID && !$threadInfo['locked']) || $permissions['moderate']) {
				if ($permissions['moderate'] || $permissions['editPost']) echo "					<a href=\"".SITEROOT."/forums/editPost/{$postInfo['postID']}\">Edit</a>\n";
				if ($permissions['moderate'] || $permissions['deletePost'] && $postInfo['postID'] != $threadInfo['firstPostID'] || $permissions['deleteThread'] && $postInfo['postID'] == $threadInfo['firstPostID']) echo "					<a href=\"".SITEROOT."/forums/delete/{$postInfo['postID']}\">Delete</a>\n";
			}
			echo "				</div>";
?>
			</div>
		</div>
<?
			$postCount += 1;
			if ($forumOptions['postSide'] == 'c') $postSide = $postSide == 'Right'?'Left':'Right';
		}
		
		if ($threadInfo['numPosts'] > PAGINATE_PER_PAGE) {
			$spread = 2;
			echo "\t\t<div class=\"paginateDiv\">";
			$numPages = ceil($threadInfo['numPosts'] / PAGINATE_PER_PAGE);
			$firstPage = $page - $spread;
			if ($firstPage < 1) $firstPage = 1;
			$lastPage = $page + $spread;
			if ($lastPage > $numPages) $lastPage = $numPages;
			echo "\t\t\t<div class=\"currentPage\">$page of $numPages</div>\n";
			if (($page - $spread) > 1) echo "\t\t\t<a href=\"?page=1\">&lt;&lt; First</a>\n";
			if ($page > 1) echo "\t\t\t<a href=\"?page=".($page - 1)."\">&lt;</a>\n";
			for ($count = $firstPage; $count <= $lastPage; $count++) echo "\t\t\t<a href=\"?page=$count\"".(($count == $page)?' class="page"':'').">$count</a>\n";
			
			if ($page < $numPages) echo "\t\t\t<a href=\"?page=".($page + 1)."\">&gt;</a>\n";
			if (($page + $spread) < $numPages) echo "\t\t\t<a href=\"?page=$numPages\">Last &gt;&gt;</a>\n";
			echo "\t\t</div>\n";
			echo "\t\t<br class=\"clear\">\n";
		}
	}
	
	if ($permissions['moderate']) {
?>
		<form id="quickMod" method="post" action="<?=SITEROOT?>/forums/process/modThread">
<?
	$sticky = $threadInfo['sticky']?'Unsticky':'Sticky';
	$lock = $threadInfo['locked']?'Unlock':'lock';
?>
			Quick Mod Actions: 
			<input type="hidden" name="threadID" value="<?=$threadID?>">
			<select name="action">
				<option value="lock"><?=ucwords($lock)?> Thread</option>
				<option value="sticky"><?=ucwords($sticky)?> Thread</option>
				<option value="move">Move Thread</option>
			</select>
			<button type="submit" name="go">Go</button>
		</form>
		<br class="clear">
<?
	}
	
	if ($permissions['write'] && $userID != 0 && !$threadInfo['locked']) {
?>
		<form method="post" action="<?=SITEROOT?>/forums/process/post">
			<h2 class="headerbar hbDark">Quick Reply</h2>
			<input type="hidden" name="threadID" value="<?=$threadID?>">
			<input type="hidden" name="title" value="Re: <?=$threadInfo['title']?>">
			<textarea id="messageTextArea" name="message"></textarea>
			
			<div id="submitDiv" class="alignCenter">
				<div class="fancyButton"><button type="submit" name="post">Post</button></div>
				<div class="fancyButton"><button type="submit" name="advanced">Advanced</button></div>
			</div>
		</form>
<?
	} elseif ($threadInfo['locked']) echo "\t\t\t<h2>Thread locked</h2>\n";
	else echo "\t\t\t<h2>You do not have permission to post in this thread.</h2>\n";
	
	require_once(FILEROOT.'/footer.php');
?>