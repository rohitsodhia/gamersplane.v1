<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	addPackage('forum');

	$noChat = false;
	$firstPost = false;
	$editPost = $pathOptions[0] == 'editPost'?true:false;

	if ($editPost) {
		$postID = intval($pathOptions[1]);
		$post = new Post($postID);
		$threadManager = new ThreadManager($post->getThreadID());
		if ($postID == $threadManager->getThreadProperty('firstPostID')) 
			$firstPost = true;

		if ($post->getAuthor('userID') != $currentUser->userID && !$threadManager->getPermissions('moderate')) 
			$noChat = true;
		elseif ($threadManager->getThreadProperty('locked') && !$threadManager->getPermissions('moderate')) 
			$noChat = true;
		elseif (!$threadManager->getPermissions('write')) 
			$noChat = true;
	} elseif ($pathOptions[0] == 'newThread') {
		$firstPost = true;
		
		$forumID = intval($pathOptions[1]);
		$threadManager = new ThreadManager(null, $forumID);
		$threadManager->thread->forumID = $forumID;
		$post = new Post();
		if (!$threadManager->getPermissions('createThread')) $noChat = true;
	} elseif ($pathOptions[0] == 'post') {
		$threadID = intval($pathOptions[1]);
		try {
			$threadManager = new ThreadManager($threadID);
			$post = new Post();

			if ($threadManager->getThreadProperty('locked') || !$threadManager->getPermissions('write')) 
				$noChat = true;
			else {
				if (isset($_SESSION['message'])) {
					$post->message = $_SESSION['message'];
					unset($_SESSION['message']);
				} elseif (isset($_GET['quote'])) {
					$quoteID = intval($_GET['quote']);
					if ($quoteID) {
						$quoteInfo = $mysql->query("SELECT u.username, p.message FROM users u, posts p WHERE p.postID = {$quoteID} AND p.authorID = u.userID");
						$quoteInfo = $quoteInfo->fetch();
						$post->message = '[quote="'.$quoteInfo['username'].'"]'.$quoteInfo['message'].'[/quote]';
					}
				}
			}
		} catch (Exception $e) { $noChat = true; }
	} else $noChat = true;
	
	if ($noChat) { header('Location: /forums/'); exit; }
	
	$fillVars = $formErrors->getErrors('post');

	if ($_GET['preview']) 
		$fillVars = $_SESSION['previewVars'];
	else 
		unset($_SESSION['previewVars']);

	$gameID = false;
	$isGM = false;
	if ($threadManager->getForumProperty('gameID')) {
		$gameID = $threadManager->getForumProperty('gameID');
		$system = $mysql->query("SELECT system FROM games WHERE gameID = {$gameID}")->fetchColumn();
		
		$gmCheck = $mysql->query("SELECT isGM FROM players WHERE userID = {$currentUser->userID} AND gameID = ".$threadManager->getForumProperty('gameID'));
		if ($gmCheck->rowCount()) $isGM = true;

		require_once(FILEROOT."/includes/packages/{$system}Character.package.php");
		$charClass = $systems->systemClassName($system).'Character';
		$characterIDs = $mysql->query("SELECT characterID FROM characters WHERE gameID = {$gameID} AND userID = {$currentUser->userID}");
		$characters = array();
		while ($characterID = $characterIDs->fetchColumn()) {
			if ($character = new $charClass($characterID)) {
				$character->load();
				if (strlen($character->getName())) 
					$characters[$characterID] = $character;
			}
		}
	} else 
		$fixedGameMenu = false;

	$rollsAllowed = ($threadManager->getPermissions('addRolls') && $threadManager->getThreadProperty('allowRolls') || $threadManager->getPermissions('moderate'))?true:false;
	$drawsAllowed = false;
	if ($gameID && $threadManager->getPermissions('addDraws')) {
		$gmCheck = $mysql->query("SELECT isGM FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID} AND isGM = 1");
		if ($gmCheck->rowCount()) 
			$decks = $mysql->query('SELECT deckID, label, type, deck, position FROM decks WHERE gameID = '.$gameID);
		else 
			$decks = $mysql->query("SELECT d.deckID, d.label, d.type, d.deck, d.position FROM decks d INNER JOIN deckPermissions p ON d.deckID = p.deckID AND p.userID = {$currentUser->userID} WHERE d.gameID = {$gameID}");
		if ($decks->rowCount()) $drawsAllowed = true;
	}

	require_once(FILEROOT.'/header.php');
?>
<?	if ($_GET['errors'] && $formErrors->errorsExist()) { ?>
		<div class="alertBox_error"><ul>
<?
		if ($formErrors->checkError('overdrawn')) echo "			<li>Incorrect number of cards drawn.</li>\n";
		if ($formErrors->checkError('noTitle')) echo "			<li>You can't leave the title blank.</li>\n";
		if ($formErrors->checkError('noMessage')) echo "			<li>You can't leave the message blank.</li>\n";
		if ($formErrors->checkError('noDrawReason')) echo "			<li>You left draw reasons blank.</li>\n";
		if ($formErrors->checkError('noPoll')) echo "			<li>You did not provide a poll question.</li>\n";
		if ($formErrors->checkError('noOptions')) echo "			<li>You did not provide poll options or provided too few (minimum 2).</li>\n";
		if ($formErrors->checkError('noOptionsPerUser')) echo "			<li>You did not provide a valid number for \"Options per user\".</li>\n";
		if ($formErrors->checkError('badRoll')) echo "			<li>One or more of your roll entries are malformed. Please make sure they are in the right format.</li>\n";
?>
		</ul></div>
<?
	}
	$threadManager->forumManager->displayBreadcrumbs();
?>
		<h1 class="headerbar"><?=($post->postID || $pathOptions[0] == 'post')?($editPost?'Edit post':'Post a reply').' - '.printReady($threadManager->getThreadProperty('title')):'New Thread'?></h1>
		
<?	if ($_GET['preview'] && strlen($fillVars['message']) > 0) { ?>
		<h2>Preview:</h2>
		<div id="preview">
			<?=BBCode2Html(printReady($fillVars['message']))."\n"?>
		</div>
		<hr>
		
<? } ?>
		<form method="post" action="/forums/process/post/">
<?
	if ($pathOptions[0] == 'newThread') echo "\t\t\t".'<input type="hidden" name="new" value="'.$forumID.'">'."\n";
	elseif ($pathOptions[0] == 'editPost') echo "\t\t\t".'<input type="hidden" name="edit" value="'.$postID.'">'."\n";
	elseif ($pathOptions[0] == 'post') echo "\t\t\t".'<input type="hidden" name="threadID" value="'.$threadID.'">'."\n";
	
	if ($fillVars) 
		$title = printReady($fillVars['title']);
	elseif (!strlen($post->getTitle()) && $threadManager->getThreadID()) 
		$title = 'Re: '.$threadManager->getThreadProperty('title');
	else 
		$title = printReady($post->title, array('stripslashes'));
?>
			<div id="basicPostInfo" class="hbMargined">
				<div class="table">
					<div>
						<label for="title">Title:</label>
						<div><input id="title" type="text" name="title" maxlength="50" tabindex="<?=tabOrder();?>" value="<?=htmlentities($title)?>" class="titleInput"></div>
					</div>
<?	
	if ($gameID && sizeof($characters)) {
		$currentChar = $post->postAs;
		if ($fillVars) 
			$currentChar = $fillVars['postAs'];
?>
					<div class="tr">
						<label>Post As:</label>
						<div><select name="postAs">
							<option value="p"<?=$currentChar == null?' selected="selected"':''?>>Player</option>
<?		foreach ($characters as $character) { ?>
							<option value="<?=$character->getCharacterID()?>"<?=$currentChar == $character->getCharacterID()?' selected="selected"':''?>><?=$character->getName()?></option>
<?		} ?>
						</select></div>
					</div>
<?	} ?>
				</div>
				<textarea id="messageTextArea" name="message" tabindex="<?=tabOrder();?>"><?=$fillVars?$fillVars['message']:$post->message?></textarea>
			</div>
			
<?	if ($firstPost || $rollsAllowed || $drawsAllowed) { ?>
			<div id="optionControls" class="clearfix hbdMargined"><div class="wingDiv sectionControls floatLeft">
				<div>
					<a href="" class="section_options<?=$firstPost?' current':''?>">Options</a>
					<a href="" class="section_poll">Poll</a>
<?		if ($rollsAllowed || $drawsAllowed) { ?>
					<a href="" class="section_rolls_decks<?=!$firstPost?' current':''?>">Rolls and Decks</a>
<?		} ?>
				</div>
				<div class="wing dlWing"></div>
				<div class="wing drWing"></div>
			</div></div>
<?	} ?>
<?	if (($firstPost) || $rollsAllowed || $drawsAllowed) { ?>
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
<?
		if ($threadManager->getPermissions('moderate')) {
			$sticky = $threadManager->getThreadProperty('sticky');
			if ($fillVars) 
				$sticky = $fillVars['sticky'];
?>
				<p><input type="checkbox" name="sticky"<?=$sticky?' checked="checked"':''?>> Sticky thread</p>
<?
		}
		if ($threadManager->getPermissions('moderate')) {
			$locked = $threadManager->getThreadProperty('locked');
			if ($fillVars) 
				$locked = $fillVars['locked'];
?>
				<p><input type="checkbox" name="locked"<?=$locked?' checked="checked"':''?>> Lock thread</p>
<?
		}
		if ($threadManager->getPermissions('addRolls')) {
			$addRolls = $rollsAllowed || ($pathOptions[0] == 'newThread' && $gameID);
			if ($fillVars) 
				$addRolls = $fillVars['allowRolls'];
?>
				<p><input type="checkbox" name="allowRolls"<?=$addRolls?' checked="checked"':''?>> Allow adding rolls to posts (if this box is unchecked, any rolls added to this thread will be ignored)</p>
<?
		}
		if ($threadManager->getPermissions('addDraws')) {
			$addDraws = $drawsAllowed || ($pathOptions[0] == 'newThread' && $gameID);
			if ($fillVars) 
				$addDraws = $fillVars['allowDraws'];
?>
				<p><input type="checkbox" name="allowDraws"<?=$addDraws?' checked="checked"':''?>> Allow adding deck draws to posts (if this box is unchecked, any draws added to this thread will be ignored)</p>
<?		} ?>
			</div>

			<div id="poll" class="section_poll hbdMargined hideDiv">
<?		if ($pathOptions[0] == 'editPost') { ?>
				<div class="clearfix">
					<label for="allowRevoting"><b>Delete Poll:</b></label>
					<div><input id="deletePoll" type="checkbox" name="deletePoll"> If checked, your poll will be deleted and cannot be recovered.</div>
				</div>
<?		} ?>
				<div class="tr clearfix">
					<label for="pollQuestion" class="textLabel"><b>Poll Question:</b></label>
					<div><input id="pollQuestion" type="text" name="poll" value="<?=$fillVars?$fillVars['poll']:$threadManager->getPollProperty('question')?>" class="borderBox"></div>
				</div>
				<div class="tr clearfix">
					<label for="pollOption" class="textLabel">
						<b>Poll Options:</b>
						<p>Place each option on a new line. You may enter up to <b>25</b> options.</p>
					</label>
					<div><textarea id="pollOptions" name="pollOptions"><?
			if ($fillVars) echo $fillVars['pollOptions'];
			else {
				$options = array();
				foreach ($threadManager->getPollProperty('options') as $option) 
					$options[] = $option->option;
				echo implode("\n", $options);
			}
?></textarea></div>
				</div>
				<div class="tr clearfix">
					<label for="optionsPerUser" class="textLabel"><b>Options per user:</b></label>
					<div><input id="optionsPerUser" type="text" name="optionsPerUser" value="<?=$fillVars?$fillVars['optionsPerUser']:$threadManager->getPollProperty('optionsPerUser')?>" class="borderBox"></div>
				</div>
				<div class="tr clearfix">
					<label for="allowRevoting"><b>Allow Revoting:</b></label>
<?
		$allowRevoting = $threadManager->getPollProperty('allowRevoting');
		if ($fillVars) $allowRevoting = $fillVars['allowRevoting'];
?>
					<div><input id="allowRevoting" type="checkbox" name="allowRevoting" <?=$allowRevoting?' checked="checked"':''?>> If checked, people will be allowed to change their votes.</div>
				</div>
			</div>
<?
	}
	if ($rollsAllowed || $drawsAllowed) {
?>
			<div id="rolls_decks" class="section_rolls_decks hbdMargined<?=$firstPost?' hideDiv':''?>">
<?		if ($rollsAllowed) { ?>
				<div id="rolls">
<?			if ($drawsAllowed) { ?>
					<h3 id="rollsHeader">Rolls</h3>
<?			} ?>
					<div id="rollExplination">
						For "Basic" type rolls, Enter the text roll in the following format:<br>
						(number of dice)d(dice type)+/-(modifier), i.e. 2d6+4, 1d10-2<br>
						The roll will automatically be added to your post when you submit it.
					</div>
<?			if (sizeof($post->rolls)) { ?>
					<div id="postedRolls">
						<h3>Posted Rolls</h3>
<?
				$visText = array(1 => '[Hidden Roll/Result]', '[Hidden Dice &amp; Roll]', '[Everything Hidden]');
				$hidden = false;
				$showAll = false;
				$first = true;
				foreach ($post->rolls as $roll) {
					$showAll = $isGM || $currentUser->userID == $post->author->userID?true:false;
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
							<option value="sweote">SWEOTE</option>
							<option value="fate">Fate</option>
						</select>
						<button type="submit" class="fancyButton">Add</button>
					</div>
					<div id="newRolls">
<?
			if (isset($fillVars['rolls'])) {
				foreach ($fillVars['rolls'] as $count => $roll) {
					rollTR($count, (object) $roll);
				}
			}
?>
					</div>
				</div>
<?		} ?>
<?		if ($drawsAllowed) { ?>
				<div id="draws">
<?			if ($rollsAllowed) { ?>
					<h3 id="decksHeader">Decks</h3>
<?			} ?>
					<p>Please remember, any cards you draw will be only visible to you until you reveal them. Reveal them by clicking them. An eye icon indicates they're visible, while an eye with a red slash through them indiates a hidden card.</p>
					<table id="decksTable">
<?
			$firstDeck = true;
			foreach ($decks as $deck) {
				if ($draws[$deck['deckID']]) {
					$draw = $draws[$deck['deckID']];
?>
						<tr><td colspan="2">
							<b><?=$deck['label']?></b> has <?=(sizeof(explode('~', $deck['deck'])) - $deck['position'] + 1)?> cards left.
							<p>Cards Drawn: <?=$draw['reason']?></p>
<?
					$cardsDrawn = explode('~', $draw['cardsDrawn']);
					foreach ($cardsDrawn as $cardDrawn) echo "\t\t\t\t\t\t".getCardImg($cardDrawn, $deck['type'], 'mini')."\n";
?>
						</td></tr>
<?				} else { ?>
						<tr class="deckTitle<?=$firstDeck?'':' titleBuffer'?>"><td class="label"><b><?=$deck['label']?></b> has <?=sizeof(explode('~', $deck['deck'])) - $deck['position'] + 1?> cards left</td></tr>
						<tr>
							<td class="reason"><input type="text" name="decks[<?=$deck['deckID']?>][reason]" maxlength="100" value="<?=$fillVars?$fillVars[$deck['deckID']]['reason']:''?>" tabindex="<?=tabOrder();?>"></td>
							<td class="draw">Draw <input type="text" name="decks[<?=$deck['deckID']?>][draw]" maxlength="2" value="<?=$fillVars?$fillVars[$deck['deckID']]['draw'].'"':''?>" tabindex="<?=tabOrder();?>"> cards</td>
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
			<input type="hidden" name="postURL" value="<?=$_SESSION['currentURL']?>">
			
			<div id="submitDiv" class="alignCenter">
				<button type="submit" name="post" tabindex="<?=tabOrder();?>" class="fancyButton"><?=$editPost?'Save':'Post'?></button>
				<button type="submit" name="preview" tabindex="<?=tabOrder();?>" class="fancyButton">Preview</button>
            </div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>