<?
	addPackage('forum');
	$postID = intval($_POST['postID']);
	$post = new Post($postID);
	$threadManager = new ThreadManager($post->getThreadID());
	$deleteType = ($threadManager->getFirstPostID() == $postID)?'thread':'post';

	if (isset($_POST['delete'])) {
 		if (($threadManager->getPermissions('moderate') || $post->getAuthor('userID') == $currentUser->userID) && $postID != $threadManager->getFirstPostID() && ($threadManager->getPermissions('deletePost') || $threadManager->getThreadProperty('states[publicPosting]'))) {
 			$threadManager->deletePost($post);
			echo 'refresh';
		} elseif (($threadManager->getPermissions('moderate') || $post->getAuthor('userID') == $currentUser->userID) && $postID == $threadManager->getFirstPostID() && $threadManager->getPermissions('deleteThread')) {
			$threadManager->deleteThread();
			echo $threadManager->getForumProperty('forumID');
		} else echo 0;
	} else echo 0;
?>