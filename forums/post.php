<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	addPackage('tools');

	$noChat = false;

	$firstPost = false;
	$editPost = $pathOptions[0] == 'editPost'?true:false;

	if ($editPost) {
		$postID = intval($pathOptions[1]);
		$threadInfo = $mysql->query("SELECT t.forumID, t.threadID, t.sticky, t.allowRolls, t.allowDraws, f.heritage FROM threads t, posts p, forums f WHERE t.threadID = p.threadID AND f.forumID = t.forumID AND p.postID = $postID");
		list($forumID, $threadID, $sticky, $allowRolls, $allowDraws, $heritage) = $threadInfo->fetch(PDO::FETCH_NUM);
		
		$rolls = $mysql->query("SELECT p.postID, r.rollID, r.type, r.reason, r.roll, r.indivRolls, r.results, r.visibility, r.extras FROM posts p, rolls r WHERE p.postID = {$postID} AND r.postID = p.postID ORDER BY r.rollID");
		$temp = array();
		foreach ($rolls as $rollInfo) {
			$rollObj = RollFactory::getRoll($rollInfo['type']);
			$rollObj->forumLoad($rollInfo);
			$temp[] = $rollObj;
		}
		$rolls = $temp;
		
		$draws = $mysql->query('SELECT deckDraws.deckID, deckDraws.type, deckDraws.cardsDrawn, deckDraws.reason FROM posts, deckDraws WHERE posts.postID = '.$postID.' AND deckDraws.postID = posts.postID');
		$temp = array();
		foreach ($draws as $drawInfo) $temp[$drawInfo['deckID']] = $drawInfo;
		$draws = $temp;
		
		$postInfo = $mysql->query("SELECT t.forumID, p.postID, p.title postTitle, p.authorID, p.postAs, p.message, first.title threadTitle, first.postID fpPostID, t.locked, t.allowRolls, t.allowDraws FROM posts p, posts first, threads t, threads_relPosts relPosts WHERE p.postID = {$postID} AND p.threadID = t.threadID AND t.threadID = relPosts.threadID and relPosts.firstPostID = first.postID");
		$postInfo = $postInfo->fetch();
		
		if ($postInfo['fpPostID'] == $postID) {
			$firstPost = true;
			$pollInfo = $mysql->query("SELECT * FROM forums_polls WHERE threadID = $threadID");
			if ($pollInfo->rowCount()) {
				$postInfo += $pollInfo->fetch();
				$pollOptions = $mysql->query("SELECT `option` FROM forums_pollOptions WHERE threadID = $threadID");
				$postInfo['pollOptions'] = array();
				foreach ($pollOptions as $pollOption) $postInfo['pollOptions'][] = $pollOption['option'];
				$postInfo['pollOptions'] = implode("\n", $postInfo['pollOptions']);
			}
		}
		
		$permissions = retrievePermissions($currentUser->userID, $postInfo['forumID'], 'write, moderate, addPoll, addRolls, addDraws', true);
		if ($postInfo['authorID'] != $currentUser->userID/* && $postInfo->rowCount() > 0*/) {
			if ($permissions['moderate'] != 1) $noChat = true;
		} elseif (($postInfo['locked'] && !$permissions['moderate'])) $noChat = true;
		else {
			if (!$permissions['write']) $noChat = true;
		}
	} elseif ($pathOptions[0] == 'newThread') {
		$firstPost = true;
		
		$forumID = intval($pathOptions[1]);
		$heritage = $mysql->query('SELECT heritage FROM forums WHERE forumID = '.$forumID);
		$heritage = $heritage->fetchColumn();
		$gameForum = (intval(substr($heritage, 0, 3)) == 2)?true:false;
		$permissions = retrievePermissions($currentUser->userID, $forumID, 'createThread, addPoll, addRolls, addDraws, moderate', true);
		if ($permissions['createThread'] != 1) $noChat = true;
	} elseif ($pathOptions[0] == 'post') {
		$threadID = intval($pathOptions[1]);
		$threadInfo = $mysql->query("SELECT threads.forumID, first.title, threads.locked, threads.allowRolls, threads.allowDraws, forums.heritage FROM threads, forums, threads_relPosts relPosts, posts first WHERE threads.threadID = $threadID AND forums.forumID = threads.forumID AND threads.threadID = relPosts.threadID AND relPosts.firstPostID = first.postID LIMIT 1");
		if ($threadInfo->rowCount() == 0) { $noChat = true; break; }
		if (isset($_SESSION['message'])) {
			$postInfo['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		list($forumID, $postInfo['threadTitle'], $locked, $allowRolls, $postInfo['allowDraws'], $heritage) = $threadInfo->fetch(PDO::FETCH_NUM);
		$permissions = retrievePermissions($currentUser->userID, $forumID, 'write, moderate, addRolls, addDraws', true);
		if ($permissions['write'] != 1 && !$locked) { $noChat = true; break; }
		
		$quoteID = intval($_GET['quote']);
		if ($quoteID) {
			$quoteInfo = $mysql->query("SELECT users.username, posts.message FROM users, posts WHERE posts.postID = {$quoteID} AND posts.authorID = users.userID");
			$quoteInfo = $quoteInfo->fetch();
			$postInfo['message'] = '[quote="'.$quoteInfo['username'].'"]'.$quoteInfo['message'].'[/quote]';
		}
	} else $noChat = true;
	
	if ($noChat) { header('Location: /forums/'); exit; }
	
	if ($_SESSION['errors']) {
		if ($_SESSION['lastURL'] == '/forums/process/post') {
			$errors = $_SESSION['errors'];
			if (isset($postInfo)) $postInfo = $_SESSION['errorVals'] + $postInfo;
			else $postInfo = $_SESSION['errorVals'];
			$postInfo['postTitle'] = $postInfo['title'];
		}
		if ($_SESSION['lastURL'] != '/forums/process/post' || time() > $_SESSION['errorTime']) {
			unset($_SESSION['errors']);
			unset($_SESSION['errorVals']);
		}
	}
	
	if ($_GET['preview']) {
		if (isset($postInfo)) $postInfo = $_SESSION['previewVars'] + $postInfo;
		else $postInfo = $_SESSION['previewVars'];
		$postInfo['postTitle'] = $postInfo['title'];
	}

	$heritage = explode('-', $heritage);
	foreach ($heritage as $key => $value) $heritage[$key] = intval($value);
	$gameID = false;
	$isGM = false;
	if ($heritage[0] == 2 && $forumID != 10) {
		$gameID = $mysql->query('SELECT gameID, systemID FROM games WHERE forumID = '.intval($heritage[1]));
		list($gameID, $systemID) = $gameID->fetch(PDO::FETCH_NUM);
		
		$gmCheck = $mysql->query("SELECT players.isGM FROM players INNER JOIN games USING (gameID) WHERE games.gameID = {$gameID} AND players.userID = {$currentUser->userID}");
		if ($gmCheck->rowCount()) $isGM = true;

		$system = $systems->getShortName($systemID);
		require_once(FILEROOT."/includes/packages/{$system}Character.package.php");
		$charClass = $system.'Character';
		$characterIDs = $mysql->query("SELECT characterID FROM characters WHERE gameID = {$gameID} AND userID = {$currentUser->userID}");
		$characters = array();
		while ($characterID = $characterIDs->fetchColumn()) {
			if ($character = new $charClass($characterID)) {
				$character->load();
				if (strlen($character->getName())) $characters[$characterID] = $character;
			}
		}
	}

	$rollsAllowed = ($permissions['addRolls'] && $allowRolls || $permissions['moderate'])?true:false;
	$drawsAllowed = false;
	if ($permissions['addDraws']) {
		$gmCheck = $mysql->query("SELECT players.isGM FROM players INNER JOIN games USING (gameID) WHERE players.userID = {$currentUser->userID}");
		if ($gmCheck->rowCount()) $deckInfos = $mysql->query('SELECT decks.deckID, decks.label, decks.type, decks.deck, decks.position FROM decks, games WHERE games.forumID = '.$forumID.' AND games.gameID = decks.gameID GROUP BY decks.deckID');
		else $deckInfos = $mysql->query("SELECT decks.deckID, decks.label, decks.type, decks.deck, decks.position FROM decks, games, characters, deckPermissions WHERE games.forumID = {$forumID} AND decks.gameID = characters.gameID AND characters.userID = {$currentUser->userID} AND decks.deckID = deckPermissions.deckID AND deckPermissions.userID = {$currentUser->userID} GROUP BY decks.deckID");
		if ($deckInfos->rowCount()) $drawsAllowed = true;
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
		<h1 class="headerbar"><?=($postInfo['postID'] || $pathOptions[0] == 'post')?($editPost?'Edit post':'Post a reply').' - '.printReady($postInfo['threadTitle']):'New Thread'?></h1>
		
<? if ($_GET['preview'] && sizeof($_SESSION['previewVars']) && strlen($postInfo['message']) > 0) { ?>
		<h2>Preview:</h2>
		<div id="preview">
			<?=BBCode2Html(printReady($postInfo['message']))."\n"?>
		</div>
		<hr>
		
<? } ?>
		<form method="post" action="/forums/process/post">
<?
	if ($pathOptions[0] == 'newThread') echo "\t\t\t".'<input type="hidden" name="new" value="'.$forumID.'">'."\n";
	elseif ($pathOptions[0] == 'editPost') echo "\t\t\t".'<input type="hidden" name="edit" value="'.$postID.'">'."\n";
	elseif ($pathOptions[0] == 'post') echo "\t\t\t".'<input type="hidden" name="threadID" value="'.$threadID.'">'."\n";
	
	if (isset($postInfo, $postInfo['postTitle'])) $title = printReady($postInfo['postTitle'], array('stripslashes'));
	elseif (isset($postInfo['threadTitle'])) $title = (substr($postInfo['threadTitle'], 0, 4) != 'Re: '?'Re: ':'').$postInfo['threadTitle'];
?>
			<div id="basicPostInfo" class="hbMargined">
				<div class="table">
					<div>
						<label for="title">Title:</label>
						<div><input id="title" type="text" name="title" maxlength="50" tabindex="<?=tabOrder();?>" value="<?=htmlentities($title)?>" class="titleInput"></div>
					</div>
<?	if ($gameID && sizeof($characters)) { ?>
					<div class="tr">
						<label>Post As:</label>
						<div><select name="postAs">
							<option value="p"<?=$postInfo['postAs'] == null?' selected="selected"':''?>>Player</option>
<?		foreach ($characters as $character) { ?>
							<option value="<?=$character->getCharacterID()?>"<?=$postInfo['postAs'] == $character->getCharacterID()?' selected="selected"':''?>><?=$character->getName()?></option>
<?		} ?>
						</select></div>
					</div>
<?	} ?>
				</div>
				<textarea id="messageTextArea" name="message" tabindex="<?=tabOrder();?>"><?=printReady($postInfo['message'], array('stripslashes'))?></textarea>
			</div>
			
<?	if ($firstPost && ($permissions['addPoll'] || $rollsAllowed || $drawsAllowed)) { ?>
			<div id="optionControls" class="clearfix hbdMargined"><div class="wingDiv sectionControls floatLeft">
				<div>
					<a href="" class="section_options<?=$firstPost?' current':''?>">Options</a>
<?		if ($permissions['addPoll']) { ?>
					<a href="" class="section_poll">Poll</a>
<?		} ?>
<?		if ($rollsAllowed || $drawsAllowed) { ?>
					<a href="" class="section_rolls_decks<?=!$firstPost?' current':''?>">Rolls and Decks</a>
<?		} ?>
				</div>
				<div class="wing dlWing"></div>
				<div class="wing drWing"></div>
			</div></div>
<?	} ?>
<?	if ($firstPost || $rollsAllowed || $drawsAllowed) { ?>
			<h2 class="headerbar hbDark">
<?		if ($firstPost) { ?>
				<span class="section_options">Thread Options</span>
				<span class="section_poll hideDiv">Poll</span>
<?		} ?>
				<span class="section_rolls_decks<?=$firstPost?' hideDiv':''?>">Rolls and Decks</span>
			</h2>
<?	} ?>
			
<?	if ($firstPost) { ?>
			<div id="threadOptions" class="section_options hbdMargined">
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
			<div id="poll" class="section_poll hbdMargined hideDiv">
<?			if ($pathOptions[0] == 'editPost') { ?>
				<div class="clearfix">
					<label for="allowRevoting"><b>Delete Poll:</b></label>
					<div><input id="deletePoll" type="checkbox" name="deletePoll"> If checked, your poll will be deleted and cannot be recovered.</div>
				</div>
<?			} ?>
				<div class="tr clearfix">
					<label for="pollQuestion" class="textLabel"><b>Poll Question:</b></label>
					<div><input id="pollQuestion" type="text" name="poll" value="<?=$postInfo['poll']?>" class="borderBox"></div>
				</div>
				<div class="tr clearfix">
					<label for="pollOption" class="textLabel">
						<b>Poll Options:</b>
						<p>Place each option on a new line. You may enter up to <b>25</b> options.</p>
					</label>
					<div><textarea id="pollOptions" name="pollOptions"><?=$postInfo['pollOptions']?></textarea></div>
				</div>
				<div class="tr clearfix">
					<label for="optionsPerUser" class="textLabel"><b>Options per user:</b></label>
					<div><input id="optionsPerUser" type="text" name="optionsPerUser" value="<?=isset($postInfo['optionsPerUser'])?$postInfo['optionsPerUser']:'1'?>" class="borderBox"></div>
				</div>
				<div class="tr clearfix">
					<label for="allowRevoting"><b>Allow Revoting:</b></label>
					<div><input id="allowRevoting" type="checkbox" name="allowRevoting" <?=isset($postInfo['allowRevoting']) && $postInfo['allowRevoting']?' checked="checked"':''?>> If checked, people will be allowed to change their votes.</div>
				</div>
			</div>
<?
		}
	}
	if ($rollsAllowed || $drawsAllowed) {
?>
			<div id="rolls_decks" class="section_rolls_decks hbdMargined<?=$firstPost?' hideDiv':''?>">
<?
		if ($rollsAllowed) {
?>
				<div id="rolls">
<?
			if ($drawsAllowed) {
?>
					<h3 id="rollsHeader">Rolls</h3>
<?			} ?>
					<div id="rollExplination">
						For "Basic" type rolls, Enter the text roll in the following format:<br>
						(number of dice)d(dice type)+/-(modifier), i.e. 2d6+4, 1d10-2<br>
						The roll will automatically be added to your post when you submit it.
					</div>
<?			if (sizeof($rolls)) { ?>
					<div id="postedRolls">
						<h3>Posted Rolls</h3>
<?
				$visText = array(1 => '[Hidden Roll/Result]', '[Hidden Dice &amp; Roll]', '[Everything Hidden]');
				$hidden = false;
				$showAll = false;
				$first = true;
				foreach ($rolls as $roll) {
					$showAll = $isGM || $currentUser->userID == $postInfo['userID']?true:false;
					$hidden = false;
?>
						<div class="rollInfo">
							<select name="nVisibility[<?=$roll->getRollID()?>]" tabindex="<?=tabOrder();?>">
								<option value="0"<?=$roll->getVisibility() == 0?' selected="selected"':''?>>Hide Nothing</option>
								<option value="1"<?=$roll->getVisibility() == 1?' selected="selected"':''?>>Hide Roll/Result</option>
								<option value="2"<?=$roll->getVisibility() == 2?' selected="selected"':''?>>Hide Dice &amp; Roll</option>
								<option value="3"<?=$roll->getVisibility() == 3?' selected="selected"':''?>>Hide Everything</option>
							</select>
							<div>
<?
					$roll->showHTML($showAll);
?>
							</div>
							<input type="hidden" name="oVisibility[<?=$roll->getRollID()?>]" value="<?=$roll->getVisibility()?>">
						</div>
<?				} ?>
					</div>
<?			} ?>
					<div id="addRoll">
						<span>Add new roll: </span>
						<select>
							<option value="basic">Basic</option>
<!--							<option value="sweote">SWEOTE</option>-->
							<option value="fate">Fate</option>
						</select>
						<button type="submit" class="fancyButton">Add</button>
					</div>
					<div id="newRolls">
<?
			if (isset($postInfo['rolls'])) { foreach ($postInfo['rolls'] as $count => $roll) {
				rollTR($count, $roll->type, $roll);
			} }
?>
					</div>
				</div>
<?		} ?>
<?
		if ($drawsAllowed) {
?>
				<div id="draws">
<?
			if ($rollsAllowed) {
?>
					<h3 id="decksHeader">Decks</h3>
<?			} ?>
					<p>Please remember, any cards you draw will be only visible to you until you reveal them. Reveal them by clicking them. An eye icon indicates they're visible, while an eye with a red slash through them indiates a hidden card.</p>
					<table id="decksTable">
<?
			$firstDeck = true;
			foreach ($deckInfos as $deckInfo) {
				if ($draws[$deckInfo['deckID']]) {
					$draw = $draws[$deckInfo['deckID']];
?>
						<tr><td colspan="2">
							<b><?=$deckInfo['label']?></b> has <?=(sizeof(explode('~', $deckInfo['deck'])) - $deckInfo['position'] + 1)?> cards left.
							<p>Cards Drawn: <?=$draw['reason']?></p>
<?
					$cardsDrawn = explode('~', $draw['cardsDrawn']);
					foreach ($cardsDrawn as $cardDrawn) echo "\t\t\t\t\t\t".getCardImg($cardDrawn, $deckInfo['type'], 'mini')."\n";
?>
						</td></tr>
<?				} else { ?>
						<tr class="deckTitle<?=$firstDeck?'':' titleBuffer'?>"><td class="label"><b><?=$deckInfo['label']?></b> has <?=sizeof(explode('~', $deckInfo['deck'])) - $deckInfo['position'] + 1?> cards left</td></tr>
						<tr>
							<td class="reason"><input type="text" name="decks[<?=$deckInfo['deckID']?>][reason]" maxlength="100"<?=isset($postInfo['deck'][$deckInfo['deckID']]['reason'])?' value="'.$postInfo['deck'][$deckInfo['deckID']]['reason'].'"':''?> tabindex="<?=tabOrder();?>"></td>
							<td class="draw">Draw <input type="text" name="decks[<?=$deckInfo['deckID']?>][draw]" maxlength="2"<?=isset($postInfo['deck'][$deckInfo['deckID']]['draw'])?' value="'.$postInfo['deck'][$deckInfo['deckID']]['draw'].'"':''?> tabindex="<?=tabOrder();?>"> cards</td>
						</tr>
<?
				}
				if ($firstDeck) $firstDeck = false;
			}
?>
					</table>
				</div>
<?
		}
?>
			</div>
<?
	}
?>
			
			<div id="submitDiv" class="alignCenter">
				<button type="submit" name="post" tabindex="<?=tabOrder();?>" class="fancyButton"><?=$editPost?'Save':'Post'?></button>
				<button type="submit" name="preview" tabindex="<?=tabOrder();?>" class="fancyButton">Preview</button>
            </div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>