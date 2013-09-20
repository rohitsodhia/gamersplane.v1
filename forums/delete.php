<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$postID = intval($pathOptions[1]);
	
	$postInfo = $mysql->query("SELECT posts.authorID, threads.forumID, threads.threadID, relPosts.firstPostID FROM posts, threads, threads_relPosts relPosts WHERE posts.postID = $postID AND posts.threadID = threads.threadID AND threads.threadID = relPosts.threadID");
	$postInfo = $postInfo->fetch();
	$permissions = retrievePermissions($userID, $postInfo['forumID'], 'deletePost, deleteThread, moderate', TRUE);
	$deleteType = ($postInfo['firstPostID'] == $postID)?'thread':'post';
	
	if ($postInfo['authorID'] != $userID && !$permissions['moderate'] || $postInfo['authorID'] == $userID && $postInfo['firstPostID'] != $postID && !$permissions['deletePost'] || $postInfo['authorID'] != $userID && $postInfo['firstPostID'] == $postID && !$permissions['deleteThread']) { header('Location: '.SITEROOT.'/forums/thread/'.$postInfo['threadID']); exit; }
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Delete <?=ucwords($deleteType)?></h1>
		
		<p class="alignCenter">Are you sure you wanna delete this <?=$deleteType?>?</p>
		<p class="alignCenter">This cannot be reversed!</p>
		
		<form method="post" action="<?=SITEROOT?>/forums/process/delete/" class="alignCenter">
			<input type="hidden" name="postID" value="<?=$postID?>">
			<button type="submit" name="delete" class="btn_delete"></button>
			<button type="submit" name="cancel" class="btn_cancel"></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>