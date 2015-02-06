<?
	addPackage('forum');

	if (isset($_POST['advanced'])) {
		$_SESSION['message'] = $_POST['message'];
		header('Location: /forums/post/'.intval($_POST['threadID']).'/');
	} elseif (isset($_POST['preview'])) {
		$_SESSION['previewVars'] = $_POST;
		header("Location: {$_SESSION['lastURL']}?preview=1");
	} elseif (isset($_POST['post'])) {
		unset ($_SESSION['errors'], $_SESSION['errorVals'], $_SESSION['errorTime']);


		$post = new Post();
		$post->setTitle($_POST['title']);
		$post->setPostAs($_POST['postAs']);
		$message = $_POST['message'];

		if (preg_match_all('/\[note="?(\w[\w +;,]+?)"?](.*?)\[\/note\]/ms', $message, $matches, PREG_SET_ORDER)) {
			$allUsers = array();
			foreach ($matches as $match) {
				foreach (preg_split('/[^\w]+/', $match[1]) as $eachUser) $allUsers[] = $eachUser;
			}
			$userCheck = $mysql->prepare('SELECT username FROM users WHERE LOWER(username) = :username');
			foreach ($allUsers as $key => $username) {
				$userCheck->bindValue(':username', strtolower($username));
				$userCheck->execute();
				if (!$userCheck->rowCount()) unset($allUsers[$key]);
				else $allUsers[$key] = $userCheck->fetchColumn();
			}
			foreach ($matches as $match) {
				$matchUsers = preg_split('/[^\w]+/', $match[1]);
				$validUsers = array_intersect($matchUsers, $allUsers);
				if (sizeof($matchUsers) != $validUsers) {
					$validNote = preg_replace('/\[note.*?\]/', '[note="'.implode(',', $validUsers).'"]', $match[0]);
					$message = str_replace($match[0], $validNote, $message);
				}
			}
		}
		$post->setMessage($_POST['message']);
		
		$rolls = array();
		$draws = array();

		if (sizeof($_POST['rolls'])) { foreach ($_POST['rolls'] as $num => $roll) {
			$cleanedRoll = array();
			if (strlen($roll['roll'])) {
				$rollObj = RollFactory::getRoll($roll['type']);
				if (!isset($roll['options'])) $roll['options'] = array();
				$rollObj->newRoll($roll['roll'], $roll['options']);
				$rollObj->roll();
				$rollObj->setReason($roll['reason']);
				$rollObj->setVisibility($roll['visibility']);
				$post->addRollObj($rollObj);
			}
		} }

		if (sizeof($_POST['decks'])) {
			$draws = array_filter($_POST['decks'], function($value) { return intval($value) > 0?true:false; });
			$deckInfos = $mysql->query('SELECT decks.deckID, decks.deck, decks.type, decks.position FROM decks INNER JOIN deckPermissions ON decks.deckID = deckPermissions.deckID WHERE deckPermissions.userID = '.$currentUser->userID.' AND decks.deckID IN ('.implode(',', array_keys($draws)).')');
			$temp = array();
			foreach ($deckInfos as $deckInfo) $temp[$deckInfo['deckID']] = array('deck' => $deckInfo['deck'], 'type' => $deckInfo['type'], 'position' => $deckInfo['position']);
			$deckInfos = $temp;
			foreach ($draws as $deckID => $draw) {
				if (isset($deckInfos[$deckID]) && $draw['draw'] > 0) {
					$deck = explode('~', $deckInfos[$deckID]['deck']);
					if (strlen($draw['reason']) == 0) {
						$_SESSION['errors']['noDrawReason'] = 1;
						break;
					} elseif ($deckInfos[$deckID]['position'] + $draw['draw'] - 1 > sizeof($deck)) {
						$_SESSION['errors']['overdrawn'] = 1;
						break;
					}
					
					$draw['cardsDrawn'] = array();
					for ($count = $deckInfos[$deckID]['position']; $count <= $deckInfos[$deckID]['position'] + $draw['draw'] - 1; $count++) $draw['cardsDrawn'][] = $deck[$count - 1];
					$draw['cardsDrawn'] = implode('~', $draw['cardsDrawn']);
					$draw['reason'] = sanitizeString($draw['reason']);
					$draw['type'] = $deckInfos[$deckID]['type'];
					$post->addRaw($draw);
				}
			}
		}

		$postID = 0;
		$threadID = 0;
		$noChat = false;
		$permissions = array();

		$formErrors->clearErrors();
		if ($_POST['new']) {
			$forumID = intval($_POST['new']);
			$threadManager = new ThreadManager(null, $forumID);

			if (!$threadManager->getPermissions('createThread')) { header('Location: /forums/'.$forumID); exit; }
			$threadManager->thread->setState('sticky', isset($_POST['sticky']) && $threadManager->getPermissions('moderate')?true:false);
			$threadManager->thread->setState('locked', isset($_POST['locked']) && $threadManager->getPermissions('moderate')?true:false);
			$threadManager->thread->setAllowRolls(isset($_POST['allowRolls']) && $threadManager->getPermissions('addRolls')?true:false);
			$threadManager->thread->setAllowDraws(isset($_POST['allowDraws']) && $threadManager->getPermissions('addRolls')?true:false);
			
			if (strlen($post->getTitle()) == 0) $formErrors->addError('noTitle');
			if (strlen($post->getMessage()) == 0) $formErrors->addError('noMessage');
			
			$threadManager->thread->poll->setQuestion($_POST['poll']);
			$threadManager->thread->poll->parseOptions($_POST['pollOptions']);
			if (strlen($threadManager->thread->poll->getQuestion()) == 0 && sizeof($threadManager->thread->poll->getOptions()) != 0) 
				$formErrors->addError('noQuestion');
			if (strlen($threadManager->thread->poll->getQuestion()) && sizeof($threadManager->thread->poll->getOptions()) <= 1) 
				$formErrors->addError('noOptions');
			$threadManager->thread->poll->setOptionsPerUser($_POST['optionsPerUser']);
			if ($threadManager->thread->poll->getOptionsPerUser() == 0) 
				$formErrors->addError('noOptionsPerUser');
			$threadManager->thread->poll->setAllowRevoting($_POST['allowRevoting']);

			if ($formErrors->errorsExist()) {
				$formErrors->setErrors('post', $_POST);
				header('Location: '.$_SESSION['lastURL'].'?errors=1');
				exit;
			} else 
				$postID = $threadManager->createThread($post);
		} elseif ($_POST['threadID']) {
			$threadID = intval($_POST['threadID']);
			$threadInfo = $mysql->query('SELECT forumID, locked, allowRolls, allowDraws FROM threads WHERE threadID = '.$threadID);
			list($forumID, $locked, $allowRolls, $allowDraws) = $threadInfo->fetch(PDO::FETCH_NUM);
			$permissions = retrievePermissions($currentUser->userID, $forumID, 'write, addRolls, addDraws, moderate', true);
			if (!$threadManager->getPermission('write') || $locked) { header('Location: /forums/'.$forumID); exit; }
			
			if (strlen($title) == 0) {
				$title = $mysql->query("SELECT p.title FROM posts p INNER JOIN threads_relPosts rp ON p.postID = rp.firstPostID WHERE rp.threadID = {$threadID} LIMIT 1");
				$title = 'Re: '.$title->fetchColumn();
			}
			if (strlen($message) == 0) $_SESSION['errors']['noMessage'] = 1;
			
			if (sizeof($_SESSION['errors'])) {
				$_SESSION['errorVals'] = $_POST;
				$_SESSION['errorTime'] = time() + 300;
				header('Location: '.$_SESSION['lastURL'].'?errors=1');
				exit;
			} else {
				$addPost = $mysql->prepare("INSERT INTO posts SET threadID = $threadID, title = :title, authorID = {$currentUser->userID}, message = :message, datePosted = :datePosted, postAs = ".($postAs?$postAs:'NULL'));
				$addPost->bindValue(':title', $title);
				$addPost->bindValue(':message', $message);
				$addPost->bindValue(':datePosted', date('Y-m-d H:i:s'));
				$addPost->execute();
				$postID = $mysql->lastInsertId();
			}
		} elseif ($_POST['edit']) {
			$postID = intval($_POST['edit']);
			$postInfo = $mysql->query('SELECT posts.threadID, forums.forumID, posts.title, posts.message, posts.authorID, posts.datePosted, posts.lastEdit, posts.timesEdited, threads.locked, threads.allowRolls, threads.allowDraws, relPosts.firstPostID FROM posts, threads, forums, threads_relPosts relPosts WHERE posts.postID = '.$postID.' AND posts.threadID = threads.threadID AND threads.forumID = forums.forumID AND threads.threadID = relPosts.threadID');
			$postInfo = $postInfo->fetch();
			$forumID = $postInfo['forumID'];
			
			$permissions = retrievePermissions($currentUser->userID, $forumID, 'editPost, addPoll, addRolls, addDraws, moderate', true);
			
			if (!$postInfo || ($postInfo['authorID'] == $currentUser->userID && !$threadManager->getPermission('editPost')) || ($postInfo['authorID'] != $currentUser->userID && !$threadManager->getPermission('moderate')) || ($postInfo['locked'] && !$threadManager->getPermission('moderate'))) { header('Location: /forums/thread/'.$postInfo['threadID']); exit; }
			if (strlen($message) == 0) $_SESSION['errors']['noMessage'] = 1;
			
			if ($postInfo['firstPostID'] == $postID && !isset($_POST['deletePoll'])) {
				$poll = array('poll' => sanitizeString($_POST['poll']), 'pollOptions' => preg_split('/(\r|\n)+/', $_POST['pollOptions']), 'optionsPerUser' => intval($_POST['optionsPerUser']), 'allowRevoting' => isset($_POST['allowRevoting'])?1:0);
				if (strlen($poll['poll']) == 0 && sizeof($poll['pollOptions']) > 1) $_SESSION['errors']['noPoll'] = 1;
				if (strlen($poll['poll']) != 0 && sizeof($poll['pollOptions']) <= 1) $_SESSION['errors']['noOptions'] = 1;
				if ($poll['optionsPerUser'] == 0 && strlen($poll['poll']) != 0 && sizeof($poll['pollOptions']) > 1) $_SESSION['errors']['noOptionsPerUser'] = 1;
			}
			
			if (sizeof($_SESSION['errors'])) {
				$_SESSION['errorVals'] = $_POST;
				$_SESSION['errorTime'] = time() + 300;
				header('Location: '.$_SESSION['lastURL'].'/?errors=1');
				exit;
			} else {
				$updatePostQuery = 'UPDATE posts SET message = :message';
				$updates = array('message' => $message);
				if ($postInfo['firstPostID'] == $postID && strlen($title) != 0) {
					$updatePostQuery .= ', title = :title';
					$updates['title'] = $title;
				}
				
				if (((time() + 300) > strtotime($postInfo['datePosted']) || (time() + 60) > strtotime($postInfo['lastEdit'])) && !$threadManager->getPermission('moderate') && ($postInfo['title'] != $title || $postInfo['message'] != $message)) {
					$updatePostQuery .= ', lastEdit = :lastEdit, timesEdited = :timesEdited';
					$updates['lastEdit'] = date('Y-m-d H:i:s');
					$updates['timesEdited'] = $postInfo['timesEdited'] + 1;
				}
				
				$updatePost = $mysql->prepare($updatePostQuery.' WHERE postID = '.$postID);
				foreach ($updates as $key => $value) $updatePost->bindValue(':'.$key, $value);
				$updatePost->execute();
				
				$threadID = $postInfo['threadID'];
				
				if ($postID == $postInfo['firstPostID']) {
					$sticky = isset($_POST['sticky']) && $threadManager->getPermission('moderate')?1:0;
					$allowRolls = isset($_POST['allowRolls']) && $threadManager->getPermission('addRolls')?1:0;
					$allowDraws = isset($_POST['allowDraws']) && $threadManager->getPermission('addDraws')?1:0;
					
					$mysql->query("UPDATE threads SET sticky = $sticky, allowRolls = $allowRolls, allowDraws = $allowDraws WHERE threadID = $threadID");
					
					if (isset($_POST['deletePoll'])) {
						$mysql->query("DELETE FROM po, pv USING forums_pollOptions po LEFT JOIN forums_pollVotes pv ON po.pollOptionID = pv.pollOptionID WHERE po.threadID = $threadID");
						$mysql->query("DELETE FROM forums_polls WHERE threadID = $threadID");
					} elseif (strlen($poll['poll']) && sizeof($poll['pollOptions'])) {
						$addPoll = $mysql->prepare("INSERT INTO forums_polls (threadID, poll, optionsPerUser, allowRevoting) VALUES ($threadID, :poll, :optionsPerUser, :allowRevoting) ON DUPLICATE KEY UPDATE poll = :poll, optionsPerUser = :optionsPerUser, allowRevoting = :allowRevoting");
						$addPoll->bindValue(':poll', $poll['poll']);
						$addPoll->bindValue(':optionsPerUser', $poll['optionsPerUser']);
						$addPoll->bindValue(':allowRevoting', $poll['allowRevoting']);
						$addPoll->execute();

						$pollOptions = $mysql->query("SELECT pollOptionID, `option` FROM forums_pollOptions WHERE threadID = $threadID");
						$options = array();
						foreach ($pollOptions as $optionInfo) $options[$optionInfo['pollOptionID']] = $optionInfo['option'];
						$oInserts = '';
						$addPollOption = $mysql->prepare("INSERT INTO forums_pollOptions SET threadID = $threadID, `option` = :option");
						foreach ($poll['pollOptions'] as $option) {
							if (in_array($option, $options)) unset($options[array_search($option, $options)]);
							else {
								$addPollOption->bindValue(':option', $option);
								$addPollOption->execute();
							}
						}
						if (sizeof($options)) $mysql->query('DELETE FROM po, pv USING forums_pollOptions po LEFT JOIN forums_pollVotes pv ON po.pollOptionID = pv.pollOptionID WHERE po.pollOptionID IN ('.implode(', ', array_keys($options)).')');
					}
				} else {
					$allowRolls = $postInfo['allowRolls'];
					$allowDraws = $postInfo['allowDraws'];
				}
				
				foreach ($_POST['nVisibility'] as $rollID => $nVisibility) {
					if (intval($nVisibility) != intval($_POST['oVisibility'][$rollID])) $mysql->query('UPDATE rolls SET visibility = '.intval($nVisibility)." WHERE rollID = $rollID");
				}
			}
		}
		
		 
		header('Location: /forums/thread/'.$threadManager->getThreadProperty('threadID').'?p='.$postID.'#p'.$postID));
	} else header('Location: /forums/thread/'.$threadID);
?>