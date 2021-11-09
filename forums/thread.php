<?php
	$responsivePage=true;
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	addPackage('forum');
	if($currentUser->addPostNavigateWarning()){
		$addJSFiles = Array('forums/unsaved-work.js','forums/postingPage.js','postPolls.js');
	}else{
		$addJSFiles = Array('forums/postingPage.js','postPolls.js');
	}

	$threadID = intval($pathOptions[1]);
	if (!$threadID) { header('Location: /forums'); exit; }

	$threadManager = new ThreadManager($threadID);
	if ($threadManager->getPermissions('read') == false) { header('Location: /403'); exit; }

	$gameID = false;
	$isGM = false;
	$gms = [];
	if ($threadManager->isGameForum()) {
		$gameID = (int) $threadManager->getForumProperty('gameID');
		$game = $mongo->games->findOne(['gameID' => $gameID], ['projection' => ['system' => true, 'players' => true]]);
		$system = $game['system'];
		$isGM = false;
		foreach ($game['players'] as $player) {
			if ($player['user']['userID'] == $currentUser->userID) {
				if ($player['isGM']) {
					$isGM = true;
				}
			}
			if ($player['isGM']) {
				$gms[] = $player['user']['userID'];
			}
		}
	} else {
		$fixedGameMenu = false;
	}

	$threadManager->setPage();
	$threadManager->getPosts();

	$dispatchInfo['title'] = $threadManager->getThreadProperty('title') . ' | ' . $dispatchInfo['title'];
	$dispatchInfo['description'] = $threadManager->getKeyPost()->message;
?>
<?php	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"> <?$threadManager->addThreadIcon()?><?=$threadManager->getThreadProperty('title')?></h1>
		<div class="hbMargined">
			<div id="threadMenu">
				<div class="leftCol">
					<?=$threadManager->displayBreadcrumbs($pathOptions,$post,$quoteID);?>
					<p id="rules" class="mob-hide">Be sure to read and follow the <a href="/forums/rules/">guidelines for our forums</a>.</p>
				</div>
				<div class="rightCol alignRight">
<?php
	if ($loggedIn) {
		$forumSubbed = $mysql->query("SELECT userID FROM forumSubs WHERE userID = {$currentUser->userID} AND type = 'f' AND ID = {$threadManager->getThreadProperty('forumID')}");
		if (!$forumSubbed->rowCount()) {
			$isSubbed = $mysql->query("SELECT userID FROM forumSubs WHERE userID = {$currentUser->userID} AND type = 't' AND ID = {$threadID}");
?>
					<p class="threadSub"><a id="forumSub" href="/forums/process/subscribe/?threadID=<?=$threadID?>"><?=$isSubbed->rowCount() ? 'Unsubscribe from' : 'Subscribe to'?> thread</a></p>
<?php
		}
	}
	$threadManager->addModerationButtons();
	if ($threadManager->getPermissions('write')) {
			$threadManager->displayPagination();
} ?>
				</div>
			</div>

			<?php $threadManager->enrichThread(); ?>
<?php
	if (!$threadManager->getThreadProperty('states[locked]') && $threadManager->getPoll()) {
?>
			<form id="poll" method="post" action="/forums/process/vote/">
				<input type="hidden" name="threadID" value="<?=$threadID?>">
				<p id="poll_question"><?=printReady($threadManager->getPollProperty('question'))?></p>
<?php
		$castVotes = $threadManager->getVotesCast();
		$allowVote = sizeof($castVotes) && $threadManager->getPollProperty('allowRevoting') || sizeof($castVotes) == 0;
		if ($allowVote) {
			echo "				<p>You may select " . ($threadManager->getPollProperty('optionsPerUser') > 1 ? 'up to ' : '') . "<b>" . $threadManager->getPollProperty('optionsPerUser') . "</b> option" . ($threadManager->getPollProperty('optionsPerUser') > 1 ? 's' : '') . ".</p>\n";
		}

		$totalVotes = $threadManager->getVoteTotal();
		$highestVotes = $threadManager->getVoteMax();
?>
				<ul>
<?php
		foreach ($threadManager->getPollProperty('options') as $pollOptionID => $option) {
			if ($allowVote) {
				if ($threadManager->getPollProperty('optionsPerUser') == 1) {
					echo "						<div class=\"poll_input\"><input type=\"radio\" name=\"votes\" value=\"{$pollOptionID}\"" . ($option->voted ? ' checked="checked"' : '') . "></div>\n";
				} else {
					echo "						<div class=\"poll_input\"><input type=\"checkbox\" name=\"votes\" value=\"{$pollOptionID}\"" . ($option->voted ? ' checked="checked"' : '') . "></div>\n";
				}
			}
			echo "						<div class=\"poll_option\">" . printReady($option->option) . "</div>\n";
			if (sizeof($castVotes)) {
				echo "						<div class=\"poll_votesCast\" " . ($option->votes ? ' style="width: ' . (100 + floor($option->votes / $highestVotes * 425)) . 'px"' : '') . ">" . $option->votes . ", " . floor($option->votes / $totalVotes * 100) . "%</div>\n";
			}
			echo "					</li>\n";
		}
?>
				</ul>
<?php		if ($allowVote) { ?>
				<div id="poll_submit"><button type="submit" name="submit" class="fancyButton">Vote</button></div>
<?php		} ?>
			</form>
<?php
	}

	$postCount = 1;
	$forumOptions = ['showAvatars' => 1, 'postSide'=> 'r'];
	if ($loggedIn) {
		$forumOptions['postSide'] = $currentUser->postSide;
	}
	if ($forumOptions['postSide'] == 'r') {
		$postSide = 'Right';
	} else {
		$postSide = 'Left';
	}

	$characters = [];
	$newPostMarked = false;
	if ($threadManager->getFirstPostID() > $threadManager->getThreadLastRead()) {
		$hitLastRead = true;
	}
	$lastPostID = 0;
	if($threadManager->getPage() && $threadManager->getPage()>1){
		?>
		<div id="backfill"><span>load previous</span></div>
		<?php
	}
	if (sizeof($threadManager->getPosts())) {
		foreach ($threadManager->getPosts() as $post) {
			$lastPostID = $post->getPostID();
			if ($post->getPostAs()) {
				$charSystem = getCharacterClass($post->getPostAs());
				require_once(FILEROOT . "/includes/packages/{$charSystem}Character.package.php");
				$charClass = Systems::systemClassName($charSystem) . 'Character';
				if (isset($characters[$post->getPostAs()]) || $characters[$post->getPostAs()] = new $charClass($post->getPostAs())) {
					$postAsChar = true;
					$character = $characters[$post->getPostAs()];
				} else {
					$postAsChar = false;
					$npc = $post->getNpc();
				}
			} else {
				$postAsChar = false;
				$npc = $post->getNpc();
			}
?>
			<div class="postBlock post<?=$postSide?><?=$postAsChar ? ' postAsChar' . ($character->getAvatar() ? ' withCharAvatar' : '') : ($npc ? ' withSingleNpc postAsChar withCharAvatar':'')?>">
				<a name="p<?=$post->getPostID()?>"></a>
<?php
			if (!$newPostMarked && ($post->getPostID() > $threadManager->getThreadLastRead() || $threadManager->thread->getLastPost('postID') == $post->getPostID())) {
				$newPostMarked = true;
?>
				<a name="newPost"></a>
<?php
			}
			$isLastPost=false;
			if ($threadManager->thread->getLastPost('postID') == $post->getPostID()) {
				$isLastPost=true;
?>
				<a name="lastPost"></a>
<?php			} ?>
				<div class="flexWrapper">
					<div class="posterDetails">
						<div class="avatar"><div>
<?php
			if ($postAsChar) {
				$character->load();
			}

			$postedCharAvatar = false;
			if ($postAsChar && $character->getAvatar()) {
				$postedCharAvatar = true;
				if ($character->checkPermissions()) {
?>
							<a href="/characters/<?=$character::SYSTEM?>/<?=$character->getID()?>/"><img src="<?=$character->getAvatar()?>"></a>
<?php				} else { ?>
							<img src="<?=$character->getAvatar()?>">
<?php
				}
				$userAvatarSize = getimagesize(FILEROOT.User::getAvatar($post->author->userID, $post->author->avatarExt));
				$xRatio = 40 / $userAvatarSize[0];
				$yRatio = 40 / $userAvatarSize[1];

				if ($userAvatarSize[0] <= 40 && $userAvatarSize[1] <= 40) {
					$finalWidth = $userAvatarSize[0];
					$finalHeight = $userAvatarSize[1];
				} elseif (($xRatio * $userAvatarSize[1]) < 40) {
					$finalWidth = 40;
					$finalHeight = ceil($xRatio * $userAvatarSize[1]);
				} else {
					$finalWidth = ceil($yRatio * $userAvatarSize[0]);
					$finalHeight = 40;
				}
			}
			else if($npc && $npc["avatar"]){
				$postedCharAvatar = true;
				$finalHeight=40;
				$finalWidth=40;
				?><img src="<?=$npc['avatar']?>"><?php
			}

?>
							<a href="/user/<?=$post->author->userID?>/" class="userAvatar"<?=$postedCharAvatar ? ' style="top: -' . ($finalHeight / 2) . 'px; right: -' . ($finalWidth / 2) . 'px;"' : ''?>><img src="<?=User::getAvatar($post->author->userID, $post->author->avatarExt)?>"></a>
						</div></div>
						<div class="postNames">
<?php
			if ($postAsChar) {
				$character->getForumTop($post->author, in_array($post->author->userID, $gms));
			} else {
				if($npc){
					?> <p class="charName"><?=$npc["name"]?></p> <?php
				}
?>
						<p class="posterName"><a href="/user/<?=$post->author->userID?>/" class="username"><?=$post->author->username?></a><?=in_array($post->author->userID, $gms)?' <img src="/images/gm_icon.png">':''?><?=User::inactive($post->author->lastActivity)?></p>
<?php			} ?>
						</div>
					</div>
					<div class="postBody">
						<div class="postContent">
							<div class="postPoint point<?=$postSide == 'Right' ? 'Left' : 'Right'?>"></div>
							<header class="postHeader">
								<div class="postedOn convertTZ"><?=date('M j, Y g:i a', strtotime($post->datePosted))?></div>
								<div class="subject"><a href="?p=<?=$post->postID?>#p<?=$post->postID?>"><?=strlen($post->title) ? printReady($post->title) : '&nbsp'?></a></div>
							</header>
<?php
			echo "\t\t\t\t\t\t\t<div class=\"post\">\n";
			echo printReady(BBCode2Html($post->message)) . "\n";
			if ($post->timesEdited) { echo "\t\t\t\t\t\t\t\t" . '<div class="editInfoDiv">Last edited <span  class="convertTZ">' . date('F j, Y g:i a', strtotime($post->lastEdit)) . "</span></div>\n"; }
			echo "\t\t\t\t\t\t\t</div>\n";

			if (sizeof($post->rolls)) {
?>
							<div class="rolls">
								<h4>Rolls</h4>
<?php
				foreach ($post->rolls as $roll) {
					$showAll = $isGM || $currentUser->userID == $post->author->userID ? true : false;
?>
								<div class="rollInfo">
<?php					$roll->showHTML($showAll); ?>
								</div>
<?php				} ?>
							</div>
<?php
	 		}

			if (sizeof($post->draws)) {
				$visText = [1 => '[Hidden Roll/Result]', '[Hidden Dice &amp; Roll]', '[Everything Hidden]'];
				$hidden = false;
?>
							<h4>Deck Draws</h4>
<?php
				foreach ($post->draws as $draw) {
					echo "\t\t\t\t\t\t\t<div>" . printReady($draw['reason']) . "</div>\n";
					if ($post->author->userID == $currentUser->userID) {
						echo "\t\t\t\t\t\t\t<form method=\"post\" action=\"/forums/process/cardVis/\">\n";
						echo "\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"drawID\" value=\"{$draw['drawID']}\">\n";
						$cardsDrawn = explode('~', $draw['cardsDrawn']);
						$count = 0;
						foreach ($cardsDrawn as $cardDrawn) {
							echo "\t\t\t\t\t\t\t\t<button type=\"submit\" name=\"position\" value=\"$count\">\n";
							echo "\t\t\t\t\t\t\t\t\t" . getCardImg($cardDrawn, $draw['type'], 'mid') . "\n";
							$visText = $draw['reveals'][$count++] ? 'Visible' : 'Hidden';
							echo "\t\t\t\t\t\t\t\t\t<div alt=\"{$visText}\" title=\"{$visText}\" class=\"eyeIcon" . ($visText == 'Hidden' ? ' hidden' : '')."\"></div>\n";
							echo "\t\t\t\t\t\t\t\t</button>\n";
						}
						echo "\t\t\t\t\t\t\t</form>\n";
					} else {
						echo "\t\t\t\t\t\t\t<div>\n";
						$cardsDrawn = explode('~', $draw['cardsDrawn']);
						$count = 0;
						foreach ($cardsDrawn as $cardDrawn) {
							if ($draw['reveals'][$count++] == 1) {
								echo "\t\t\t\t\t\t\t\t" . getCardImg($cardDrawn, $draw['type'], 'mid') . "\n";
							} else {
								echo "\t\t\t\t\t\t\t\t<img src=\"/images/tools/cards/back.png\" alt=\"Hidden Card\" title=\"Hidden Card\" class=\"cardBack mid\">\n";
							}
						}
						echo "\t\t\t\t\t\t\t</div>\n";
					}
				}
			}
?>
						</div>
						<div class="postActions">
<?php
			if($isLastPost){
				echo "<a class=\"keepUnread\" title=\"Mark as unread\" data-threadid='{$threadID}'>Mark as unread</a>\n";
			}
			if ($threadManager->getPermissions('write')) echo "\t\t\t\t\t\t\t<span class='quotePost' data-postid='{$post->postID}'\">Quote</span>\n";
			if (($post->author->userID == $currentUser->userID && !$threadManager->getThreadProperty('states[locked]')) || $threadManager->getPermissions('moderate')) {
				if ($threadManager->getPermissions('moderate') || $threadManager->getPermissions('editPost')) {
					echo "\t\t\t\t\t\t\t<a href=\"/forums/editPost/{$post->postID}/\">Edit</a>\n";
				}
				if (
					$threadManager->getPermissions('moderate') ||
					$threadManager->getPermissions('deletePost') &&
					$post->postID != $threadManager->getThreadProperty('firstPostID') ||
					$threadManager->getPermissions('deleteThread') &&
					$post->postID == $threadManager->getThreadProperty('firstPostID')
				) {
					echo "\t\t\t\t\t\t\t<a href=\"/forums/delete/{$post->postID}/\" class=\"deletePost\">Delete</a>\n";
				}
			}
?>
						</div>
					</div>
				</div>
			</div>
<?php
			$postCount += 1;
			if ($forumOptions['postSide'] == 'c') {
				$postSide = $postSide == 'Right'?'Left':'Right';
			}
		}
?>
			<div class="postBlock post<?=$postSide?> postPreview" style="display:none;">
				<div class="flexWrapper">
					<div class="posterDetails">Preview</div>
					<div class="postBody">
						<div class="postContent">
							<div class="postPoint point<?=$postSide == 'Right' ? 'Left' : 'Right'?>"></div>
							<div class="post"></div>
						</div>
					</div>
				</div>
			</div>
<?php
		$threadManager->displayPagination();
	}

?>
	<div class="rightCol alignRight">
	<?php
	$threadManager->addModerationButtons();
	?>
	</div>
		</div>

<?php
	if (($threadManager->getPermissions('write') && $currentUser->userID != 0 && !$threadManager->getThreadProperty('states[locked]')) || $threadManager->getPermissions('moderate')) {
		$characters = [];
		if ($gameID) {
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
		}

		$rollsAllowed = $threadManager->getThreadProperty('allowRolls') ? true : false;
?>

<?php /*  ------ START REPLY ----- */ ?>

		<form id="quickReply" method="post" action="/forums/process/post/">
			<h2 class="headerbar hbDark"><i class="ra ra-quill-ink"></i> Quick Reply</h2>
			<input type="hidden" name="threadID" value="<?=$threadID?>">
			<input type="hidden" name="title" value="<?=htmlspecialchars($threadManager->getThreadProperty('title'))?>">
			<div class="">
<?php		if (sizeof($characters)) { ?>
				<div id="charSelect" class="tr">
					<label>Post As:</label>
					<div><select name="postAs">
						<option value="p"<?=$currentChar == null ? ' selected="selected"' : ''?>>Player</option>
<?php			foreach ($characters as $characterID => $name) { ?>
						<option value="<?=$characterID?>"<?=$currentChar == $characterID ? ' selected="selected"' : ''?>><?=$name?></option>
<?php			} ?>
					</select> <span id="charSheetLink"></span></div>
				</div>
<?php		} ?>
				<textarea id="messageTextArea" name="message"></textarea>
				<div class="alignRight"><span id="previewPost" class="fancyButton" accesskey="p">Preview</span></div>
			</div>

<?php /*  ------ START REPLY ROLLS ----- */ ?>
			<?php if ($rollsAllowed) { ?>
				<h3 class="headerbar"><i class="ra ra-perspective-dice-random"></i> Rolls</h2>
				<div id="rolls_decks">
					<div id="rolls">
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
				</div>
<?php		} ?>
<?php /*  ------ END OF REPLY ROLLS ----- */ ?>


			<div id="submitDiv" class="alignCenter">
				<button type="submit" name="post" class="fancyButton">Post</button>
				<button type="submit" name="advanced" class="fancyButton">Advanced</button>
			</div>
		</form>


<?php /*  ------ END OF REPLY ----- */ ?>

<?php
	} elseif ($threadManager->getThreadProperty('states[locked]')) {
		echo "\t\t\t<h2 class=\"alignCenter\">Thread locked</h2>\n";
	} else {
		echo "\t\t\t<h2 class=\"alignCenter\">You do not have permission to post in this thread.</h2>\n";
	}

	$threadManager->updateLastRead($lastPostID);

	require_once(FILEROOT . '/footer.php');
?>