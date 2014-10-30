<?
	$forumID = intval($pathOptions[1]);
	$redirect = FALSE;

	$isAdmin = $mysql->query("SELECT f.forumID, p.forumID, fa.forumID FROM forums f, forums p, forumAdmins fa WHERE fa.userID = 1 AND fa.forumID = p.forumID AND f.heritage LIKE CONCAT(p.heritage, '%') AND f.forumID = $forumID");
	$forumInfo = $mysql->query("SELECT forumID, title, forumType, parentID, heritage FROM forums WHERE forumID = $forumID");
	$forumInfo = $forumInfo->fetch();
	if (!$isAdmin->rowCount() || $forumInfo['parentID'] == 2) { header('Location: /forums/'); exit; }
	
	$gameInfo = $mysql->query('SELECT gameID, groupID FROM games WHERE forumID = '.$forumID);
	$gameInfo = $gameInfo->fetch();
	$gameInfo = $gameInfo?$gameInfo:FALSE;
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Delete Forum</h1>
		
		<p>Are you sure you want to delete <strong><?=$forumInfo['title']?></strong>? This cannot be reversed!</p>
		<p>This will delete all threads, posts, and relating content in this forum, as well as in any subforums and the subforums themselves.</p>

		<form method="post" action="/forums/process/acp/deleteForum">
			<input type="hidden" name="forumID" value="<?=$forumID?>">
			<div class="buttonPanel alignCenter"><button type="submit" name="delete" class="fancyButton">Delete</button></div>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>