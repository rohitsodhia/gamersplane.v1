<?php
	$responsivePage=true;
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	addPackage('forum');
	if($currentUser->addPostNavigateWarning()){
		$addJSFiles = Array('forums/unsaved-work.js','forums/postingPage.js','postPolls.js');
	}else{
		$addJSFiles = Array('forums/postingPage.js','postPolls.js');
	}


	$noChat = false;
	$firstPost = false;
	$editPost = $pathOptions[0] == 'editPost';

	if ($editPost) {
		$postID = intval($pathOptions[1]);
		$post = new Post($postID);
		$threadManager = new ThreadManager($post->getThreadID());
		if ($postID == $threadManager->getThreadProperty('firstPostID')) {
			$firstPost = true;
		}

		if ($post->getAuthor('userID') != $currentUser->userID && !$threadManager->getPermissions('moderate')) {
			$noChat = true;
		} elseif ($threadManager->getThreadProperty('states[locked]') && !$threadManager->getPermissions('moderate')) {
			$noChat = true;
		} elseif (!$threadManager->getPermissions('write')) {
			$noChat = true;
		}

		$post->loadRolls();
	} elseif ($pathOptions[0] == 'newThread') {
		$firstPost = true;

		$forumID = intval($pathOptions[1]);
		$threadManager = new ThreadManager(null, $forumID);
		$threadManager->thread->forumID = $forumID;
		$post = new Post();
		if (!$threadManager->getPermissions('createThread')) {
			$noChat = true;
		}
	} elseif ($pathOptions[0] == 'post') {
		$threadID = intval($pathOptions[1]);
		try {
			$threadManager = new ThreadManager($threadID);
			$post = new Post();

			if ($threadManager->getThreadProperty('states[locked]') || !$threadManager->getPermissions('write')) {
				$noChat = true;
			} else {
				if (isset($_SESSION['message'])) {
					$post->postAs = $_SESSION['postAs'];
					$post->message = $_SESSION['message'];
					unset($_SESSION['message']);
				} elseif (isset($_GET['quote'])) {
					$quoteID = intval($_GET['quote']);
					if ($quoteID) {
						$quoteInfo = $mysql->query("SELECT u.username, p.message FROM users u, posts p WHERE p.postID = {$quoteID} AND p.authorID = u.userID");
						$quoteInfo = $quoteInfo->fetch();
						$gameID = $threadManager->forumManager->forums[$threadManager->getThreadProperty('forumID')]->gameID;
						if ($gameID) {
							$game = $mongo->games->findOne(
								[
									'gameID' => (int) $gameID,
									'players' => ['$elemMatch' => [
										'user.userID' => $currentUser->userID,
										'isGM' => true
									]]
								],
								['projection' => ['players.$' => true]]
							);
							$isGM = $game['players'][0]['isGM'];
							if (!$isGM) {
								$quoteInfo['message'] = Post::cleanNotes($quoteInfo['message']);
							}
						}
						else{
							$quoteInfo['message'] = Post::cleanNotes($quoteInfo['message']);
						}

						$post->message = '[quote="' . $quoteInfo['username'] . '"]' . $quoteInfo['message'] . '[/quote]';
					}
				}
			}
		} catch (Exception $e) { $noChat = true; }
	} else {
		$noChat = true;
	}

	if ($noChat) { header('Location: /forums/'); exit; }

	$fillVars = $formErrors->getErrors('post');

	if ($_GET['preview']) {
		$fillVars = $_SESSION['previewVars'];
	} else {
		unset($_SESSION['previewVars']);
	}

	$gameID = false;
	$isGM = false;
	if ($threadManager->getForumProperty('gameID')) {
		$gameID = (int) $threadManager->getForumProperty('gameID');
		$returnFields = ['system' => true, 'players' => true];
		if ($threadManager->getPermissions('addDraws')) {
			$returnFields['decks'] = true;
		}
		$game = $mongo->games->findOne(['gameID' => $gameID], ['projection' => $returnFields]);
		$system = $game['system'];
		$isGM = false;
		foreach ($game['players'] as $player) {
			if ($player['user']['userID'] == $currentUser->userID) {
				if ($player['isGM']) {
					$isGM = true;
				}
				break;
			}
		}

		$rCharacters = $mongo->characters->find(
			[
				'game.gameID' => $gameID,
				'game.approved' => true,
				'user.userID' => $currentUser->userID
			],
			['projection' => ['characterID' => true, 'name' => true]]
		);
		$characters = [];
		foreach ($rCharacters as $character) {
			if (strlen($character['name'])) {
				$characters[$character['characterID']] = $character['name'];
			}
		}

		$pcCharacters = [];
		if($isGM){
			$rPcCharacters = $mongo->characters->find(
				[
					'game.gameID' => $gameID,
					'game.approved' => true,
					'user.userID' => ['$ne'=>$currentUser->userID]
				],
				['projection' => ['characterID' => true, 'name' => true]]
			);
			foreach ($rPcCharacters as $character) {
				if (strlen($character['name'])) {
					$pcCharacters[$character['characterID']] = $character['name'];
				}
			}
		}

	} else {
		$fixedGameMenu = false;
	}

	$rollsAllowed = $threadManager->getThreadProperty('allowRolls') ? true : false;
	$drawsAllowed = false;
	if ($gameID && $threadManager->getPermissions('addDraws')) {
		$decks = $game['decks'];
		if (!$isGM) {
			foreach ($decks as $key => $deck) {
				if (!in_array($currentUser->userID, $deck['permissions'])) {
					unset($decks[$key]);
				}
			}
			$decks = array_values($decks);
		}
		if (sizeof($decks)) {
			$drawsAllowed = true;
		}
	}

	require_once(FILEROOT . '/header.php');
?>
<?php	if ($_GET['errors'] && $formErrors->errorsExist()) { ?>
		<div class="alertBox_error"><ul>
<?php
		if ($formErrors->checkError('overdrawn')) {
			echo "			<li>Incorrect number of cards drawn.</li>\n";
		}
		if ($formErrors->checkError('noTitle')) {
			echo "			<li>You can't leave the title blank.</li>\n";
		}
		if ($formErrors->checkError('noMessage')) {
			echo "			<li>You can't leave the message blank.</li>\n";
		}
		if ($formErrors->checkError('noDrawReason')) {
			echo "			<li>You left draw reasons blank.</li>\n";
		}
		if ($formErrors->checkError('noPoll')) {
			echo "			<li>You did not provide a poll question.</li>\n";
		}
		if ($formErrors->checkError('noOptions')) {
			echo "			<li>You did not provide poll options or provided too few (minimum 2).</li>\n";
		}
		if ($formErrors->checkError('noOptionsPerUser')) {
			echo "			<li>You did not provide a valid number for \"Options per user\".</li>\n";
		}
		if ($formErrors->checkError('badRoll')) {
			echo "			<li>One or more of your roll entries are malformed. Please make sure they are in the right format.</li>\n";
		}
?>
		</ul></div>
<?php
	}
	$threadManager->displayBreadcrumbs($pathOptions,$post,$quoteID);
?>
		<p id="rules" class="mob-hide">Be sure to read and follow the <a href="/forums/rules/">guidelines for our forums</a>.</p>
		<h1 class="headerbar"><i class="ra ra-quill-ink"></i> <?=($post->postID || $pathOptions[0] == 'post') ? ($editPost ? 'Edit post' : 'Post a reply') . ' - ' . printReady($threadManager->getThreadProperty('title')) : 'New Thread'?></h1>

<?php	if ($_GET['preview'] && strlen($fillVars['message']) > 0) { ?>
		<h2>Preview:</h2>
		<div id="preview">
			<?=printReady(BBCode2Html($fillVars['message']))."\n"?>
		</div>
		<hr>

<?php } ?>

		<div id="page_forum_thread">
		<div class="postBlock postRight postPreview postAsChar" style="display:none;">
			<div class="flexWrapper">
				<div class="posterDetails">
					<div class="avatar"><div><img src=""></div></div>
					<div class="postNames">
						<p class="charName"></p>
						<p class="posterName"><span>Preview</span></p>
					</div>
				</div>
				<div class="postBody">
					<div class="postContent">
						<div class="postPoint pointLeft"></div>
						<header class="postHeader"><div class="subject">Post Preview</div></header>
						<div class="post"></div>
					</div>
				</div>
			</div>
		</div>
		</div>

		<form method="post" action="/forums/process/post/">
<?php
	if ($pathOptions[0] == 'newThread') {
		echo "\t\t\t".'<input type="hidden" name="new" value="'.$forumID.'">'."\n";
	} elseif ($pathOptions[0] == 'editPost') {
		echo "\t\t\t".'<input type="hidden" name="edit" value="'.$postID.'">'."\n";
	} elseif ($pathOptions[0] == 'post') {
		echo "\t\t\t".'<input type="hidden" name="threadID" value="'.$threadID.'">'."\n";
	}

	if ($fillVars) {
		$title = printReady($fillVars['title']);
	} elseif (!strlen($post->getTitle()) && $threadManager->getThreadID()) {
		$title = $threadManager->getThreadProperty('title');
	} else {
		$title = printReady($post->title, ['stripslashes']);
	}
?>
			<div id="basicPostInfo" class="hbMargined">
				<div class="table">
					<div>
						<label for="title">Title:</label>
						<div><input id="title" type="text" name="title" maxlength="50" tabindex="<?=tabOrder()?>" value="<?=htmlentities($title)?>" class="titleInput"></div>
					</div>
<?php
	if ($gameID && (sizeof($characters)||sizeof($pcCharacters))) {
		$currentChar = $post->postAs;
		if ($fillVars) {
			$currentChar = $fillVars['postAs'];
		}
?>
					<div class="tr">
						<label>Post As:</label>
						<div><select name="postAs">
							<option value="p"<?=$currentChar == null ? ' selected="selected"' : ''?>>Player</option>
<?php		foreach ($characters as $characterID => $name) { ?>
							<option value="<?=$characterID?>"<?=$currentChar == $characterID ? ' selected="selected"' : ''?>><?=$name?></option>
<?php		}
			if(sizeof($pcCharacters)){
				foreach ($pcCharacters as $characterID => $name) { ?>
							<option value="<?=$characterID?>"<?=$currentChar == $characterID ? ' selected="selected"' : ''?>><?=$name?></option>
<?php			}} ?>
						</select> <span id="charSheetLink"></span></div>
					</div>
<?php	}?>
				</div>
				<textarea id="messageTextArea" name="message" tabindex="<?=tabOrder()?>"><?=$fillVars ? $fillVars['message'] : $post->message?></textarea>
				<?php if ($editPost) {?>
				<p><input type="checkbox" name="minorChange" checked="checked"> This is a minor edit</p>
				<?php }?>
			</div>
			<div id="submitDiv" class="alignRight">
				<button id="previewPost" class="fancyButton" accesskey="p" type="button">Preview</button>
				<button type="submit" name="post" tabindex="<?=tabOrder()?>" class="fancyButton submitButton"><?=$editPost?'Save':'Post'?></button>
            </div>

<?php	if ($firstPost || $rollsAllowed || $drawsAllowed) { ?>
			<div id="optionControls"><div class="trapezoid sectionControls flexWrapper">
<?php		if ($firstPost) { ?>
				<a href="" class="section_options<?=$firstPost ? ' current' : ''?>">Options</a>
				<a href="" class="section_poll">Poll</a>
<?php		} ?>
<?php		if ($rollsAllowed || $drawsAllowed) { ?>
				<a href="" class="section_rolls_decks<?=!$firstPost ? ' current' : ''?>">Rolls and Decks</a>
<?php		} ?>
			</div></div>
<?php	} ?>
<?php	if (($firstPost) || $rollsAllowed || $drawsAllowed) { ?>
			<h2 class="headerbar hbDark">
<?php		if ($firstPost) { ?>
				<span class="section_options">Thread Options</span>
				<span class="section_poll hideDiv">Poll</span>
<?php		} ?>
				<span class="section_rolls_decks<?=$firstPost ? ' hideDiv' : ''?>">Rolls and Decks</span>
			</h2>
<?php	} ?>

<?php	if ($firstPost) { ?>
			<div id="threadOptions" class="section_options hbdMargined">
<?php
		if ($threadManager->getPermissions('moderate')) {
			$sticky = $threadManager->getThreadProperty('states[sticky]');
			if ($fillVars) {
				$sticky = $fillVars['sticky'];
			}
?>
				<p><input type="checkbox" name="sticky"<?=$sticky ? ' checked="checked"' : ''?>> Sticky thread</p>
<?php
		}
		if ($threadManager->getPermissions('moderate')) {
			$locked = $threadManager->getThreadProperty('states[locked]');
			if ($fillVars) {
				$locked = $fillVars['locked'];
			}
?>
				<p><input type="checkbox" name="locked"<?=$locked ? ' checked="checked"' : ''?>> Lock thread</p>
<?php
		}
		if ($threadManager->getPermissions('addRolls')) {
			$addRolls = $rollsAllowed || ($pathOptions[0] == 'newThread' && $gameID);
			if ($fillVars) {
				$addRolls = $fillVars['allowRolls'];
			}
?>
				<p><input type="checkbox" name="allowRolls"<?=$addRolls ? ' checked="checked"' : ''?>> Allow adding rolls to posts (if this box is unchecked, any rolls added to this thread will be ignored)</p>
<?php
		}
		if ($threadManager->getPermissions('addDraws')) {
			$addDraws = $drawsAllowed || ($pathOptions[0] == 'newThread' && $gameID);
			if ($fillVars) {
				$addDraws = $fillVars['allowDraws'];
			}
?>
				<p><input type="checkbox" name="allowDraws"<?=$addDraws ? ' checked="checked"' : ''?>> Allow adding deck draws to posts (if this box is unchecked, any draws added to this thread will be ignored)</p>
<?php		} ?>
			</div>

			<div id="poll" class="section_poll hbdMargined hideDiv">
<?php		if ($pathOptions[0] == 'editPost') { ?>
				<div class="flexWrapper">
					<label for="allowRevoting"><b>Delete Poll:</b></label>
					<div><input id="deletePoll" type="checkbox" name="deletePoll"> If checked, your poll will be deleted and cannot be recovered.</div>
				</div>
<?php		} ?>
				<div class="tr flexWrapper">
					<label for="pollQuestion" class="textLabel"><b>Poll Question:</b></label>
					<div><input id="pollQuestion" type="text" name="poll" value="<?=$fillVars ? $fillVars['poll'] : $threadManager->getPollProperty('question')?>" class="borderBox"></div>
				</div>
				<div class="tr flexWrapper">
					<label for="pollOption" class="textLabel">
						<b>Poll Options:</b>
						<p>Place each option on a new line. You may enter up to <b>25</b> options.</p>
					</label>
					<div><textarea id="pollOptions" name="pollOptions"><?
			if ($fillVars) {
				echo $fillVars['pollOptions'];
			} else {
				$options = [];
				foreach ($threadManager->getPollProperty('options') as $option) {
					$options[] = $option->option;
				}
				echo implode("\n", $options);
			}
?></textarea></div>
				</div>
				<div class="tr flexWrapper">
					<label for="optionsPerUser" class="textLabel"><b>Options per user:</b></label>
					<div><input id="optionsPerUser" type="text" name="optionsPerUser" value="<?=$fillVars ? $fillVars['optionsPerUser'] : $threadManager->getPollProperty('optionsPerUser')?>" class="borderBox"></div>
				</div>
				<div class="tr flexWrapper">
					<label for="allowRevoting"><b>Allow Revoting:</b></label>
<?php
		$allowRevoting = $threadManager->getPollProperty('allowRevoting');
		if ($fillVars) {
			$allowRevoting = $fillVars['allowRevoting'];
		}
?>
					<div><input id="allowRevoting" type="checkbox" name="allowRevoting" <?=$allowRevoting ? ' checked="checked"' : ''?>> If checked, people will be allowed to change their votes.</div>
				</div>
			</div>
<?php
	}
	if ($rollsAllowed || $drawsAllowed) {
?>
			<div id="rolls_decks" class="section_rolls_decks hbdMargined<?=$firstPost ? ' hideDiv' : ''?>">
<?php		if ($rollsAllowed) { ?>
				<div id="rolls">
<?php			if ($drawsAllowed) { ?>
					<h3 id="rollsHeader">Rolls</h3>
<?php			} ?>
					<div id="rollExplination">
						For "Basic" type rolls, Enter the text roll in the following format:<br>
						(number of dice)d(dice type)+/-(modifier), i.e. 2d6+4, 1d10-2<br>
						The roll will automatically be added to your post when you submit it.
					</div>
<?php			if (sizeof($post->rolls)) { ?>
					<div id="postedRolls">
						<h3>Posted Rolls</h3>
<?php
				$visText = [1 => '[Hidden Roll/Result]', '[Hidden Dice &amp; Roll]', '[Everything Hidden]', '[Hidden Reason]'];
				$hidden = false;
				$showAll = false;
				$first = true;
				foreach ($post->rolls as $roll) {
					$showAll = $isGM || $currentUser->userID == $post->author->userID?true:false;
					$hidden = false;
?>
						<div class="rollInfo editRollInfo">
							<div class="editRollInfoRoll">
<?php
					$roll->showHTML($showAll);
?>
							</div>
							<div class="editRollInfoChangeVisibility">
							<select name="nVisibility[<?=$roll->getRollID()?>]" tabindex="<?=tabOrder()?>">
								<option value="0"<?=$roll->getVisibility() == 0 ? ' selected="selected"' : ''?>>Hide Nothing</option>
								<option value="1"<?=$roll->getVisibility() == 1 ? ' selected="selected"' : ''?>>Hide Roll/Result</option>
								<option value="2"<?=$roll->getVisibility() == 2 ? ' selected="selected"' : ''?>>Hide Dice &amp; Roll</option>
								<option value="3"<?=$roll->getVisibility() == 3 ? ' selected="selected"' : ''?>>Hide Everything</option>
								<option value="4"<?=$roll->getVisibility() == 4 ? ' selected="selected"' : ''?>>Hide Reason</option>
							</select>
							</div>
							<input type="hidden" name="oVisibility[<?=$roll->getRollID()?>]" value="<?=$roll->getVisibility()?>">

						</div>
<?php				} ?>
					</div>
<?php			} ?>
					<div id="addRoll">
						<span>Add new roll: </span>
						<select>
							<option value="basic">Basic</option>
							<option value="starwarsffg">Star Wars FFG</option>
							<option value="fate">Fate</option>
							<option value="fengshui">Feng Shui</option>
						</select>
						<button type="submit" class="fancyButton">Add</button>
					</div>
					<div id="newRolls">
<?php
			if (isset($fillVars['rolls'])) {
				foreach ($fillVars['rolls'] as $count => $roll) {
					rollTR($count, (object) $roll);
				}
			}
?>
					</div>
				</div>
<?php		} ?>
<?php		if ($drawsAllowed) { ?>
				<div id="draws">
<?php			if ($rollsAllowed) { ?>
					<h3 id="decksHeader">Decks</h3>
<?php			} ?>
					<p>Please remember, any cards you draw will be only visible to you until you reveal them. Reveal them by clicking them. An eye icon indicates they're visible, while an eye with a red slash through them indiates a hidden card.</p>
					<table id="decksTable">
<?php
			$firstDeck = true;
			foreach ($decks as $deck) {
				if ($draws[$deck['deckID']]) {
					$draw = $draws[$deck['deckID']];
?>
						<tr><td colspan="2">
							<b><?=$deck['label']?></b> has <?=(sizeof($deck['deck']) - $deck['position'] + 1)?> cards left.
							<p>Cards Drawn: <?=$draw['reason']?></p>
<?php
					$cardsDrawn = explode('~', $draw['cardsDrawn']);
					foreach ($cardsDrawn as $cardDrawn) {
						echo "\t\t\t\t\t\t" . getCardImg($cardDrawn, $deck['type'], 'mini') . "\n";
					}
?>
						</td></tr>
<?php				} else { ?>
						<tr class="deckTitle<?=$firstDeck ? '' : ' titleBuffer'?>"><td class="label"><b><?=$deck['label']?></b> has <?=sizeof($deck['deck']) - $deck['position'] + 1?> cards left</td></tr>
						<tr>
							<td class="reason"><input type="text" name="decks[<?=$deck['deckID']?>][reason]" maxlength="100" value="<?=$fillVars ? $fillVars[$deck['deckID']]['reason'] : ''?>" tabindex="<?=tabOrder()?>"></td>
							<td class="draw">Draw <input type="text" name="decks[<?=$deck['deckID']?>][draw]" maxlength="2" value="<?=$fillVars ? $fillVars[$deck['deckID']]['draw'] . '"' : ''?>" tabindex="<?=tabOrder()?>"> cards</td>
						</tr>
<?php
				}
				if ($firstDeck) $firstDeck = false;
			}
?>
					</table>
				</div>
<?php
		}
?>
			</div>
<?php
	}
?>
			<input type="hidden" name="postURL" value="<?=$_SESSION['currentURL']?>">

		</form>
<?php require_once(FILEROOT.'/footer.php'); ?>