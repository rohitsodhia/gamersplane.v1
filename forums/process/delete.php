<?
	$userID = intval($_SESSION['userID']);
	$postID = intval($_POST['postID']);
	
	if (isset($_POST['delete'])) {
		$postInfo = $mysql->query('SELECT posts.authorID, threads.forumID, threads.threadID, relPosts.firstPostID FROM posts, threads, threads_relPosts relPosts WHERE posts.postID = '.$postID.' AND posts.threadID = threads.threadID AND threads.threadID = relPosts.threadID');
		$postInfo = $postInfo->fetch();
		$permissions = retrievePermissions($userID, $postInfo['forumID'], 'deletePost, deleteThread, moderate', TRUE);
		
 		if (($permissions['moderate'] || $postInfo['authorID'] == $userID) && $postID != $postInfo['firstPostID'] && $permissions['deletePost']) {
			$mysql->query('DELETE FROM posts, rolls, deckDraws USING posts LEFT JOIN rolls ON posts.postID = rolls.postID LEFT JOIN deckDraws ON posts.postID = deckDraws.postID WHERE posts.postID = '.$postID);
			echo 'refresh';
		} elseif (($permissions['moderate'] || $postInfo['authorID'] == $userID) && $postID == $postInfo['firstPostID'] && $permissions['deleteThread']) {
			$mysql->query('DELETE FROM threads, posts, rolls, deckDraws USING threads LEFT JOIN posts ON threads.threadID = posts.threadID LEFT JOIN rolls ON posts.postID = rolls.postID LEFT JOIN deckDraws ON posts.postID = deckDraws.postID WHERE threads.threadID = '.$postInfo['threadID']);
			echo $postInfo['forumID'];
		} else echo 0;
	} else echo 0;
?>