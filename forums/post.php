<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$noChat = FALSE;
	
	$firstPost = FALSE;
	
	if ($pathOptions[0] == 'editPost') {
		$postID = intval($pathOptions[1]);
		$threadInfo = $mysql->query('SELECT threads.forumID, threads.threadID, threads.sticky, threads.allowRolls, threads.allowDraws FROM threads, posts WHERE threads.threadID = posts.threadID AND posts.postID = '.$postID);
		list($forumID, $threadID, $sticky, $allowRolls, $allowDraws) = $threadInfo->fetch();
		
		$rolls = $mysql->query('SELECT posts.postID, rolls.rollID, rolls.roll, rolls.indivRolls, rolls.reason, rolls.ra, rolls.total, rolls.visibility FROM posts, rolls WHERE posts.postID = '.$postID.' AND rolls.postID = posts.postID');
		$temp = array();
		foreach ($rolls as $rollInfo) $temp[] = $rollInfo;
		$rolls = $temp;
		
		$draws = $mysql->query('SELECT deckDraws.deckID, deckDraws.type, deckDraws.cardsDrawn, deckDraws.reason FROM posts, deckDraws WHERE posts.postID = '.$postID.' AND deckDraws.postID = posts.postID');
		$temp = array();
		foreach ($draws as $drawInfo) $temp[$drawInfo['deckID']] = $drawInfo;
		$draws = $temp;
		
		$postInfo = $mysql->query('SELECT threads.forumID, posts.postID, posts.title postTitle, posts.authorID, posts.message, first.title threadTitle, first.postID fpPostID, threads.locked, threads.allowRolls, threads.allowDraws FROM posts, posts first, threads, threads_relPosts relPosts WHERE posts.postID = '.$postID.' AND posts.threadID = threads.threadID AND threads.threadID = relPosts.threadID and relPosts.firstPostID = first.postID');
		$postInfo = $postInfo->fetch();
		
		if ($postInfo['fpPostID'] == $postID) {
			$firstPost = TRUE;
			$pollInfo = $mysql->query("SELECT * FROM forums_polls WHERE threadID = $threadID");
			if ($pollInfo->rowCount()) {
				$postInfo += $pollInfo->fetch();
				$pollOptions = $mysql->query("SELECT `option` FROM forums_pollOptions WHERE threadID = $threadID");
				$postInfo['pollOptions'] = array();
				foreach ($pollOptions as $pollOption) $postInfo['pollOptions'][] = $pollOption['option'];
				$postInfo['pollOptions'] = implode("\n", $postInfo['pollOptions']);
			}
		}
		
		$permissions = retrievePermissions($userID, $postInfo['forumID'], 'write, moderate, addPoll, addRolls, addDraws', TRUE);
		if ($postInfo['authorID'] != $userID/* && $postInfo->rowCount() > 0*/) {
			if ($permissions['moderate'] != 1) $noChat = TRUE;
		} elseif (($postInfo['locked'] && !$permissions['moderate'])) $noChat = TRUE;
		else {
			if (!$permissions['write']) $noChat = TRUE;
		}
	} elseif ($pathOptions[0] == 'newThread') {
		$firstPost = TRUE;
		
		$forumID = intval($pathOptions[1]);
		$heritage = $mysql->query('SELECT heritage FROM forums WHERE forumID = '.$forumID);
		$heritage = $heritage->fetchColumn();
		$gameForum = (intval(substr($heritage, 0, 3)) == 2)?TRUE:FALSE;
		$permissions = retrievePermissions($userID, $forumID, 'createThread, addPoll, addRolls, addDraws, moderate', TRUE);
		if ($permissions['createThread'] != 1) $noChat = TRUE;
	} elseif ($pathOptions[0] == 'post') {
		$threadID = intval($pathOptions[1]);
		$threadInfo = $mysql->query('SELECT threads.forumID, first.title, threads.locked, threads.allowRolls, threads.allowDraws FROM threads, threads_relPosts relPosts, posts first WHERE threads.threadID = '.$threadID.' AND threads.threadID = relPosts.threadID AND relPosts.firstPostID = first.postID LIMIT 1');
		if ($threadInfo->rowCount() == 0) { $noChat = TRUE; break; }
		if (isset($_SESSION['message'])) {
			$postInfo['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		list($forumID, $postInfo['threadTitle'], $locked, $allowRolls, $postInfo['allowDraws']) = $threadInfo->fetch();
		$permissions = retrievePermissions($userID, $forumID, 'write, moderate, addRolls, addDraws', TRUE);
		if ($permissions['write'] != 1 && !$locked) { $noChat = TRUE; break; }
		
		$quoteID = intval($_GET['quote']);
		if ($quoteID) {
			$quoteInfo = $mysql->query("SELECT users.username, posts.message FROM users, posts WHERE posts.postID = {$quoteID} AND posts.authorID = users.userID");
			$quoteInfo = $quoteInfo->fetch();
			$postInfo['message'] = '[quote="'.$quoteInfo['username'].'"]'.$quoteInfo['message'].'[/quote]';
		}
	} else $noChat = TRUE;
	
//	if ($noChat) { header('Location: '.SITEROOT.'/forums/'); exit; }
	
	if ($_SESSION['errors']) {
		if ($_SESSION['lastURL'] == SITEROOT.'/forums/process/post') {
			$errors = $_SESSION['errors'];
			if (isset($postInfo)) $postInfo = $_SESSION['errorVals'] + $postInfo;
			else $postInfo = $_SESSION['errorVals'];
			$postInfo['postTitle'] = $postInfo['title'];
		}
		if ($_SESSION['lastURL'] != SITEROOT.'/forums/process/post' || time() > $_SESSION['errorTime']) {
			unset($_SESSION['errors']);
			unset($_SESSION['errorVals']);
		}
	}
	
	if ($_GET['preview']) {
		if (isset($postInfo)) $postInfo = $_SESSION['previewVars'] + $postInfo;
		else $postInfo = $_SESSION['previewVars'];
		$postInfo['postTitle'] = $postInfo['title'];
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
<? if ($_GET['errors'] && $errors) { ?>
		<div class="alertBox_error"><ul>
<?
	if ($errors['overdrawn']) echo "			<li>Incorrect number of cards drawn.</li>\n";
	if ($errors['noTitle']) echo "			<li>You can't leave the title blank.</li>\n";
	if ($errors['noMessage']) echo "			<li>You can't leave the message blank.</li>\n";
	if ($errors['noDrawReason']) echo "			<li>You left draw reasons blank.</li>\n";
	if ($errors['noPoll']) echo "			<li>You did not provide a poll question.</li>\n";
	if ($errors['noOptions']) echo "			<li>You did not provide poll options or provided too few (minimum 2).</li>\n";
	if ($errors['noOptionsPerUser']) echo "			<li>You did not provide a valid number for \"Options per user\".</li>\n";
	if ($errors['badRoll']) echo "			<li>One or more of your roll entries are malformed. Please make sure they are in the right format.</li>\n";
?>
		</ul></div>
<? } ?>
		<h1><?=($postInfo['postID'] || $pathOptions[0] == 'post')?printReady($postInfo['threadTitle']):'New Thread'?></h1>
		
<? if ($_GET['preview'] && sizeof($_SESSION['previewVars']) && strlen($postInfo['message']) > 0) { ?>
		<h3>Preview:</h3>
		<div id="preview">
			<?=BBCode2Html(printReady($postInfo['message']))."\n"?>
		</div>
		<hr>
		
<? } ?>
		<form method="post" action="<?=SITEROOT?>/forums/process/post">
<?
	if ($pathOptions[0] == 'newThread') echo "\t\t\t".'<input type="hidden" name="new" value="'.$forumID.'">'."\n";
	elseif ($pathOptions[0] == 'editPost') echo "\t\t\t".'<input type="hidden" name="edit" value="'.$postID.'">'."\n";
	elseif ($pathOptions[0] == 'post') echo "\t\t\t".'<input type="hidden" name="threadID" value="'.$threadID.'">'."\n";
	
	if (isset($postInfo, $postInfo['postTitle'])) $title = printReady($postInfo['postTitle'], array('stripslashes'));
	elseif (isset($postInfo['threadTitle'])) $title = (substr($postInfo['threadTitle'], 0, 4) != 'Re: '?'Re: ':'').$postInfo['threadTitle'];
?>
			<div id="basicPostInfo">
				<label class="textLabel">Title:</label>
				<input type="text" name="title" maxlength="50" tabindex="<?=tabOrder();?>" value="<?=$title?>" class="titleInput">
				<textarea id="messageTextArea" name="message" tabindex="<?=tabOrder();?>"><?=printReady($postInfo['message'], array('stripslashes'))?></textarea>
			</div>
<?
 if ($firstPost) { ?>
			
			<hr>
			
			<div id="threadOptions">
				<h3>Thread Options</h3>
<?		if ($permissions['moderate']) { ?>
				<p><input type="checkbox" name="sticky"<?=$sticky?' checked="checked"':''?>> Make thread sticky</p>
<?
		}
		if ($permissions['addRolls']) {
?>
				<p><input type="checkbox" name="allowRolls"<?=$allowRolls || ($pathOptions[0] == 'newThread' && $gameForum)?' checked="checked"':''?>> Allow adding rolls to posts (if this box is unchecked, any rolls added to this thread will be ignored)</p>
<?
		}
		if ($permissions['addDraws']) {
?>
				<p><input type="checkbox" name="allowDraws"<?=$allowDraws || ($pathOptions[0] == 'newThread' && $gameForum)?' checked="checked"':''?>> Allow adding deck draws to posts (if this box is unchecked, any draws added to this thread will be ignored)</p>
<?		} ?>
			</div>
			
<?
		if ($permissions['addPoll']) {
?>
			<hr>
			
			<div id="addPoll">
				<p>If you don't want to add a poll to your thread, leave this section blank.</p>
<?			if ($pathOptions[0] == 'editPost') { ?>
				<div class="tr">
					<label for="allowRevoting" class="textLabel"><b>Delete Poll:</b></label>
					<input id="deletePoll" type="checkbox" name="deletePoll"> If checked, your poll will be deleted and cannot be recovered.
				</div>
<?			} ?>
				<div class="tr">
					<label for="poll" class="textLabel"><b>Poll Question:</b></label>
					<input id="poll" type="text" name="poll" value="<?=$postInfo['poll']?>">
				</div>
				<div class="tr">
					<label for="pollOption" class="textLabel">
						<b>Poll Options:</b>
						<p>Place each option on a new line. You may enter up to <b>25</b> options.</p>
					</label>
					<textarea id="pollOptions" name="pollOptions"><?=$postInfo['pollOptions']?></textarea>
				</div>
				<div class="tr">
					<label for="optionsPerUser" class="textLabel"><b>Options per user:</b></label>
					<input id="optionsPerUser" type="text" name="optionsPerUser" value="<?=isset($postInfo['optionsPerUser'])?$postInfo['optionsPerUser']:'1'?>">
				</div>
				<div class="tr">
					<label for="allowRevoting" class="textLabel"><b>Allow Revoting:</b></label>
					<input id="allowRevoting" type="checkbox" name="allowRevoting" <?=isset($postInfo['allowRevoting']) && $postInfo['allowRevoting']?' checked="checked"':''?>> If checked, people will be allowed to change their votes.
				</div>
			</div>
<?
		}
	}
	
	if ($permissions['addRolls'] && $allowRolls || $permissions['moderate']) {
?>
			
			<hr>
			
			<h3>Add Rolls</h3>
			<div id="rollExplination">
				Enter the text roll in the following format:<br>
				(number of dice)d(dice type)+/-(modifier), i.e. 2d6+4, 1d10-2<br>
				The roll will automatically be added to your post when you submit it. Only put one dice type per roll.
			</div>
<?
	if (sizeof($rolls)) {
		echo "\t\t\t\t<div id=\"postedRolls\">\n";
		echo "\t\t\t\t\t<h3>Posted Rolls</h3>\n";
		$visText = array(1 => '[Hidden Roll/Result]', '[Hidden Dice &amp; Roll]', '[Everything Hidden]');
		$hidden = FALSE;
		$showAll = FALSE;
		foreach ($rolls as $roll) {
			$hidden = FALSE;
			
			echo "\t\t\t\t\t<div class=\"rollInfo\">\n";
			echo $roll['visibility'] > 0?"\t\t\t\t\t\t<div class=\"hidden\">".$visText[$roll['visibility']]."</div>\n":'';
			echo "\t\t\t\t\t\t<div>";
			if ($roll['visibility'] <= 2) echo $roll['reason'];
			else { echo '<span class="hidden">'.$roll['reason']; $hidden = TRUE; }
			if ($roll['visibility'] <= 1) echo " - ({$roll['roll']}".($roll['ra']?', RA':'').')';
			else { echo ($hidden?'':'<span class="hidden">')." - ({$roll['roll']}".($roll['ra']?', RA':'').')'; $hidden = TRUE; }
			echo $hidden?'</span>':'';
			echo "</div>\n";
?>
						<select name="nVisibility_<?=$roll['rollID']?>" tabindex="<?=tabOrder();?>">
							<option value="0"<?=$roll['visibility'] == 0?' selected="selected"':''?>>Hide Nothing</option>
							<option value="1"<?=$roll['visibility'] == 1?' selected="selected"':''?>>Hide Roll/Result</option>
							<option value="2"<?=$roll['visibility'] == 2?' selected="selected"':''?>>Hide Dice &amp; Roll</option>
							<option value="3"<?=$roll['visibility'] == 3?' selected="selected"':''?>>Hide Everything</option>
						</select>
						<input type="hidden" name="oVisibility_<?=$roll['rollID']?>" value="<?=$roll['visibility']?>">
<?
			if ($roll['visibility'] == 0) echo "\t\t\t\t\t\t<div class=\"indent\">{$roll['indivRolls']} = {$roll['total']}</div>\n";
			else echo "\t\t\t\t\t\t<div class=\"indent\"><span class=\"hidden\">{$roll['indivRolls']} = {$roll['total']}</span></div>\n";
			echo "\t\t\t\t\t</div>\n";
//			echo "\t\t\t\t\t<div>{$roll['reason']} ({$roll['roll']}".($roll['ra']?', RA':'').")</div>\n";
//			echo "\t\t\t\t\t<div class=\"indent\">{$roll['indivRolls']} = {$roll['total']}</div>\n";
		}
		echo "\t\t\t\t</div>\n";
	}
?>
			<table id="rollsTable">
				<tr>
					<th class="reason">Reason</th>
					<th class="roll">Roll</th>
					<th class="reroll">Reroll Aces</th>
					<th class="visibility">Visibility</th>
				</tr>
<? for ($count = 1; $count <= 4; $count++) { ?>
				<tr>
					<td class="reason"><input type="text" name="roll_reason_<?=$count?>" maxlength="100"<?=isset($postInfo['roll_reason_'.$count])?' value="'.$postInfo['roll_reason_'.$count].'"':''?> tabindex="<?=tabOrder();?>"></td>
					<td class="roll"><input type="text" name="roll_roll_<?=$count?>" maxlength="50"<?=isset($postInfo['roll_roll_'.$count])?' value="'.$postInfo['roll_roll_'.$count].'"':''?> tabindex="<?=tabOrder();?>"></td>
					<td class="reroll"><input type="checkbox" name="roll_ra_<?=$count?>"<?=isset($postInfo['roll_ra_'.$count])?' checked="checked"':''?> tabindex="<?=tabOrder();?>"></td>
					<td class="visibility"><select name="roll_visibility_<?=$count?>" tabindex="<?=tabOrder();?>">
						<option value="0"<?=$postInfo['roll_visibility_'.$count] == 0?' selected="selected"':''?>>Hide Nothing</option>
						<option value="1"<?=$postInfo['roll_visibility_'.$count] == 1?' selected="selected"':''?>>Hide Roll/Result</option>
						<option value="2"<?=$postInfo['roll_visibility_'.$count] == 2?' selected="selected"':''?>>Hide Dice &amp; Roll</option>
						<option value="3"<?=$postInfo['roll_visibility_'.$count] == 3?' selected="selected"':''?>>Hide Everything</option>
					</select></td>
				</tr>
<? } ?>
			</table>
<!--			<p>If you click the roll button directly below, your post will be submitted, and you will be brought 
			<button type="submit" name="addRoll" class="btn_roll"></button>-->
			
<? } ?>
<?
	if ($permissions['addDraws']) {
		$gmCheck = $mysql->query("SELECT gms.primary FROM gms INNER JOIN games USING (gameID) WHERE gms.userID = $userID");
		if ($gmCheck->rowCount()) $deckInfos = $mysql->query('SELECT decks.deckID, decks.label, decks.type, decks.deck, decks.position FROM decks, games WHERE games.forumID = '.$forumID.' AND games.gameID = decks.gameID GROUP BY decks.deckID');
		else $deckInfos = $mysql->query('SELECT decks.deckID, decks.label, decks.type, decks.deck, decks.position FROM decks, games, characters, deckPermissions WHERE games.forumID = '.$forumID.' AND decks.gameID = characters.gameID AND characters.userID = '.$userID.' AND decks.deckID = deckPermissions.deckID AND deckPermissions.userID = '.$userID.' GROUP BY decks.deckID');
//		$mysql->query('SELECT decks.deckID, decks.label, decks.type, decks.deck, decks.position FROM decks, games, characters, deckPermissions WHERE games.forumID = '.$forumID.' AND ((decks.gameID = characters.gameID AND characters.userID = '.$userID.' AND decks.deckID = deckPermissions.deckID AND deckPermissions.userID = '.$userID.') OR (decks.gameID = games.gameID AND games.gmID = '.$userID.')) GROUP BY decks.deckID');
		if ($deckInfos->rowCount()) {
			echo "\n\t\t\t<h3 id=\"decksHeader\">Decks</h3>\n";
			echo "\t\t\t<p>Please remember, any cards you draw will be only visible to you until you reveal them. Reveal them by clicking them. An eye icon indicates they're visible, while an eye with a red slash through them indiates a hidden card.</p>\n";
			echo "\t\t\t<table id=\"decksTable\">\n";
			$firstDeck = TRUE;
			foreach ($deckInfos as $deckInfo) {
				if ($draws[$deckInfo['deckID']]) {
					$draw = $draws[$deckInfo['deckID']];
					echo "\t\t\t\t<tr><td colspan=\"2\">\n";
					echo "\t\t\t\t\t<b>".$deckInfo['label'].'</b> has '.(sizeof(explode('~', $deckInfo['deck'])) - $deckInfo['position'] + 1).' cards left.';
					echo "\t\t\t\t\t<p>Cards Drawn: ".$draw['reason']."</p>\n";
					$cardsDrawn = explode('~', $draw['cardsDrawn']);
					foreach ($cardsDrawn as $cardDrawn) echo "\t\t\t\t\t".'<img src="'.SITEROOT.'/images/cards/'.$draw['type'].'/'.$cardDrawn.'_mini.png" alt="'.cardText($cardDrawn, $draw['type']).'" title="'.cardText($cardDrawn, $draw['type']).'">'."\n";
					echo "\t\t\t\t</td></tr>\n";
				} else {
?>
				<tr class="deckTitle<?=$firstDeck?'':' titleBuffer'?>"><td class="label"><b><?=$deckInfo['label']?></b> has <?=sizeof(explode('~', $deckInfo['deck'])) - $deckInfo['position'] + 1?> cards left</td></tr>
				<tr>
					<td class="reason"><input type="text" name="deck_reason_<?=$deckInfo['deckID']?>" maxlength="100"<?=isset($postInfo['deck_reason_'.$deckInfo['deckID']])?' value="'.$postInfo['deck_reason_'.$deckInfo['deckID']].'"':''?> tabindex="<?=tabOrder();?>"></td>
					<td class="draw">Draw <input type="text" name="deck_draw_<?=$deckInfo['deckID']?>" maxlength="2"<?=isset($postInfo['deck_draw_'.$deckInfo['deckID']])?' value="'.$postInfo['deck_draw_'.$deckInfo['deckID']].'"':''?> tabindex="<?=tabOrder();?>"> cards</td>
				</tr>
<?
				}
				if ($firstDeck) $firstDeck = FALSE;
			}
			echo "\t\t\t</table>\n";
		}
	}
?>
			
			<div id="submitDiv" class="alignCenter">
            	<button type="submit" name="post" tabindex="<?=tabOrder();?>" class="btn_post"></button>
                <button type="submit" name="preview" tabindex="<?=tabOrder();?>" class="btn_preview"></button>
            </div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>