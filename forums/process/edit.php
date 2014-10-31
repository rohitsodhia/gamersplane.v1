<?
	$postID = intval($_POST['postID']);
	
	if (isset($_POST['post'])) {
		$title = sanatizeString($_POST['title']);
		$message = sanatizeString($_POST['message']);
		
		$postInfo = $mysql->query('SELECT posts.threadID, forums.forumID, posts.title, posts.message, posts.authorID, posts.datePosted, posts.lastEdit, posts.timesEdited, threads.locked FROM posts, threads, forums WHERE posts.postID = '.$postID.' && posts.threadID = threads.threadID && threads.forumID = forums.forumID');
		$postInfo = $postInfo->fetch();
		
		$permissions = retrievePermissions($currentUser->userID, $postInfo['forumID'], 'moderate', TRUE);
						
		if ($postInfo && ($permissions['moderate'] || $currentUser->userID = $postInfo['authorID']) && !$postInfo['locked']) {
			unset($_SESSION['errors']);
			unset($_SESSION['errorTime']);
			
			if (($postInfo['title'] != $title || $postInfo['message'] != $message) && strlen($title > 0) {
				$mysql->setTable('posts');
				$mysql->setWhere('postID = '.$postID);
				$updates = array('title' => $title, 'message' => $message);
				if ((((time() + 300) > strtotime($postInfo['datePosted']) && $postInfo['lastEdit'] == '0000-00-00 00:00:00') || ((time() + 60) > strtotime($postInfo['lastEdit']))) && $permissions['moderate'] == 0) {
					$updates['lastEdit'] = date('Y-m-d H:i:s');
					$updates['timesEdited'] = $postInfo['timesEdited'] + 1;
				}
				$mysql->setUpdates($updates);
//				$mysql->stdQuery('update', 'where');
			}
			
//			header('Location: /chat/success/edit/'.$postInfo['threadID']);
		} else header('Location: /unauthorized');
	} else header('Location: /forums');
?>