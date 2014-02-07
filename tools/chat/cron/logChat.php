<?
	if ($pathOptions[2] == '05q7QdN7Zu8vDKpRwl5h') {
		$gameIDs = $mysql->query('SELECT DISTINCT gameID FROM chat_messages WHERE postedOn < NOW() - INTERVAL 1 HOUR AND logged = 0');
		$temp = array();
		foreach ($gameIDs as $info) $temp[] = $info['gameID'];
		$gameIDs = $temp;
		
		if (sizeof($gameIDs)) {
			$gameInfos = $mysql->query('SELECT gameID, gmID, forumID, logForumID FROM games WHERE gameID IN ('.implode(', ', $gameIDs).')');
			$temp = array();
			foreach ($gameInfo as $info) {
				if ($info['logForumID'] == 0) {
					$subforums = $mysql->query('SELECT forumID FROM forums WHERE parentID = '.$info['forumID']);
					$order = $subforums->rowCount() + 1;
					$mysql->query("INSERT INTO forums (title, parentID, `order`) VALUES ('Chat Logs', {$info['forumID']}, $order)");
					$forumID = $mysql->lastInsertId();
					$mysql->query('INSERT INTO forums_permissions_general (forumID) VALUES ('.$forumID.')');
					$mysql->query("UPDATE forums SET heritage = '".sql_forumIDPad(2)."-".sql_forumIDPad($info['forumID'])."-".sql_forumIDPad($forumID)."' WHERE forumID = $forumID");
					$mysql->query("UPDATE games SET logForumID = $forumID WHERE gameID = {$info['gameID']}");
					$info['logForumID'] = $forumID;
				}
				$temp[$info['gameID']] = $info;
			}
			$gameInfos = $temp;
			
			if (sizeof($gameIDs)) {
				$mysql->query('UPDATE chat_sessions SET locked = 1 WHERE gameID IN ('.implode(', ', $gameIDs).')');
				$message = '';
				foreach ($gameIDs as $gameID) {
					$message = '';
					$title = '';
					$messageInfos = $mysql->query("SELECT users.username, messages.postedOn, messages.message FROM chat_messages messages LEFT JOIN users ON messages.posterID = users.userID WHERE gameID = $gameID AND logged = 0");
					foreach ($messageInfos as $messageInfo) {
						if (strlen($title) == 0) $title = 'Chat Session: '.date('M j, Y', strtotime($messageInfo['postedOn'])).', Started at '.date('H:i:s', strtotime($messageInfo['postedOn']));
						
						$message .= '[b]'.date('H:i:s', strtotime($messageInfo['postedOn']))." {$messageInfo['username']}[/b] > ".printReady($messageInfo['message'])."\n";
					}
					
					$mysql->query("INSERT INTO threads (forumID) VALUES ({$gameInfos[$gameID]['logForumID']})");
					$threadID = $mysql->lastInsertId();
					$mysql->query("INSERT INTO posts (threadID, title, authorID, message, datePosted) VALUES ($threadID, '".sanitizeString($title)."', {$gameInfos[$gameID]['gmID']}, '".sanitizeString($message)."', NOW())");
				}
				$mysql->query('UPDATE chat_sessions SET locked = 0 WHERE gameID IN ('.implode(', ', $gameIDs).')');
				$mysql->query('UPDATE chat_messages SET logged = 1 WHERE gameID IN ('.implode(', ', $gameIDs).')');
			}
		}
	} else header('Location: '.SITEROOT.'/');
?>