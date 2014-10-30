<?
	$forumID = intval($pathOptions[1]);
	$redirect = FALSE;

	$isAdmin = $mysql->query("SELECT f.forumID, p.forumID, fa.forumID FROM forums f, forums p, forumAdmins fa WHERE fa.userID = 1 AND fa.forumID = p.forumID AND f.heritage LIKE CONCAT(p.heritage, '%') AND f.forumID = $forumID");
	$forumInfo = $mysql->query("SELECT forumID, title, forumType, parentID, heritage FROM forums WHERE forumID = $forumID");
	$forumInfo = $forumInfo->fetch();
	$permissionType = in_array($pathOptions[3], array('group', 'user'))?$pathOptions[3]:FALSE;
	if (!$isAdmin->rowCount() || ($forumInfo['parentID'] == 2 && $forumID != 10) || !$permissionType) { header('Location: /forums/'); exit; }
	$gameForum = strpos($forumInfo['heritage'], sql_forumIDPad(2)) !== FALSE && $forumID != 10?TRUE:FALSE;
	if ($gameForum) {
		$heritage = explode('-', $forumInfo['heritage']);
		$gameID = $mysql->query('SELECT gameID FROM games WHERE forumID = '.intval($heritage[1]));
		$gameID = $gameID->fetchColumn();
	} else $gameID = NULL;
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Add <?=ucwords($permissionType)?> Permission</h1>
		
<?
	if ($gameForum && $permissionType == 'user') $validOpts = $mysql->query("SELECT u.userID optID, u.username title FROM users u INNER JOIN players p ON u.userID = p.userID and p.approved = 1 LEFT JOIN forums_permissions_users per ON u.userID = per.userID AND per.forumID = {$forumID} WHERE p.gameID = {$gameID} AND per.forumID IS NULL");
	elseif ($gameForum && $permissionType == 'user') $validOpts = $mysql->query("SELECT u.userID optID, u.username title FROM users u LEFT JOIN forums_permissions_users per ON u.userID = per.userID AND per.forumID = {$forumID} WHERE per.forumID IS NULL");
	elseif ($permissionType == 'group') $validOpts = $mysql->query("SELECT fg.groupID optID, fg.name title FROM forums_groups fg LEFT JOIN forums_permissions_groups per ON fg.groupID = per.groupID AND per.forumID = {$forumID} WHERE fg.ownerID = {$currentUser->userID}");

	if ((!$gameForum && $permissionType == 'user') || $validOpts->rowCount()) {
?>
		<form method="post" action="/forums/process/acp/permissions">
			<div id="optList">
				<input id="gameForum" type="hidden" name="gameForum" value="<?=$gameForum?>">
				<input id="permissionType" type="hidden" name="pType" value="<?=$permissionType?>">
<?		if (!$gameForum && $permissionType == 'user' || $permissionType == 'group') { ?>
				<input id="optSearch" type="text" name="option" class="placeholder" autocomplete="off" data-placeholder="<?=$permissionType == 'user'?'Username':'Group Name'?>">
<?		} else { foreach ($validOpts as $validOpt) { ?>
				<div class="tr">
					<input type="radio" name="option" value="<?=$validOpt['optID']?>"> <?=$validOpt['title']?>
				</div>
<?		} } ?>
			</div>
			<div id="permissionsList">
<?	foreach ($permissionTypes as $type => $title) { ?>
				<div class="tr clearfix">
					<div class="permission_type textLabel"><?=$title?></div>
					<select name="permissions[<?=$type?>]">
						<option value="1"<?=$permissions[$type] == 1?' selected="selected"':''?>>Yes</option>
						<option value="0"<?=$permissions[$type] == 0?' selected="selected"':''?>>Don't Care</option>
						<option value="-1"<?=$permissions[$type] == -1?' selected="selected"':''?>>No</option>
					</select>
				</div>
<?	} ?>
<?	if ($pType != 'general') echo "\t\t\t\t<p>Giving a {$pType} moderator privilages will grant it all other privilages listed here.</p>\n"; ?>
			</div>
			<input id="forumID" type="hidden" name="forumID" value="<?=$forumID?>">
			<div class="buttonPanel alignCenter"><button type="submit" name="add" class="fancyButton">Add</button></div>
		</form>
<?	} else { ?>
		<p>No valid users to add</p>
<?	} ?>
<?	require_once(FILEROOT.'/footer.php'); ?>