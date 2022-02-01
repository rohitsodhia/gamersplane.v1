<?
	addPackage('forum');
	$postID = intval($pathOptions[1]);
	$post = new Post($postID);
	$threadManager = new ThreadManager($post->getThreadID());
	$deleteType = ($threadManager->getFirstPostID() == $postID)?'thread':'post';

	if (($post->getAuthor('userID') != $currentUser->userID && !$threadManager->getPermissions('moderate')) || ($post->getAuthor('userID') == $currentUser->userID && $threadManager->getFirstPostID() != $postID && !$threadManager->getPermissions('deletePost') && !$threadManager->getThreadProperty('states[publicPosting]')) || ($post->getAuthor('userID') != $currentUser->userID && $threadManager->getFirstPostID() == $postID && !$threadManager->getPermissions('deleteThread'))) { header('Location: /forums/thread/'.$postInfo['threadID']); exit; }
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Delete <?=ucwords($deleteType)?></h1>

		<p class="alignCenter">Are you sure you wanna delete this <?=$deleteType?>?</p>
		<p class="alignCenter">This cannot be reversed!</p>

		<form method="post" action="/forums/process/delete/" class="alignCenter">
			<input type="hidden" name="postID" value="<?=$postID?>">
			<button type="submit" name="delete" class="fancyButton">Delete</button>
			<button type="submit" name="cancel" class="fancyButton">Cancel</button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>