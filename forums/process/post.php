<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	
	if (isset($_POST['advanced'])) {
		$_SESSION['message'] = $_POST['message'];
		header('Location: '.SITEROOT.'/forums/post/'.intval($_POST['threadID']));
	} elseif (isset($_POST['preview'])) {
		$_SESSION['previewVars'] = $_POST;
		header('Location: '.$_SESSION['lastURL'].'?preview=1');
	} elseif (isset($_POST['post'])) {
		unset ($_SESSION['errors'], $_SESSION['errorVals'], $_SESSION['errorTime']);
		$title = sanitizeString($_POST['title']);
		$message = sanitizeString($_POST['message']);
		
		$rolls = array();
		$draws = array();
		foreach ($_POST as $key => $value) {
			if (substr($key, 0, 4) == 'roll') {
				$parts = explode('_', $key);
				if (!isset($rolls[$parts[2]])) $rolls[$parts[2]] = array();
				$rolls[intval($parts[2])][$parts[1]] = $value;
			} elseif (substr($key, 0, 4) == 'deck') {
				$parts = explode('_', $key);
				if (!isset($draws[$parts[2]])) $draws[$parts[2]] = array();
				$draws[intval($parts[2])][$parts[1]] = $value;
			}
		}
		
		if (sizeof($rolls)) { foreach ($rolls as $num => $roll) {
			$cleanedRoll = array();
			if ($roll['roll']) {
				$ra = $roll['ra']?1:0;
				$indivRoll = parseRolls($roll['roll']);
				if (!$indivRoll) {
//					unset($rolls[$num]);
					$_SESSION['errors']['badRoll'] = 1;
				} else {
					$indivRoll = $indivRoll[0];
					$rollVals = rollDice($indivRoll, $ra);
					$cleanedRoll['roll'] = $roll['roll'];
					$cleanedRoll['reason'] = sanatizeString($roll['reason']);
					$cleanedRoll['ra'] = $ra;
					$cleanedRoll['total'] = $rollVals['total'];
					$cleanedRoll['indivRolls'] = $rollVals['indivRolls'];
					$cleanedRoll['visibility'] = $roll['visibility'];
					$rolls[$num] = $cleanedRoll;
				}
			} else unset($rolls[$num]);
		} }
		
		if (sizeof($draws)) {
			$deckInfos = $mysql->query('SELECT decks.deckID, decks.deck, decks.type, decks.position FROM decks INNER JOIN deckPermissions ON decks.deckID = deckPermissions.deckID WHERE deckPermissions.userID = '.$userID.' AND decks.deckID IN ('.implode(',', array_keys($draws)).')');
			$temp = array();
			foreach ($deckInfo as $deckInfo) $temp[$deckInfo['deckID']] = array('deck' => $deckInfo['deck'], 'type' => $deckInfo['type'], 'position' => $deckInfo['position']);
			$deckInfos = $temp;
			foreach ($draws as $deckID => &$draw) {
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
					$draw['reason'] = sanatizeString($draw['reason']);
					$draw['type'] = $deckInfos[$deckID]['type'];
//					$draws[$drawID] = $draw;
				} else unset($draws[$deckID]);
			}
		}
		
		$postID = 0;
		$threadID = 0;
		$noChat = FALSE;
		$permissions = array();
		
		if ($_POST['new']) {
			$forumID = intval($_POST['new']);
			$permissions = retrievePermissions($userID, $forumID, 'createThread, addPoll, addRolls, addDraws, moderate', TRUE);
			
			if (!$permissions['createThread']) { header('Location: '.SITEROOT.'/forums/'.$forumID); exit; }
			$sticky = isset($_POST['sticky']) && $permissions['moderate']?1:0;
			$allowRolls = isset($_POST['allowRolls']) && $permissions['addRolls']?1:0;
			$allowDraws = isset($_POST['allowDraws']) && $permissions['addDraws']?1:0;
			
			if (strlen($title) == 0) $_SESSION['errors']['noTitle'] = 1;
			if (strlen($message) == 0) $_SESSION['errors']['noMessage'] = 1;
			
			$poll = array('poll' => sanatizeString($_POST['poll']), 'pollOptions' => preg_split('/\n/', $_POST['pollOptions']), 'optionsPerUser' => intval($_POST['optionsPerUser']), 'allowRevoting' => isset($_POST['allowRevoting'])?1:0);
			if (strlen($poll['poll']) == 0 && sizeof($poll['pollOptions']) > 1) $_SESSION['errors']['noPoll'] = 1;
			if (strlen($poll['poll']) != 0 && sizeof($poll['pollOptions']) <= 1) $_SESSION['errors']['noOptions'] = 1;
			if ($poll['optionsPerUser'] == 0 && strlen($poll['poll']) != 0 && sizeof($poll['pollOptions']) > 1) $_SESSION['errors']['noOptionsPerUser'] = 1;
			
			
			if (sizeof($_SESSION['errors'])) {
				$_SESSION['errorVals'] = $_POST;
				$_SESSION['errorTime'] = time() + 300;
				header('Location: '.$_SESSION['lastURL'].'?errors=1');
				exit;
			} else {
				$mysql->query('INSERT INTO threads '.$mysql->setupInserts(array('forumID' => $forumID, 'sticky' => $sticky, 'allowRolls' => $allowRolls, 'allowDraws' => $allowDraws)));
				$threadID = $mysql->lastInsertId();
				
				$mysql->query('INSERT INTO posts '.$mysql->setupInserts(array('threadID' => $threadID, 'title' => $title, 'authorID' => $userID, 'message' => $message, 'datePosted' => date('Y-m-d H:i:s'))));
				$postID = $mysql->lastInsertId();
				
				if (strlen($poll['poll']) && sizeof($poll['pollOptions'])) {
					$mysql->query("INSERT INTO forums_polls (threadID, poll, optionsPerUser, allowRevoting) VALUES ($threadID, '{$poll['poll']}', {$poll['optionsPerUser']}, {$poll['allowRevoting']})");
					$oInserts = '';
					foreach ($poll['pollOptions'] as $option) $oInserts .= "($threadID, '".sanatizeString($option)."'), ";
					$mysql->query('INSERT INTO forums_pollOptions (threadID, `option`) VALUES '.substr($oInserts, 0, -2));
				}
			}
		} elseif ($_POST['threadID']) {
			$threadID = intval($_POST['threadID']);
			$threadInfo = $mysql->query('SELECT forumID, locked, allowRolls, allowDraws FROM threads WHERE threadID = '.$threadID);
			list($forumID, $locked, $allowRolls, $allowDraws) = $threadInfo->fetch(PDO::FETCH_NUM);
			$permissions = retrievePermissions($userID, $forumID, 'write, addRolls, addDraws', TRUE);
			if (!$permissions['write'] || $locked) { header('Location: '.SITEROOT.'/forums/'.$forumID); exit; }
			
			if (strlen($message) == 0) $_SESSION['errors']['noMessage'] = 1;
			
			if (sizeof($_SESSION['errors'])) {
				$_SESSION['errorVals'] = $_POST;
				$_SESSION['errorTime'] = time() + 300;
				header('Location: '.$_SESSION['lastURL'].'?errors=1');
				exit;
			} else {
//				$mysql->setTable('posts');
				$datePosted = date('Y-m-d H:i:s');
//				$mysql->setInserts(array('threadID' => $threadID, 'title' => $title, 'authorID' => $userID, 'message' => $message, 'datePosted' => $datePosted));
//				$mysql->stdQuery('insert');
				$mysql->query("INSERT INTO posts (threadID, title, authorID, message, datePosted) VALUES ($threadID, '$title', $userID, '$message', '$datePosted')");
				$postID = $mysql->lastInsertId();
			}
		} elseif ($_POST['edit']) {
			$postID = intval($_POST['edit']);
			$postInfo = $mysql->query('SELECT posts.threadID, forums.forumID, posts.title, posts.message, posts.authorID, posts.datePosted, posts.lastEdit, posts.timesEdited, threads.locked, threads.allowRolls, threads.allowDraws, relPosts.firstPostID FROM posts, threads, forums, threads_relPosts relPosts WHERE posts.postID = '.$postID.' AND posts.threadID = threads.threadID AND threads.forumID = forums.forumID AND threads.threadID = relPosts.threadID');
			$postInfo = $postInfo->fetch();
			$forumID = $postInfo['forumID'];
			
			$permissions = retrievePermissions($userID, $forumID, 'editPost, addPoll, addRolls, addDraws, moderate', TRUE);
			
			if (!$postInfo || ($postInfo['authorID'] == $userID && !$permissions['editPost']) || ($postInfo['authorID'] != $userID && !$permissions['moderate']) || ($postInfo['locked'] && !$permissions['moderate'])) { header('Location: '.SITEROOT.'/forums/thread/'.$postInfo['threadID']); exit; }
			if (strlen($message) == 0) $_SESSION['errors']['noMessage'] = 1;
			
			if ($postInfo['firstPostID'] == $postID && !isset($_POST['deletePoll'])) {
				$poll = array('poll' => sanatizeString($_POST['poll']), 'pollOptions' => preg_split('/(\r|\n)+/', $_POST['pollOptions']), 'optionsPerUser' => intval($_POST['optionsPerUser']), 'allowRevoting' => isset($_POST['allowRevoting'])?1:0);
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
				$updates = array('message = "'.$message.'"');
				if ($postInfo['firstPostID'] == $postID && strlen($title) != 0) $updates[] = 'title = "'.$title.'"';
				
				if (((time() + 300) > strtotime($postInfo['datePosted']) || (time() + 60) > strtotime($postInfo['lastEdit'])) && !$permissions['moderate'] && ($postInfo['title'] != $title || $postInfo['message'] != $message)) {
					$updates[] = 'lastEdit = "'.date('Y-m-d H:i:s').'"';
					$updates[] = 'timesEdited = '.($postInfo['timesEdited'] + 1);
				}
				
				$mysql->query('UPDATE posts SET '.implode(', ', $updates).' WHERE postID = '.$postID);
				
				$threadID = $postInfo['threadID'];
				
				if ($postID == $postInfo['firstPostID']) {
					$sticky = isset($_POST['sticky']) && $permissions['moderate']?1:0;
					$allowRolls = isset($_POST['allowRolls']) && $permissions['addRolls']?1:0;
					$allowDraws = isset($_POST['allowDraws']) && $permissions['addDraws']?1:0;
					
					$mysql->query("UPDATE threads SET sticky = $sticky, allowRolls = $allowRolls, allowDraws = $allowDraws WHERE threadID = $threadID");
					
					if (isset($_POST['deletePoll'])) {
						$mysql->query("DELETE FROM po, pv USING forums_pollOptions po LEFT JOIN forums_pollVotes pv ON po.pollOptionID = pv.pollOptionID WHERE po.threadID = $threadID");
						$mysql->query("DELETE FROM forums_polls WHERE threadID = $threadID");
					} elseif (strlen($poll['poll']) && sizeof($poll['pollOptions'])) {
						$mysql->query("INSERT INTO forums_polls (threadID, poll, optionsPerUser, allowRevoting) VALUES ($threadID, '{$poll['poll']}', {$poll['optionsPerUser']}, {$poll['allowRevoting']}) ON DUPLICATE KEY UPDATE poll = '{$poll['poll']}', optionsPerUser = {$poll['optionsPerUser']}, allowRevoting = {$poll['allowRevoting']}");
						
						$pollOptions = $mysql->query("SELECT pollOptionID, `option` FROM forums_pollOptions WHERE threadID = $threadID");
						$options = array();
						foreach ($pollOptions as $optionInfo) $options[$optionInfo['pollOptionID']] = $optionInfo['option'];
						$oInserts = '';
						foreach ($poll['pollOptions'] as $option) {
							if (in_array($option, $options)) unset($options[array_search($option, $options)]);
							else $oInserts .= "($threadID, '".sanatizeString($option)."'), ";
						}
						if (strlen($oInserts)) $mysql->query('INSERT INTO forums_pollOptions (threadID, `option`) VALUES '.substr($oInserts, 0, -2));
						if (sizeof($options)) $mysql->query('DELETE FROM po, pv USING forums_pollOptions po LEFT JOIN forums_pollVotes pv ON po.pollOptionID = pv.pollOptionID WHERE po.pollOptionID IN ('.implode(', ', array_keys($options)).')');
					}
				} else {
					$allowRolls = $postInfo['allowRolls'];
					$allowDraws = $postInfo['allowDraws'];
				}
				
				foreach ($_POST as $key => $value) { if (substr($key, 0, 11) == 'nVisibility') {
					$rollID = explode('_', $key);
					$rollID = $rollID[1];
					if (intval($_POST['nVisibility_'.$rollID]) != intval($_POST['oVisibility_'.$rollID])) $mysql->query('UPDATE rolls SET visibility = '.intval($_POST['nVisibility_'.$rollID])." WHERE rollID = $rollID");
				} }
			}
		}
		
		if ($postID && $threadID && (sizeof($rolls) || sizeof($draws))) {
			if (sizeof($rolls) && $permissions['addRolls'] && $allowRolls) {
				foreach($rolls as $num => $roll) $rolls[$num]['postID'] = $postID;
				$mysql->query('INSERT INTO rolls '.setupInserts($rolls));
			}
			
			if (sizeof($draws)) { foreach($draws as $deckID => $draw) {
				$mysql->query('UPDATE decks SET position = position + '.$draw['draw'].' WHERE deckID = '.$deckID);
				$mysql->query('INSERT INTO deckDraws '.$mysql->setupInserts(array('postID' => $postID, 'deckID' => $deckID, 'type' => $draw['type'], 'cardsDrawn' => $draw['cardsDrawn'], 'reveals' => "'".str_repeat('0', $draw['draw'])."'", 'reason' => $draw['reason'])));
			} }
		}
		
		$mysql->query("INSERT INTO forums_readData_threads SET threadID = {$threadID}, userID = {$userID}, lastRead = {$postID} ON DUPLICATE KEY UPDATE lastRead = {$postID}");
		 
		if ($postID && $threadID) header('Location: '.SITEROOT.'/forums/thread/'.$threadID.($postID == $postInfo['firstPostID']?'':'?p='.$postID.'#p'.$postID));
		else header('Location: '.SITEROOT.'/403');
	} else header('Location: '.SITEROOT.'/forums/thread/'.$threadID);
?>