<?
	addPackage('forum');

	if (isset($_POST['advanced'])) {
		$_SESSION['message'] = $_POST['message'];
		header('Location: /forums/post/'.intval($_POST['threadID']).'/');
	} elseif (isset($_POST['preview'])) {
		$_SESSION['previewVars'] = $_POST;
		header("Location: {$_POST['postURL']}?preview=1");
	} elseif (isset($_POST['post'])) {
		unset ($_SESSION['errors'], $_SESSION['errorVals'], $_SESSION['errorTime']);

		if ($_POST['threadID']) {
			$threadID = intval($_POST['threadID']);
			$threadManager = new ThreadManager($threadID);
			if (!$threadManager->getPermissions('write') || ($locked && $threadManager->getPermissions('moderate'))) { header('Location: /forums/'.$forumID.'/'); exit; }
		}

		if ($_POST['edit']) {
			$postID = intval($_POST['edit']);
			$post = new Post($postID);
			$threadID = intval($post->threadID);
		} else
			$post = new Post();
		$post->setTitle($_POST['title']);
		$post->setPostAs($_POST['postAs']);
		$message = $_POST['message'];
		if (isset($threadID))
			$gameID = (int) $mysql->query("SELECT f.gameID FROM threads t INNER JOIN forums f ON t.forumID = f.forumID WHERE t.threadID = {$threadID} LIMIT 1")->fetchColumn();
		elseif (isset($_POST['new']))
			$gameID = (int) $mysql->query("SELECT gameID FROM forums f WHERE forumID = ".intval($_POST['new'])." LIMIT 1")->fetchColumn();

		if (preg_match_all('/\[note="?(\w[\w +;,]+?)"?](.*?)\[\/note\]/ms', $message, $matches, PREG_SET_ORDER)) {
			$allUsers = array();
			foreach ($matches as $match)
				foreach (preg_split('/[^\w]+/', $match[1]) as $eachUser)
					$allUsers[] = $eachUser;
			$allUsers = array_unique($allUsers);
			$userCheck = $mysql->prepare('SELECT username FROM users WHERE LOWER(username) = :username');
			foreach ($allUsers as $key => $username) {
				$userCheck->bindValue(':username', strtolower($username));
				$userCheck->execute();
				if (!$userCheck->rowCount())
					unset($allUsers[$key]);
				else
					$allUsers[$key] = $userCheck->fetchColumn();
			}
			foreach ($matches as $match) {
				$matchUsers = preg_split('/[^\w]+/', $match[1]);
				$validUsers = array();
				foreach ($matchUsers as $user) {
					foreach ($allUsers as $realUser) {
						if (strtolower($user) == strtolower($realUser))
							$validUsers[] = $realUser;
					}
				}
				$validNote = preg_replace('/\[note.*?\]/', '[note="'.implode(',', $validUsers).'"]', $match[0]);
				$message = str_replace($match[0], $validNote, $message);
			}
		}
		$post->setMessage($message);

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
			$returnFields = array('players' => true);
			if (sizeof($_POST['decks']))
				$returnFields['decks'] = true;
			$game = $mongo->games->findOne(array('gameID' => $gameID, 'players.user.userID' => $currentUser->userID), $returnFields);
			if ($game) {
				$rDecks = $game['decks'];
				$decks = array();
				$draws = array_filter($_POST['decks'], function($value) { return intval($value['draw']) > 0?true:false; });
				foreach ($rDecks as $deck)
					if (array_key_exists((int) $deck['deckID'], $draws) && in_array($currentUser->userID, $deck['permissions']))
						$decks[$deck['deckID']] = $deck;
				$isGM = null;
				foreach ($game['players'] as $player) {
					if ($player['user']['userID'] == $currentUser->userID) {
						$isGM = $player['isGM'];
						break;
					}
				}
				foreach ($draws as $deckID => $draw) {
					if ($draw['draw'] > 0) {
						$deck = $decks[$deckID]['deck'];
						if (strlen($draw['reason']) == 0) {
							$_SESSION['errors']['noDrawReason'] = 1;
							break;
						} elseif ($decks[$deckID]['position'] + $draw['draw'] - 1 > sizeof($deck)) {
							$_SESSION['errors']['overdrawn'] = 1;
							break;
						}

						$draw['cardsDrawn'] = array();
						for ($count = $decks[$deckID]['position']; $count <= $decks[$deckID]['position'] + $draw['draw'] - 1; $count++)
							$draw['cardsDrawn'][] = $deck[$count - 1];
						$draw['cardsDrawn'] = implode('~', $draw['cardsDrawn']);
						$draw['reason'] = sanitizeString($draw['reason']);
						$draw['type'] = $decks[$deckID]['type'];
						$post->addDraw($deckID, $draw);
					}
				}
			}
		}

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
			if (strlen($threadManager->thread->poll->getQuestion()) && sizeof($threadManager->thread->poll->getOptions())) {
				if (strlen($threadManager->thread->poll->getQuestion()) == 0 && sizeof($threadManager->thread->poll->getOptions()) != 0)
					$formErrors->addError('noQuestion');
				if (strlen($threadManager->thread->poll->getQuestion()) && sizeof($threadManager->thread->poll->getOptions()) <= 1)
					$formErrors->addError('noOptions');
				$threadManager->thread->poll->setOptionsPerUser($_POST['optionsPerUser']);
				if ($threadManager->thread->poll->getOptionsPerUser() == 0)
					$formErrors->addError('noOptionsPerUser');
				$threadManager->thread->poll->setAllowRevoting($_POST['allowRevoting']);
			}

			if ($formErrors->errorsExist()) {
				$formErrors->setErrors('post', $_POST);
				header('Location: '.$_SESSION['lastURL'].'?errors=1');
				exit;
			} else
				$postID = $threadManager->saveThread($post);
		} elseif ($_POST['threadID']) {
			$threadID = intval($_POST['threadID']);

			$post->setThreadID($threadID);
			if (strlen($post->getTitle()) == 0)
				$title = 'Re: '.$threadManager->getThreadProperty('title');
			if (strlen($post->getMessage()) == 0) $formErrors->addError('noMessage');

			if ($formErrors->errorsExist()) {
				$formErrors->setErrors('post', $_POST);
				header('Location: '.$_SESSION['lastURL'].'?errors=1');
				exit;
			} else {
				$postID = $post->savePost();
				$mysql->query("UPDATE threads SET lastPostID = {$postID} WHERE threadID = {$threadID}");
				$threadManager->updatePostCount();
				$threadManager->updateLastRead($postID);
			}
		} elseif ($_POST['edit']) {
			$threadManager = new ThreadManager($post->getThreadID());
			$firstPost = $threadManager->getThreadProperty('firstPostID') == $post->getPostID()?true:false;

			if (!(($post->getAuthor('userID') == $currentUser->userID && $threadManager->getPermissions('editPost') && !$threadManager->thread->getStates('locked')) || $threadManager->getPermissions('moderate'))) { header('Location: /forums/thread/'.$post->getThreadID().'/'); exit; }

			if ($firstPost && strlen($post->getTitle()) == 0)
				$formErrors->addError('noTitle');
			if (strlen($post->getMessage()) == 0)
				$formErrors->addError('noMessage');

			if ($firstPost) {
				$threadManager->thread->setState('sticky', isset($_POST['sticky']) && $threadManager->getPermissions('moderate')?true:false);
				$threadManager->thread->setState('locked', isset($_POST['locked']) && $threadManager->getPermissions('moderate')?true:false);
				$threadManager->thread->setAllowRolls(isset($_POST['allowRolls']) && $threadManager->getPermissions('addRolls')?true:false);
				$threadManager->thread->setAllowDraws(isset($_POST['allowDraws']) && $threadManager->getPermissions('addRolls')?true:false);

				if (!isset($_POST['deletePoll'])) {
					$threadManager->thread->poll->setQuestion($_POST['poll']);
					$threadManager->thread->poll->parseOptions($_POST['pollOptions']);
					if (strlen($threadManager->thread->poll->getQuestion()) && sizeof($threadManager->thread->poll->getOptions())) {
						if (strlen($threadManager->thread->poll->getQuestion()) == 0 && sizeof($threadManager->thread->poll->getOptions()) != 0)
							$formErrors->addError('noQuestion');
						if (strlen($threadManager->thread->poll->getQuestion()) && sizeof($threadManager->thread->poll->getOptions()) <= 1)
							$formErrors->addError('noOptions');
						$threadManager->thread->poll->setOptionsPerUser($_POST['optionsPerUser']);
						if ($threadManager->thread->poll->getOptionsPerUser() == 0)
							$formErrors->addError('noOptionsPerUser');
						$threadManager->thread->poll->setAllowRevoting($_POST['allowRevoting']);
					}
				}
			}

			if ($formErrors->errorsExist()) {
				$formErrors->setErrors('post', $_POST);
				header('Location: '.$_SESSION['lastURL'].'?errors=1');
				exit;
			} else {
				if (((time() + 300) > strtotime($post->getDatePosted()) || (time() + 60) > strtotime($post->getLastEdit())) && !$threadManager->getPermissions('moderate') && $post->getModified()) {
					$edited = true;
					$post->updateEdited();
				}

				if ($firstPost) {
					$threadManager->thread->setState('sticky', isset($_POST['sticky']) && $threadManager->getPermissions('moderate')?true:false);
					$threadManager->thread->setState('locked', isset($_POST['locked']) && $threadManager->getPermissions('moderate')?true:false);
					$threadManager->thread->setAllowRolls(isset($_POST['allowRolls']) && $threadManager->getPermissions('addRolls')?true:false);
					$threadManager->thread->setAllowDraws(isset($_POST['allowDraws']) && $threadManager->getPermissions('addDraws')?true:false);

					if (isset($_POST['deletePoll'])) $threadManager->deletePoll();

					$threadManager->saveThread($post);
				} else {
					$allowRolls = $postInfo['allowRolls'];
					$allowDraws = $postInfo['allowDraws'];

					$post->savePost();
				}

				foreach ($_POST['nVisibility'] as $rollID => $nVisibility) {
					if (intval($nVisibility) != intval($_POST['oVisibility'][$rollID])) $mysql->query('UPDATE rolls SET visibility = '.intval($nVisibility)." WHERE rollID = $rollID");
				}
			}
		}

		if (!isset($_POST['edit'])) {
			$subbedUsers = $mysql->query("SELECT u.email FROM forumSubs s INNER JOIN users u ON s.userID = u.userID WHERE s.userID != {$currentUser->userID} AND ((s.type = 'f' AND s.ID = {$threadManager->getThreadProperty('forumID')}) OR (s.type = 't' AND s.ID = {$threadManager->getThreadID()}))");
			$subs = array();
			if ($subbedUsers->rowCount())
				foreach ($subbedUsers as $user)
					$subs[] = $user['email'];
			if (sizeof($subs)) {
				$subs = array_unique($subs);
				ob_start();
				include('forums/process/threadSubEmail.php');
				$email = ob_get_contents();
				ob_end_clean();
				foreach ($subs as $sub)
					mail($sub, "New Posts", $email, "Content-type: text/html\r\nFrom: Gamers Plane <contact@gamersplane.com>");
			}
		}

		header('Location: /forums/thread/'.$threadManager->threadID.'/?p='.$post->getPostID().'#p'.$post->getPostID());
	} else header('Location: /forums/thread/'.$threadID);
?>
