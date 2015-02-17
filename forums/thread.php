<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	addPackage('forum');
	
	$threadID = intval($pathOptions[1]);
	if (!$threadID) { header('Location: /forums'); exit; }
	
	$threadManager = new ThreadManager($threadID);
	if ($threadManager->getPermissions('read') == false) { header('Location: /403'); exit; }

	if (isset($_GET['view']) && $_GET['view'] == 'newPost') {
		$numPrevPosts = $mysql->query("SELECT COUNT(postID) numPosts FROM posts WHERE threadID = {$threadID} AND postID <= ".$threadManager->getThreadLastRead());
		$numPrevPosts = $numPrevPosts->fetchColumn() + 1;
		$page = $numPrevPosts?ceil($numPrevPosts / PAGINATE_PER_PAGE):1;
	} elseif (isset($_GET['post'])) {
		$post = intval($_GET['post']);
		$numPrevPosts = $mysql->query('SELECT COUNT(postID) FROM posts WHERE threadID = '.$threadID.' AND postID <= '.$post);
		$numPrevPosts = $numPrevPosts->fetchColumn();
		$page = $numPrevPosts?ceil($numPrevPosts / PAGINATE_PER_PAGE):1;
	} else $page = intval($_GET['page']);
	$threadManager->getPosts($page);

	$gameID = false;
	$isGM = false;
	if ($threadManager->getForumProperty('gameID')) {
		$gameID = $threadManager->getForumProperty('gameID');
		$systemID = $mysql->query("SELECT systemID FROM games WHERE gameID = {$gameID}");
		$systemID = $systemID->fetchColumn();

		$gmCheck = $mysql->query("SELECT isGM FROM players WHERE userID = {$currentUser->userID} AND gameID = ".$threadManager->getForumProperty('gameID'));
		if ($gmCheck->rowCount()) $isGM = true;

		$system = $systems->getShortName($systemID);
		require_once(FILEROOT."/includes/packages/{$system}Character.package.php");
		$charClass = $system.'Character';
	} else 
		$fixedGameMenu = false;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=$threadManager->getThreadProperty('title')?></h1>
		<div class="hbMargined">
			<div id="threadMenu" class="clearfix">
				<div class="leftCol">
					<a href="/forums/<?=$threadManager->getThreadProperty('forumID')?>/">Back to the forums</a>
				</div>
				<div class="rightCol alignRight">
<? if ($threadManager->getPermissions('moderate')) { ?>
					<form id="threadOptions" method="post" action="/forums/process/modThread/">
<?
	$sticky = $threadManager->thread->getStates('sticky')?'unsticky':'sticky';
	$lock = $threadManager->thread->getStates('locked')?'unlock':'lock';
?>
						<input type="hidden" name="threadID" value="<?=$threadID?>">
						<button type="submit" name="sticky" title="<?=ucwords($sticky)?> Thread" alt="<?=ucwords($sticky)?> Thread" class="<?=$sticky?>"></button>
						<button type="submit" name="lock" title="<?=ucwords($lock)?> Thread" alt="<?=ucwords($lock)?> Thread" class="<?=$lock?>"></button>
					</form>
<? } ?>
<?	if ($threadManager->getPermissions('write')) { ?>
					<a href="/forums/post/<?=$threadID?>/" class="fancyButton">Reply</a>
<?	} ?>
				</div>
			</div>
<?
	if ($threadManager->getPoll()) {
?>
			<form id="poll" method="post" action="/forums/process/vote/">
				<input type="hidden" name="threadID" value="<?=$threadID?>">
				<p id="poll_question"><?=printReady($threadManager->getPollProperty('question'))?></p>
<? 
		$castVotes = $threadManager->getVotesCast();
		$allowVote = sizeof($castVotes) && $threadManager->getPollProperty('allowRevoting') || sizeof($castVotes) == 0;
		if ($allowVote) echo "				<p>You may select ".($threadManager->getPollProperty('optionsPerUser') > 1?'up to ':'')."<b>".$threadManager->getPollProperty('optionsPerUser')."</b> option".($threadManager->getPollProperty('optionsPerUser') > 1?'s':'').".</p>\n";

		$totalVotes = $threadManager->getVoteTotal();
		$highestVotes = $threadManager->getVoteMax();
?>
				<ul>
<?
		foreach ($threadManager->getPollProperty('options') as $pollOptionID => $option) {
			echo "					<li class=\"clearfix\">\n";
			if ($allowVote) {
				if ($threadManager->getPollProperty('optionsPerUser') == 1) echo "						<div class=\"poll_input\"><input type=\"radio\" name=\"votes\" value=\"{$pollOptionID}\"".($option->voted?' checked="checked"':'')."></div>\n";
				else echo "						<div class=\"poll_input\"><input type=\"checkbox\" name=\"votes\" value=\"{$pollOptionID}\"".($option->voted?' checked="checked"':'')."></div>\n";
			}
			echo "						<div class=\"poll_option\">".printReady($option->option)."</div>\n";
			if (sizeof($castVotes)) {
				echo "						<div class=\"poll_votesCast\" ".($option->votes?' style="width: '.(100 + floor($option->votes / $highestVotes * 425)).'px"':'').">".$option->votes.", ".floor($option->votes / $totalVotes * 100)."%</div>\n";
			}
			echo "					</li>\n";
		}
?>
				</ul>
<?		if ($allowVote) { ?>
				<div id="poll_submit"><button type="submit" name="submit" class="fancyButton">Vote</button></div>
<?		} ?>
			</form>
<?
	}
	
	$postCount = 1;
	$forumOptions = array('showAvatars' => 1, 'postSide'=> 'r');
	if ($loggedIn) $forumOptions['postSide'] = $currentUser->postSide;
	if ($forumOptions['postSide'] == 'r') $postSide = 'Right';
	else $postSide = 'Left';
	
	$characters = array();
	if (sizeof($threadManager->getPosts())) {
		foreach ($threadManager->getPosts() as $post) {
			if ($post->postAs) {
				if (isset($characters[$post->postAs]) || $characters[$post->postAs] = new $charClass($post->postAs)) {
					$postAsChar = true;
					$character = $characters[$post->postAs];
				} else $postAsChar = false;
			} else $postAsChar = false;
?>
			<div class="postBlock post<?=$postSide?><?=$postAsChar && $character->getAvatar()?' postAsChar':''?> clearfix">
				<a name="p<?=$post->postID?>"></a>
				<div class="posterDetails">
					<div class="avatar"><div>
<?			if ($postAsChar && $character->getAvatar()) { ?>
						<a href="/character/<?=$character::SYSTEM?>/<?=$character->getCharacterID()?>/"><img src="<?=$character->getAvatar()?>"></a>
<?			} ?>
						<a href="/user/<?=$post->author->userID?>/" class="userAvatar"><img src="<?=User::getAvatar($post->author->userID, $post->author->avatarExt)?>"></a>
					</div></div>
<?
			if ($postAsChar) {
				$character->load();
				$character->getForumTop($post->author);
			} else {
?>
					<p class="posterName"><a href="/user/<?=$post->userID?>/" class="username"><?=$post->author->username?></a></p>
<?			} ?>
				</div>
				<div class="postContent">
					<div class="postPoint point<?=$postSide == 'Right'?'Left':'Right'?>"></div>
					<header class="postHeader">
						<div class="postedOn convertTZ"><?=date('M j, Y g:i a', strtotime($post->datePosted))?></div>
						<div class="subject"><a href="?p=<?=$post->postID?>"><?=strlen($post->title)?printReady($post->title):'&nbsp'?></a></div>
					</header>
<?
			echo "\t\t\t\t\t<div class=\"post\">\n";
			echo printReady(BBCode2Html($post->message))."\n";
			if ($post->timesEdited) { echo "\t\t\t\t\t\t".'<div class="editInfoDiv">Last edited <span  class="convertTZ">'.date('F j, Y g:i a', strtotime($post->lastEdit)).'</span>, a total of '.$post->timesEdited.' time'.(($post->timesEdited > 1)?'s':'')."</div>\n"; }
			echo "\t\t\t\t\t</div>\n";
			
			if (sizeof($post->rolls)) {
?>
					<div class="rolls">
						<h4>Rolls</h4>
<?
				foreach ($post->rolls as $roll) {
					$showAll = $isGM || $currentUser->userID == $post->author->userID?true:false;
?>
						<div class="rollInfo">
<?					$roll->showHTML($showAll); ?>
						</div>
<?
				}
?>
					</div>
<?
	 		}
			
			if (sizeof($draws[$post->postID])) {
				$visText = array(1 => '[Hidden Roll/Result]', '[Hidden Dice &amp; Roll]', '[Everything Hidden]');
				$hidden = false;
?>
					<h4>Deck Draws</h4>
<?
				foreach ($post->draws as $draw) {
					echo "\t\t\t\t\t<div>".printReady($draw['reason'])."</div>\n";
					if ($post->author->userID == $currentUser->userID) {
						echo "\t\t\t\t\t<form method=\"post\" action=\"/forums/process/cardVis/\">\n";
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
			if ($threadManager->getPermissions('write')) echo "						<a href=\"/forums/post/{$threadID}/?quote={$post->postID}\">Quote</a>\n";
			if (($post->userID == $currentUser->userID && !$threadManager->getThreadProperty('locked')) || $threadManager->getPermissions('moderate')) {
				if ($threadManager->getPermissions('moderate') || $threadManager->getPermissions('editPost')) echo "					<a href=\"/forums/editPost/{$post->postID}/\">Edit</a>\n";
				if ($threadManager->getPermissions('moderate') || $threadManager->getPermissions('deletePost') && $post->postID != $threadManager->getThreadProperty('firstPostID') || $threadManager->getPermissions('deleteThread') && $post->postID == $threadManager->getThreadProperty('firstPostID')) echo "					<a href=\"/forums/delete/{$post->postID}/\" class=\"deletePost\">Delete</a>\n";
			}
?>
				</div>
			</div>
<?
			$postCount += 1;
			if ($forumOptions['postSide'] == 'c') $postSide = $postSide == 'Right'?'Left':'Right';
		}

		$threadManager->displayPagination($page);
	}
	
	if ($threadManager->getPermissions('moderate')) {
?>
			<div class="clearfix"><form id="quickMod" method="post" action="/forums/process/modThread/">
<?
	$sticky = $threadManager->thread->getStates('sticky')?'Unsticky':'Sticky';
	$lock = $threadManager->thread->getStates('locked')?'Unlock':'lock';
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

<?	if ($threadManager->getPermissions('write') && $currentUser->userID != 0 && !$threadManager->getThreadProperty('locked')) { ?>
		<form id="quickReply" method="post" action="/forums/process/post/">
			<h2 class="headerbar hbDark">Quick Reply</h2>
			<input type="hidden" name="threadID" value="<?=$threadID?>">
			<input type="hidden" name="title" value="Re: <?=$threadManager->getThreadProperty('title')?>">
			<div class="hbdMargined"><textarea id="messageTextArea" name="message"></textarea></div>
			
			<div id="submitDiv" class="alignCenter">
				<button type="submit" name="post" class="fancyButton">Post</button>
				<button type="submit" name="advanced" class="fancyButton">Advanced</button>
			</div>
		</form>
<?
	} elseif ($threadManager->getThreadProperty('locked')) echo "\t\t\t<h2 class=\"alignCenter\">Thread locked</h2>\n";
	else echo "\t\t\t<h2 class=\"alignCenter\">You do not have permission to post in this thread.</h2>\n";
	
	require_once(FILEROOT.'/footer.php');
?>