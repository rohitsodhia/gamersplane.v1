<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$postID = intval($_POST['postID']);
	
	if (isset($_POST['delete'])) {
		$postInfo = $mysql->query('SELECT posts.authorID, threads.forumID, threads.threadID, relPosts.firstPostID FROM posts, threads, threads_relPosts relPosts WHERE posts.postID = '.$postID.' AND posts.threadID = threads.threadID AND threads.threadID = relPosts.threadID');
		$postInfo = $postInfo->fetch();
		$permissions = retrievePermissions($userID, $postInfo['forumID'], 'deletePost, deleteThread, moderate', TRUE);
		
		if ($postInfo['authorID'] != $userID && !$permissions['moderate'] || $postInfo['authorID'] == $userID && $postInfo['firstPostID'] != $postID && !$permissions['deletePost'] || $postInfo['authorID'] != $userID && $postInfo['firstPostID'] == $postID && !$permissions['deleteThread']) header('Location: '.SITEROOT.'/forums/thread/'.$postInfo['threadID'].'?deletePermission=1');
 		elseif ($postID != $postInfo['firstPostID']) {
			$mysql->query('DELETE FROM posts, rolls, deckDraws USING posts LEFT JOIN rolls ON posts.postID = rolls.postID LEFT JOIN deckDraws ON posts.postID = deckDraws.postID WHERE posts.postID = '.$postID);
		} else {
			$mysql->query('DELETE FROM threads, posts, rolls, deckDraws USING threads LEFT JOIN posts ON threads.threadID = posts.threadID LEFT JOIN rolls ON posts.postID = rolls.postID LEFT JOIN deckDraws ON posts.postID = deckDraws.postID WHERE threads.threadID = '.$postInfo['threadID']);
		}
		header('Location: '.SITEROOT.'/forums/'.$postInfo['forumID']);
	} else header('Location: '.SITEROOT.'/forums');
?>