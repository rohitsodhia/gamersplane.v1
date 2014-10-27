<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	addPackage('tools');
	
	$userID = intval($_SESSION['userID']);
	$threadID = intval($pathOptions[1]);
	if (!threadID) { header('Location: /forums'); exit; }
	
	$threadInfo = $mysql->query("SELECT threads.forumID, threads.threadID, threads.locked, threads.sticky, posts.title, relPosts.firstPostID, relPosts.lastPostID, numPosts.numPosts, forums.heritage, newPosts.lastRead lastReadID, newPosts.cLastRead cLastReadID FROM threads INNER JOIN forums ON threads.forumID = forums.forumID INNER JOIN threads_relPosts relPosts ON threads.threadID = relPosts.threadID INNER JOIN posts ON relPosts.firstPostID = posts.postID INNER JOIN (SELECT threadID, COUNT(postID) numPosts FROM posts WHERE threadID = {$threadID}) numPosts ON threads.threadID = numPosts.threadID LEFT JOIN forums_readData_newPosts newPosts ON threads.threadID = newPosts.threadID AND newPosts.userID = {$userID} WHERE threads.threadID = {$threadID}");
	$threadInfo = $threadInfo->fetch();
	$threadInfo['heritage'] = explode('-', $threadInfo['heritage']);
	foreach ($threadInfo['heritage'] as $key => $value) $threadInfo['heritage'][$key] = intval($value);
	$permissions = retrievePermissions($userID, $threadInfo['forumID'], array('read', 'write', 'editPost', 'deletePost', 'deleteThread', 'moderate'), TRUE);
	
	if ($permissions['read'] == 0) { header('Location: /403'); exit; }

	if (isset($_GET['view']) && $_GET['view'] == 'newPost') {
		$lastReadID = (int) $threadInfo['lastReadID'] > (int) $threadInfo['cLastReadID']?(int) $threadInfo['lastReadID']:(int) $threadInfo['cLastReadID'];
		$numPrevPosts = $mysql->query("SELECT COUNT(postID) numPosts FROM posts WHERE threadID = {$threadID} AND postID <= {$lastReadID}");
		$numPrevPosts = $numPrevPosts->fetchColumn();
		if ($threadInfo['lastReadID'] != $threadInfo['lastPostID']) $numPrevPosts += 1;
		$page = $numPrevPosts?ceil($numPrevPosts / PAGINATE_PER_PAGE):1;
	} elseif (isset($_GET['p'])) {
		$post = intval($_GET['p']);
		$numPrevPosts = $mysql->query('SELECT COUNT(postID) FROM posts WHERE threadID = '.$threadID.' AND postID <= '.$post);
		$numPrevPosts = $numPrevPosts->fetchColumn();
		$page = $numPrevPosts?ceil($numPrevPosts / PAGINATE_PER_PAGE):1;
	} else $page = intval($_GET['page']);
	$page = $page > 0?$page:1;
	if ($page > ceil($threadInfo['numPosts'] / PAGINATE_PER_PAGE)) $page = ceil($threadInfo['numPosts'] / PAGINATE_PER_PAGE);
	$start = ($page - 1) * PAGINATE_PER_PAGE;
	$posts = $mysql->query("SELECT posts.postID, posts.title, users.userID, users.avatarExt, posts.message, posts.datePosted, posts.lastEdit, posts.timesEdited, users.username, rolls.numRolls, draws.numDraws FROM posts LEFT JOIN users ON posts.authorID = users.userID LEFT JOIN (SELECT COUNT(rollID) AS numRolls, postID FROM rolls GROUP BY postID) AS rolls ON posts.postID = rolls.postID LEFT JOIN (SELECT COUNT(drawID) AS numDraws, postID FROM deckDraws GROUP BY postID) AS draws ON posts.postID = draws.postID WHERE posts.threadID = {$threadID} ORDER BY postID LIMIT {$start}, ".PAGINATE_PER_PAGE);
	if ($loggedIn) $mysql->query("INSERT INTO forums_readData_threads SET threadID = $threadID, userID = $userID, lastRead = {$threadInfo['lastPostID']} ON DUPLICATE KEY UPDATE lastRead = {$threadInfo['lastPostID']}");

	$gameID = FALSE;
	$isGM = FALSE;
	if ($threadInfo['heritage'][0] == 2 && $threadInfo['forumID'] != 10) {
		$gameID = $mysql->query('SELECT gameID FROM games WHERE forumID = '.intval($threadInfo['heritage'][1]));
		$gameID = $gameID->fetchColumn();
		$gmCheck = $mysql->query("SELECT isGM FROM players WHERE userID = $userID AND gameID = $gameID");
		if ($gmCheck->rowCount()) $isGM = TRUE;
	}

	$rolls = $mysql->query("SELECT p.postID, r.rollID, r.type, r.reason, r.roll, r.indivRolls, r.results, r.visibility, r.extras FROM posts p, rolls r WHERE p.threadID = {$threadID} AND r.postID = p.postID ORDER BY r.rollID");
	$temp = array();
	foreach ($rolls as $rollInfo) {
		$rollObj = RollFactory::getRoll($rollInfo['type']);
		$rollObj->forumLoad($rollInfo);
		$temp[$rollInfo['postID']][] = $rollObj;
	}
	$rolls = $temp;
	
	$draws = $mysql->query("SELECT posts.postID, deckDraws.drawID, deckDraws.type, deckDraws.cardsDrawn, deckDraws.reveals, deckDraws.reason FROM posts, deckDraws WHERE posts.threadID = {$threadID} AND deckDraws.postID = posts.postID");
	$temp = array();
	foreach ($draws as $drawInfo) $temp[$drawInfo['postID']][] = $drawInfo;
	$draws = $temp;
	
	$pollInfo = $mysql->query("SELECT poll, optionsPerUser, allowRevoting FROM forums_polls WHERE threadID = {$threadID}");
	$pollInfo = $pollInfo->rowCount()?$pollInfo->fetch():FALSE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=$threadInfo['title']?></h1>
		<div class="hbMargined">
			<div id="threadMenu" class="clearfix">
				<div class="leftCol">
					<a href="<?='/forums/'.$threadInfo['forumID']?>">Back to the forums</a>
				</div>
				<div class="rightCol alignRight">
<? if ($permissions['moderate']) { ?>
					<form id="threadOptions" method="post" action="/forums/process/modThread">
<?
	$sticky = $threadInfo['sticky']?'unsticky':'sticky';
	$lock = $threadInfo['locked']?'unlock':'lock';
?>
						<input type="hidden" name="threadID" value="<?=$threadID?>">
						<button type="submit" name="sticky" title="<?=ucwords($sticky)?> Thread" alt="<?=ucwords($sticky)?> Thread" class="<?=$sticky?>"></button>
						<button type="submit" name="lock" title="<?=ucwords($lock)?> Thread" alt="<?=ucwords($lock)?> Thread" class="<?=$lock?>"></button>
					</form>
<? } ?>
<?	if ($permissions['write']) { ?>
					<a href="/forums/post/<?=$threadID?>" class="fancyButton">Reply</a>
<?	} ?>
				</div>
			</div>
<?
	if ($pollInfo) {
?>
			<form id="poll" method="post" action="/forums/process/vote">
				<input type="hidden" name="threadID" value="<?=$threadID?>">
				<p id="poll_question"><?=printReady($pollInfo['poll'])?></p>
<? 
		$castVotes = $mysql->query("SELECT pv.pollOptionID FROM forums_pollVotes pv, forums_pollOptions po WHERE po.threadID = $threadID AND po.pollOptionID = pv.pollOptionID AND pv.userID = $userID");
		$temp = array();
		foreach ($castVotes as $voteInfo) $temp[] = $voteInfo['pollOptionID'];
		$castVotes = $temp;
		if (sizeof($castVotes) && $pollInfo['allowRevoting'] || sizeof($castVotes) == 0) echo "				<p>You may select ".($pollInfo['optionsPerUser'] > 1?'up to ':'')."<b>{$pollInfo['optionsPerUser']}</b> option".($pollInfo['optionsPerUser'] > 1?'s':'').".</p>\n";
		
		$votes = $mysql->query("SELECT po.pollOptionID, COUNT(po.pollOptionID) numVotes FROM forums_pollOptions po, forums_pollVotes pv WHERE po.threadID = $threadID AND po.pollOptionID = pv.pollOptionID GROUP BY po.pollOptionID");
		$numVotes = array();
		$totalVotes = 0;
		foreach ($votes as $voteInfo) {
			$numVotes[$voteInfo['pollOptionID']] = $voteInfo['numVotes'];
			$totalVotes += $voteInfo['numVotes'];
		}
		$highestVotes = sizeof($numVotes)?max($numVotes):0;
?>
				<ul>
<?
		$options = $mysql->query("SELECT pollOptionID, `option` FROM forums_pollOptions WHERE threadID = $threadID ORDER BY pollOptionID");
		foreach ($options as $optionInfo) {
			echo "					<li class=\"clearfix\">\n";
			if (sizeof($castVotes) && $pollInfo['allowRevoting'] || sizeof($castVotes) == 0) {
				if ($pollInfo['optionsPerUser'] == 1) echo "						<div class=\"poll_input\"><input type=\"radio\" name=\"votes\" value=\"{$optionInfo['pollOptionID']}\"".(in_array($optionInfo['pollOptionID'], $castVotes)?' checked="checked"':'')."></div>\n";
				else echo "						<div class=\"poll_input\"><input type=\"checkbox\" name=\"votes[]\" value=\"{$optionInfo['pollOptionID']}\"".(in_array($optionInfo['pollOptionID'], $castVotes)?' checked="checked"':'')."></div>\n";
			}
			echo "						<div class=\"poll_option\">".printReady($optionInfo['option'])."</div>\n";
			if (sizeof($castVotes)) {
				if (!isset($numVotes[$optionInfo['pollOptionID']]))$numVotes[$optionInfo['pollOptionID']] = 0;
				echo "						<div class=\"poll_votesCast\" ".($numVotes[$optionInfo['pollOptionID']]?' style="width: '.(100 + floor($numVotes[$optionInfo['pollOptionID']] / $highestVotes * 425)).'px"':'').">".$numVotes[$optionInfo['pollOptionID']].", ".floor($numVotes[$optionInfo['pollOptionID']] / $totalVotes * 100)."%</div>\n";
			}
			echo "					</li>\n";
		}
?>
				</ul>
				<div id="poll_submit"><button type="submit" name="submit" class="fancyButton">Vote</button></div>
			</form>
<?
	}
	
	$postCount = 1;
	if ($loggedIn) {
		$forumOptions = $mysql->query("SELECT showAvatars, postSide FROM users WHERE userID = $userID");
		$forumOptions = $forumOptions->fetch();
	} else $forumOptions = array('showAvatars' => 1, 'postSide'=> 'r');
	if ($forumOptions['postSide'] == 'r' || $forumOptions['postSide'] == 'c') $postSide = 'Right';
	else $postSide = 'Left';
	
	if ($posts->rowCount()) {
		foreach ($posts as $postInfo) {
			$postInfo['datePosted'] = switchTimezone($_SESSION['timezone'], $postInfo['datePosted']);
			$postInfo['lastEdit'] = switchTimezone($_SESSION['timezone'], $postInfo['lastEdit']);
?>
			<div class="postBlock post<?=$postSide?> clearfix">
				<a name="p<?=$postInfo['postID']?>"></a>
				<div class="posterDetails">
					<a href="<?='/user/'.$postInfo['userID']?>" class="avatar"><img src="<?='/ucp/avatars/'.(file_exists(FILEROOT."/ucp/avatars/{$postInfo['userID']}.{$postInfo['avatarExt']}")?$postInfo['userID'].'.'.$postInfo['avatarExt']:'avatar.png')?>"></a>
					<p class="posterName"><a href="<?='/user/'.$postInfo['userID']?>" class="username"><?=$postInfo['username']?></a></p>
				</div>
				<div class="postContent">
					<div class="postPoint point<?=$postSide == 'Right'?'Left':'Right'?>"></div>
					<header class="postHeader">
						<div class="postedOn"><?=date('M j, Y g:i a', $postInfo['datePosted'])?></div>
						<div class="subject"><a href="?p=<?=$postInfo['postID']?>"><?=strlen($postInfo['title'])?printReady($postInfo['title']):'&nbsp'?></a></div>
					</header>
<?
			echo "\t\t\t\t\t<div class=\"post\">\n";
			echo printReady(BBCode2Html($postInfo['message']))."\n";
			if ($postInfo['timesEdited']) { echo "\t\t\t\t\t\t".'<div class="editInfoDiv">Last edited '.date('F j, Y g:i a', $postInfo['lastEdit']).', a total of '.$postInfo['timesEdited'].' time'.(($postInfo['timesEdited'] > 1)?'s':'')."</div>\n"; }
			echo "\t\t\t\t\t</div>\n";
			
			if (sizeof($rolls[$postInfo['postID']])) {
?>
					<div class="rolls">
						<h4>Rolls</h4>
<?
				$visText = array(1 => '[Hidden Roll/Result]', '[Hidden Dice &amp; Roll]', '[Everything Hidden]');
				$hidden = FALSE;
				$showAll = FALSE;
				foreach ($rolls[$postInfo['postID']] as $roll) {
					$showAll = $isGM || $userID == $postInfo['userID']?TRUE:FALSE;
					$hidden = FALSE;
?>
						<div class="rollInfo">
<?
					$roll->showHTML($showAll);
/*					echo $showAll && $roll['visibility'] > 0?'<span class="hidden">'.$visText[$roll['visibility']].'</span> ':'';
					if ($roll['visibility'] <= 2) echo $roll['reason'];
					elseif ($showAll) { echo '<span class="hidden">'.$roll['reason']; $hidden = TRUE; }
					else echo 'Secret Roll';
					if ($roll['visibility'] <= 1) echo " - ({$roll['roll']}".($roll['ra']?', RA':'').')';
					elseif ($showAll) { echo ($hidden?'':'<span class="hidden">')." - ({$roll['roll']}".($roll['ra']?', RA':'').')'; $hidden = TRUE; }
					echo $hidden?'</span>':'';
					echo "</div>\n";
					if ($roll['visibility'] == 0) echo "\t\t\t\t\t\t<div class=\"indent\">".displayIndivDice($roll['indivRolls'])." = {$roll['result']}</div>\n";
					elseif ($showAll) echo "\t\t\t\t\t\t<div class=\"indent\"><span class=\"hidden\">".displayIndivDice($roll['indivRolls'])." = {$roll['result']}</span></div>\n";*/
?>
						</div>
<?
				}
?>
					</div>
<?
	 		}
			
			if (sizeof($draws[$postInfo['postID']])) {
?>
					<h4>Deck Draws</h4>
<?
				foreach ($draws[$postInfo['postID']] as $draw) {
					echo "\t\t\t\t\t<div>".printReady($draw['reason'])."</div>\n";
					if ($postInfo['userID'] == $userID) {
						echo "\t\t\t\t\t<form method=\"post\" action=\"/forums/process/cardVis\">\n";
						echo "\t\t\t\t\t\t<input type=\"hidden\" name=\"drawID\" value=\"{$draw['drawID']}\">\n";
						$cardsDrawn = explode('~', $draw['cardsDrawn']);
						$count = 0;
						foreach ($cardsDrawn as $cardDrawn) {
							echo "\t\t\t\t\t\t<button type=\"submit\" name=\"position\" value=\"$count\">\n";
							echo "\t\t\t\t\t\t\t".getCardImg($cardDrawn, $draw['type'], 'mid')."\n";
							$visText = $draw['reveals'][$count++]?'Visible':'Hidden';
							echo "\t\t\t\t\t\t\t<div alt=\"{$visText}\" title=\"{$visText}\" class=\"eyeIcon".($visText == 'Hidden'?' hidden':'')."\"></div>\n";
							echo "\t\t\t\t\t\t</button>\n";
						}
						echo "\t\t\t\t\t</form>\n";
					} else {
						echo "\t\t\t\t\t<div>\n";
						$cardsDrawn = explode('~', $draw['cardsDrawn']);
						$count = 0;
						foreach ($cardsDrawn as $cardDrawn) {
							if ($draw['reveals'][$count++] == 1) echo "\t\t\t\t\t\t".getCardImg($cardDrawn, $draw['type'], 'mid')."\n";
							else echo "\t\t\t\t\t\t<img src=\"/images/tools/cards/back.png\" alt=\"Hidden Card\" title=\"Hidden Card\" class=\"cardBack mid\">\n";
						}
						echo "\t\t\t\t\t</div>\n";
					}
				}
	 		}
?>
				</div>
				<div class="postActions">
<?
			if ($permissions['write']) echo "						<a href=\"/forums/post/{$threadID}?quote={$postInfo['postID']}\">Quote</a>\n";
			if (($postInfo['userID'] == $userID && !$threadInfo['locked']) || $permissions['moderate']) {
				if ($permissions['moderate'] || $permissions['editPost']) echo "					<a href=\"/forums/editPost/{$postInfo['postID']}\">Edit</a>\n";
				if ($permissions['moderate'] || $permissions['deletePost'] && $postInfo['postID'] != $threadInfo['firstPostID'] || $permissions['deleteThread'] && $postInfo['postID'] == $threadInfo['firstPostID']) echo "					<a href=\"/forums/delete/{$postInfo['postID']}\" class=\"deletePost\">Delete</a>\n";
			}
?>
				</div>
			</div>
<?
			$postCount += 1;
			if ($forumOptions['postSide'] == 'c') $postSide = $postSide == 'Right'?'Left':'Right';
		}
		
		if ($threadInfo['numPosts'] > PAGINATE_PER_PAGE) {
			$spread = 2;
			echo "\t\t\t<div class=\"paginateDiv\">";
			$numPages = ceil($threadInfo['numPosts'] / PAGINATE_PER_PAGE);
			$firstPage = $page - $spread;
			if ($firstPage < 1) $firstPage = 1;
			$lastPage = $page + $spread;
			if ($lastPage > $numPages) $lastPage = $numPages;
			echo "\t\t\t\t<div class=\"currentPage\">$page of $numPages</div>\n";
			if (($page - $spread) > 1) echo "\t\t\t\t<a href=\"?page=1\">&lt;&lt; First</a>\n";
			if ($page > 1) echo "\t\t\t\t<a href=\"?page=".($page - 1)."\">&lt;</a>\n";
			for ($count = $firstPage; $count <= $lastPage; $count++) echo "\t\t\t\t<a href=\"?page=$count\"".(($count == $page)?' class="page"':'').">$count</a>\n";
			
			if ($page < $numPages) echo "\t\t\t\t<a href=\"?page=".($page + 1)."\">&gt;</a>\n";
			if (($page + $spread) < $numPages) echo "\t\t\t\t<a href=\"?page=$numPages\">Last &gt;&gt;</a>\n";
			echo "\t\t\t</div>\n";
			echo "\t\t\t<br class=\"clear\">\n";
		}
	}
	
	if ($permissions['moderate']) {
?>
			<div class="clearfix"><form id="quickMod" method="post" action="/forums/process/modThread">
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
			</form></div>
<?	} ?>
		</div>
	
<?	if ($permissions['write'] && $userID != 0 && !$threadInfo['locked']) { ?>
		<form method="post" action="/forums/process/post">
			<h2 class="headerbar hbDark">Quick Reply</h2>
			<input type="hidden" name="threadID" value="<?=$threadID?>">
			<input type="hidden" name="title" value="Re: <?=$threadInfo['title']?>">
			<div class="hbdMargined"><textarea id="messageTextArea" name="message"></textarea></div>
			
			<div id="submitDiv" class="alignCenter">
				<button type="submit" name="post" class="fancyButton">Post</button>
				<button type="submit" name="advanced" class="fancyButton">Advanced</button>
			</div>
		</form>
<?
	} elseif ($threadInfo['locked']) echo "\t\t\t<h2>Thread locked</h2>\n";
	else echo "\t\t\t<h2 class=\"alignCenter\">You do not have permission to post in this thread.</h2>\n";
	
	require_once(FILEROOT.'/footer.php');
?>