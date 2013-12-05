<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($pathOptions[1]);
	$redirect = FALSE;
	$pType = in_array($pathOptions[3], array('group', 'user'))?$pathOptions[3]:NULL;
	$typeID = intval($pathOptions[4]);

	$isAdmin = $mysql->query("SELECT f.forumID, p.forumID, fa.forumID FROM forums f, forums p, forumAdmins fa WHERE fa.userID = 1 AND fa.forumID = p.forumID AND f.heritage LIKE CONCAT(p.heritage, '%') AND f.forumID = $forumID");
	$forumInfo = $mysql->query("SELECT forumID, title, forumType, parentID, heritage FROM forums WHERE forumID = $forumID");
	$forumInfo = $forumInfo->fetch();
	if (!$isAdmin->rowCount() || ($forumInfo['parentID'] == 2 && $pType == 'group') || $pType == NULL || $typeID == 0) { header('Location: '.SITEROOT.'/forums/'); exit; }
	
	$gameInfo = $mysql->query('SELECT gameID, groupID FROM games WHERE forumID = '.$forumID);
	$gameInfo = $gameInfo->fetch();
	$gameInfo = $gameInfo?$gameInfo:FALSE;

	if ($pType == 'group') $typeInfo = $mysql->query("SELECT name FROM forums_groups WHERE groupID = $typeID");
	else $typeInfo = $mysql->query("SELECT username FROM users WHERE userID = $typeID");
	$name = $typeInfo->fetchColumn();
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Delete Forum</h1>
		
		<p>Are you sure you want to delete the permissions for  <strong><?=$name?></strong> in the forum <strong><?=$forumInfo['title']?></strong>? This cannot be reversed!</p>

		<form method="post" action="<?=SITEROOT?>/forums/process/acp/deletePermission">
			<input type="hidden" name="forumID" value="<?=$forumID?>">
			<input type="hidden" name="pType" value="<?=$pType?>">
			<input type="hidden" name="typeID" value="<?=$typeID?>">
			<div class="buttonPanel alignCenter"><button type="submit" name="delete" class="fancyButton">Delete</button></div>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>