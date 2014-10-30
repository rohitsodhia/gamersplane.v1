<?
	$forumID = intval($pathOptions[2]);
	$redirect = FALSE;
	
	$adminForums = $mysql->query("SELECT forumID FROM forumAdmins WHERE userID = {$currentUser->userID}");
	$temp = array();
	foreach ($adminForums as $aForumID) $temp[] = $aForumID['forumID'];
	$adminForums = $temp;
	if ($forumID != 0) {
		$forumInfo = $mysql->query('SELECT parentID, heritage FROM forums WHERE forumID = '.$forumID);
		list($parentID, $heritage) = $forumInfo->fetch(PDO::FETCH_NUM);
		$heritage = explode('-', $heritage);
		foreach ($heritage as $key => $hForumID) $heritage[$key] = intval($hForumID);
	}
	
	if (!(in_array(0, $adminForums) || array_intersect($adminForums, $heritage))) { header('Location: /forums/'); exit; }
	
	$gameInfo = $mysql->query('SELECT gameID, groupID FROM games WHERE forumID = '.$forumID);
	$gameInfo = $gameInfo->fetch();
	$gameInfo = $gameInfo?$gameInfo:FALSE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Forum ACP: Permissions</h1>
		
		<div id="breadcrumbs">
			<a id="forumReturn" href="<?='/forums/'.($forumID?$forumID:'')?>">Return to forum</a>
			
<?
	$parentAdmin = 0;
	if (in_array(0, $adminForums)) {
		$parentAdmin = 1;
		echo "\t\t\t<a href=\"/forums/acp/0\">Index</a>".($forumID != 0?' > ':'')."\n";
	} else echo "\t\t\tIndex".($forumID != 0?' > ':'')."\n";
	if ($forumID != 0) {
		$breadcrumbs = $mysql->query('SELECT forumID, title FROM forums WHERE forumID IN ('.implode(',', $heritage).')');
		$breadcrumbForums = array();
		foreach ($breadcrumbs as $forumInfo) $breadcrumbForums[$forumInfo['forumID']] = printReady($forumInfo['title']);
		$fCounter = 1;
		foreach ($heritage as $hForumID) {
			if (!$parentAdmin) $parentAdmin = in_array($hForumID, $adminForums)?1:0;
			if ($parentAdmin) echo "\t\t\t<a href=\"/forums/acp/".$hForumID.'">'.$breadcrumbForums[$hForumID].'</a>'.($fCounter != sizeof($heritage)?' > ':'')."\n";
			else echo "\t\t\t".$breadcrumbForums[$hForumID].($fCounter != sizeof($heritage)?' > ':'')."\n";
			$fCounter++;
		}
		$curForumInfo = $mysql->query('SELECT title, description, forumType FROM forums WHERE forumID = '.$forumID);
		$curForumInfo = $curForumInfo->fetch();
	}
?>
			
		</div>
		
<?
	if (isset($_GET['edit']) && (($_GET['edit'] == 'general' && !$gameInfo) || (($_GET['edit'] == 'group' || $_GET['edit'] == 'user') && isset($_GET['id']))) || isset($_GET['new']) && in_array($_GET['new'], array('group', 'user'))) {
		$pType = isset($_GET['edit'])?$_GET['edit']:$_GET['new'];
		$typeID = isset($_GET['id'])?intval($_GET['id']):0;
		$updateType = isset($_GET['new'])?'new':'edit';
		if ($updateType == 'edit') {
			$permissions = $mysql->query('SELECT * FROM forums_permissions_'.$pType.($pType != 'general'?'s':'').' WHERE forumID = '.$forumID.($pType != 'general'?" AND {$pType}ID = {$typeID}":''));
			$permissions = $permissions->fetch();
		}
?>
		<h2>Add <?=ucwords($pType)?> Permissions</h2>
		
<? 		if (array_intersect(array_keys($_GET), array('invalidName', 'notInGame'))) { ?>
		<div class="alertBox_error">
<?
		if (isset($_GET['invalidName'])) echo "\t\t\tThat {$pType} was not found\n";
		if (isset($_GET['notInGame'])) echo "\t\t\tThat user is not in this game\n";
?>
		</div>
<? 		} ?>
		
		<form id="newPermission" method="post" action="/forums/process/acp/permissions">
			<input type="hidden" name="forumID" value="<?=$forumID?>">
			<input type="hidden" name="type" value="<?=$pType?>">
<?
		if ($updateType == 'new' && ($pType == 'group' || $pType == 'user')) {
			echo "\t\t\t<div class=\"tr\">\n";
			echo "\t\t\t\t<div class=\"permissionsTitle textLabel\">".($pType == 'group'?'Group name':'Username')."</div>\n";
			echo "\t\t\t\t<div class=\"permissionsType\"><input type=\"text\" name=\"typeName\"></div>\n";
			echo "\t\t\t</div>\n";
		} else {
			if ($pType == 'group' || $pType == 'user') echo "\t\t\t<input type=\"hidden\" name=\"typeID\" value=\"$typeID\">\n";
			foreach ($permissionTypes as $type => $title) { if (!($title == 'Moderate' && $pType == 'general')) {
?>
			<div class="tr">
				<div class="permissionsTitle textLabel"><?=$title?></div>
				<div class="permissionsType">
					<select name="permissions[<?=$type?>]">
						<option value="1"<?=$permissions[$type] == 1?' selected="selected"':''?>>Yes</option>
						<option value="0"<?=$permissions[$type] == 0?' selected="selected"':''?>>Don't Care</option>
						<option value="-1"<?=$permissions[$type] == -1?' selected="selected"':''?>>No</option>
					</select>
				</div>
			</div>
<?
			} }
			if ($pType != 'general') echo "\t\t\t<p>Giving a {$pType} moderator privilages will grant it all other privilages listed here</p>\n";
		}
?>
			<div class="tr"><button type="submit" name="save" class="btn_save" value="<?=$updateType?>"></button></div>
		</form>
		<hr>
<? } ?>
		
<? if (isset($_GET['success'])) { ?>
		<div class="alertBox_success">
			Permission saved
		</div>
		
<? } ?>
		<h2>Set Permissions</h2>
		<h3>General</h3>
		<div class="tr">
			<div class="permissionsTitle">General</div>
			<div class="permissionsLink noFloat"><?=$gameInfo?'&nbsp;':'<a href="/forums/acp/permissions/'.$forumID.'/?edit=general">Edit</a>'?></div>
		</div>
		<br class="clear">
		
<?
	echo "\t\t<h3 class=\"gapAbove\">Groups</h3>\n";
	
	$gPermissions = $mysql->query('SELECT groups.groupID, groups.name, groups.gameGroup FROM forums_permissions_groups AS permissions, forums_groups AS groups WHERE permissions.forumID = '.$forumID.' AND permissions.groupID = groups.groupID');
	if ($gPermissions->rowCount()) { foreach ($gPermissions as $permission) {
?>
		<div class="tr">
			<div class="permissionsTitle"><?=$permission['name'].($gameInfo && $gameInfo['groupID'] == $permission['groupID']?' (Game Group)':'')?></div>
			<div class="permissionsLink<?=$gameInfo?' noFloat':''?>"><a href="/forums/acp/permissions/<?=$forumID?>/?edit=group&id=<?=$permission['groupID']?>">Edit</a></div>
<? if (!$gameInfo) { ?>
			<div class="permissionsLink noFloat"><a href="/forums/acp/permissions/<?=$forumID?>/?delete=group&id=<?=$permission['groupID']?>">Delete</a></div>
<? } ?>
		</div>
<?
	} } else echo "\t\t\t<div class=\"tr\">No group level permissions for this forum.</div>\n";
	if (!$gameInfo) echo "\t\t\t<div class=\"tr\"><a href=\"/forums/acp/permissions/{$forumID}?new=group\">Add Group Permission</a></div>\n";
	
	echo "\t\t<h3 class=\"gapAbove\">User</h3>\n";
	
	$uPermissions = $mysql->query('SELECT users.userID, users.username FROM forums_permissions_users AS permissions, users WHERE permissions.forumID = '.$forumID.' AND permissions.userID = users.userID AND permissions.userID != 1');
	if (sizeof($permissions['user'])) { foreach ($permissions['user'] as $permission) {
?>
		<div class="tr">
			<div class="permissionsTitle"><?=$permission['username']?></div>
			<div class="permissionsLink"><a href="/forums/acp/permissions/<?=$forumID?>/?edit=user&id=<?=$permission['userID']?>">Edit</a></div>
			<div class="permissionsLink noFloat"><a href="/forums/acp/permissions/<?=$forumID?>/?delete=user&id=<?=$permission['userID']?>">Delete</a></div>
		</div>
<?
	} } else echo "\t\t\t<div class=\"tr\">No user level permissions for this forum.</div>\n";
	echo "\t\t\t<div class=\"tr\"><a href=\"/forums/acp/permissions/{$forumID}?new=user\">Add User Permission</a></div>\n";
?>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>