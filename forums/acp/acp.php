<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($pathOptions[1]);
	$redirect = FALSE;
	
	$adminForums = $mysql->query('SELECT forumID FROM forumAdmins WHERE userID = '.$userID);
	$temp = array();
	foreach ($adminForums as $aForumID) $temp[] = $aForumID['forumID'];
	$adminForums = $temp;
	if ($forumID != 0) {
		$forumInfo = $mysql->query('SELECT parentID, heritage FROM forums WHERE forumID = '.$forumID);
		list($parentID, $heritage) = $forumInfo->fetch();
		$heritage = explode('-', $heritage);
		foreach ($heritage as $key => $hForumID) $heritage[$key] = intval($hForumID);
	}
	
	if (!(in_array(0, $adminForums) || array_intersect($adminForums, $heritage))) { header('Location: '.SITEROOT.'/forums/'); exit; }
	
	$gameInfo = $mysql->query('SELECT gameID, groupID FROM games WHERE forumID = '.$forumID);
	$gameInfo = $gameInfo->fetch();
	$gameInfo = $gameInfo?$gameInfo:FALSE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Forum ACP</h1>
		
		<div id="breadcrumbs">
			<a id="forumReturn" href="<?=SITEROOT.'/forums/'.($forumID?$forumID:'')?>">Return to forum</a>
			
<?
	$parentAdmin = 0;
	if (in_array(0, $adminForums)) {
		$parentAdmin = 1;
		echo "\t\t\t<a href=\"".SITEROOT.'/forums/acp/0">Index</a>'.($forumID != 0?' > ':'')."\n";
	} else echo "\t\t\tIndex".($forumID != 0?' > ':'')."\n";
	if ($forumID != 0) {
		$breadcrumbs = $mysql->query('SELECT forumID, title FROM forums WHERE forumID IN ('.implode(',', $heritage).')');
		$breadcrumbForums = array();
		foreach ($breadcrumbs as $forumInfo) $breadcrumbForums[$forumInfo['forumID']] = printReady($forumInfo['title']);
		$fCounter = 1;
		foreach ($heritage as $hForumID) {
			if (!$parentAdmin) $parentAdmin = in_array($hForumID, $adminForums)?1:0;
			if ($parentAdmin) echo "\t\t\t<a href=\"".SITEROOT.'/forums/acp/'.$hForumID.'">'.$breadcrumbForums[$hForumID].'</a>'.($fCounter != sizeof($heritage)?' > ':'')."\n";
			else echo "\t\t\t".$breadcrumbForums[$hForumID].($fCounter != sizeof($heritage)?' > ':'')."\n";
			$fCounter++;
		}
		$curForumInfo = $mysql->query('SELECT title, description, forumType FROM forums WHERE forumID = '.$forumID);
		$curForumInfo = $curForumInfo->fetch();
	}
?>
		</div>
		
		<ul id="acpMenu">
<? if ($forumID != 0) { ?>
			<li<?=$pathOptions[2] == 'details' || !isset($pathOptions[2])?' class="current"':''?>><a id="ml_forumDetails" href="">Forum Details</a></li>
<? } ?>
			<li<?=$forumID == 0 || $pathOptions[2] == 'subforums'?' class="current"':''?>><a id="ml_subforums" href="">Subforums</a></li>
<? if ($forumID != 0) { ?>
			<li<?=$pathOptions[2] == 'permissions'?' class="current"':''?>><a id="ml_permissions" href="">Permissions</a></li>
<? } ?>
		</ul>
		
<? if ($forumID != 0 && !isset($_GET['delete'])) { ?>
		<div id="forumDetails" class="acpContent<?=$pathOptions[2] == 'details' || !isset($pathOptions[2])?' class="current"':''?>">
			<h2>Forum Details</h2>
			<form method="post" action="<?=SITEROOT?>/forums/process/acp/edit">
				<div class="tr">
					<label class="textLabel">Forum title:</label>
					<input type="text" name="title" maxlength="50" value="<?=printReady($curForumInfo['title'])?>"<?=(in_array($forumID, array(1, 2, 3)) || $parentID == 2)?' disabled="disabled"':''?>>
				</div>
<? if ($curForumInfo['forumType'] == 'f') { ?>
				<div class="tr">
					<label class="textLabel">Forum description:</label>
					<textarea name="description"><?=printReady($curForumInfo['description'])?></textarea>
				</div>
<? } ?>
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<button type="submit" name="update" class="btn_update"></button>
			</form>
		</div>
<? } elseif (isset($_GET['delete']) && $_GET['delete'] && !in_array($forumID, array(1, 2, 3)) && $parentID != 2) { ?>
		<div id="forumDetails" class="acpContent">
			<form id="deleteForum" method="post" action="<?=SITEROOT?>/forums/process/acp/delete">
				<p>Are you sure you want to delete this forum?</p>
				<p><b>This action is irreversible!</b></p>
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<button type="submit" name="delete" class="btn_delete"></button>
			</form>
		</div>
<? } ?>
		
		<div id="subforums" class="acpContent<?=$forumID == 0 || $pathOptions[2] == 'subforums'?' class="current"':''?>">
			<h2>Subforums</h2>
			<form id="forums" method="post" action="<?=SITEROOT?>/forums/process/acp/move">
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<div id="forumList">
<?
	$forums = array();
	$forumInfos = $mysql->query('SELECT forumID, title, forumType, parentID FROM forums WHERE parentID = '.$forumID.' ORDER BY `order`');
//	while ($forumInfo = $mysql->fetch()) $forums[] = array('forumID' => $forumInfo['forumID'], 'type' => $forumInfo['forumType'], 'title' => $forumInfo['title']);
	
	$forumCount = 1;
	$numForums = $forumInfos->rowCount();
//	foreach ($forums as $sForumID => $forumInfo) {
	foreach ($forumInfos as $forumInfo) {
		echo "\t\t\t\t\t<div class=\"tr\">\n";
		echo "\t\t\t\t\t\t<div class=\"buttonDiv\">".(($forumCount > 1)?'<input type="image" name="moveUp_'.$forumInfo['forumID'].'" src="'.SITEROOT.'/images/arrow_up.jpg" alt="Up" title="Up">':'&nbsp;')."</div>\n";
		echo "\t\t\t\t\t\t<div class=\"buttonDiv\">".(($forumCount < $numForums && $numForums > 1)?'<input type="image" name="moveDown_'.$forumInfo['forumID'].'" src="'.SITEROOT.'/images/arrow_down.jpg" alt="Down" title="Down">':'&nbsp;')."</div>\n";
		echo "\t\t\t\t\t\t".'<div class="buttonDiv"><a href="'.SITEROOT.'/forums/acp/'.$forumInfo['forumID'].'"><img src="'.SITEROOT.'/images/edit_wheel.jpg" alt="Edit" title="Edit"></a></div>'."\n";
//		echo "\t\t\t\t\t\t".'<div class="buttonDiv">'.(($forumInfo['forumType'] == 'f')?'<a href="'.SITEROOT.'/forums/acp/permissions/'.$forumInfo['forumID'].'"><img src="'.SITEROOT.'/images/permission.jpg" alt="Permissions" title="Permissions"></a>':'&nbsp;')."</div>\n";
		echo "\t\t\t\t\t\t".'<div class="buttonDiv"><a href="'.SITEROOT.'/forums/acp/permissions/'.$forumInfo['forumID'].'"><img src="'.SITEROOT.'/images/permission.jpg" alt="Permissions" title="Permissions"></a>'."</div>\n";
		echo "\t\t\t\t\t\t<div class=\"buttonDiv\">".((!in_array($forumInfo['forumID'], array(1, 2, 3)) && $forumInfo['parentID'] != 2)?'<a href="'.SITEROOT.'/forums/acp/'.$forumInfo['forumID'].'?delete=1"><img src="'.SITEROOT.'/images/cross.jpg"></a>':'&nbsp;')."</div>\n";
		echo "\t\t\t\t\t\t<div class=\"forumNames\"> (".strtoupper($forumInfo['forumType']).') '.$forumInfo['title']."</div>\n";
		echo "\t\t\t\t\t</div>\n";
		$forumCount++;
	}
	if ($forumInfos->rowCount() == 0) echo "\t\t\t\t\t<p>No subforums</p>\n";
//	else echo "\t\t\t\t\t".'<div class="tr"><button type="submit" name="update" class="btn_update"></button></div>'."\n";

?>
				</div>
			</form>
			
			<form id="newForum" method="post" action="<?=SITEROOT?>/forums/process/acp/new">
				<div class="tr">
					<label class="textLabel">New Forum</label>
					<input type="text" name="newForum" maxlength="50">
				</div>
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<button type="submit" name="addForum" class="btn_add"></button>
			</form>
		</div>
		
<? if ($forumID != 0) { ?>
		<div id="permissions" class="acpContent<?=$pathOptions[2] == 'permissions'?' class="current"':''?>">
			<h2>Permissions</h2>
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
		if (isset($_GET['invalidName'])) echo "\t\t\t\tThat {$pType} was not found\n";
		if (isset($_GET['notInGame'])) echo "\t\t\t\tThat user is not in this game\n";
?>
			</div>
<? 		} ?>
			
			<form id="newPermission" method="post" action="<?=SITEROOT?>/forums/process/acp/permissions">
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<input type="hidden" name="type" value="<?=$pType?>">
<?
		if ($updateType == 'new' && ($pType == 'group' || $pType == 'user')) {
			echo "\t\t\t\t<div class=\"tr\">\n";
			echo "\t\t\t\t\t<div class=\"permissionsTitle textLabel\">".($pType == 'group'?'Group name':'Username')."</div>\n";
			echo "\t\t\t\t\t<div class=\"permissionsType\"><input type=\"text\" name=\"typeName\"></div>\n";
			echo "\t\t\t\t</div>\n";
		} else {
			if ($pType == 'group' || $pType == 'user') echo "\t\t\t\t<input type=\"hidden\" name=\"typeID\" value=\"$typeID\">\n";
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
			if ($pType != 'general') echo "\t\t\t\t<p>Giving a {$pType} moderator privilages will grant it all other privilages listed here</p>\n";
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
			<h3>General</h3>
			<div class="tr">
				<div class="permissionsTitle">General</div>
				<div class="permissionsLink noFloat"><?=$gameInfo?'&nbsp;':'<a href="'.SITEROOT.'/forums/acp/permissions/'.$forumID.'/?edit=general">Edit</a>'?></div>
			</div>
			
<?
	echo "\t\t\t<h3 class=\"gapAbove\">Groups</h3>\n";
	
	$gPermissions = $mysql->query('SELECT groups.groupID, groups.name, groups.gameGroup FROM forums_permissions_groups AS permissions, forums_groups AS groups WHERE permissions.forumID = '.$forumID.' AND permissions.groupID = groups.groupID');
	if ($gPermissions->rowCount()) { foreach ($gPermissions as $permission) {
?>
			<div class="tr">
				<div class="permissionsTitle"><?=$permission['name'].($gameInfo && $gameInfo['groupID'] == $permission['groupID']?' (Game Group)':'')?></div>
				<div class="permissionsLink<?=$gameInfo?' noFloat':''?>"><a href="<?=SITEROOT?>/forums/acp/permissions/<?=$forumID?>/?edit=group&id=<?=$permission['groupID']?>">Edit</a></div>
<? if (!$gameInfo) { ?>
				<div class="permissionsLink noFloat"><a href="<?=SITEROOT?>/forums/acp/permissions/<?=$forumID?>/?delete=group&id=<?=$permission['groupID']?>">Delete</a></div>
<? } ?>
			</div>
<?
	} } else echo "\t\t\t\t<div class=\"tr\">No group level permissions for this forum.</div>\n";
	if (!$gameInfo) echo "\t\t\t\t<div class=\"tr\"><a href=\"".SITEROOT."/forums/acp/permissions/{$forumID}?new=group\">Add Group Permission</a></div>\n";
	
	echo "\t\t<h3 class=\"gapAbove\">User</h3>\n";
	
	$uPermissions = $mysql->query('SELECT users.userID, users.username FROM forums_permissions_users AS permissions, users WHERE permissions.forumID = '.$forumID.' AND permissions.userID = users.userID AND permissions.userID != 1');
	if (sizeof($permissions['user'])) { foreach ($permissions['user'] as $permission) {
?>
			<div class="tr">
				<div class="permissionsTitle"><?=$permission['username']?></div>
				<div class="permissionsLink"><a href="<?=SITEROOT?>/forums/acp/permissions/<?=$forumID?>/?edit=user&id=<?=$permission['userID']?>">Edit</a></div>
				<div class="permissionsLink noFloat"><a href="<?=SITEROOT?>/forums/acp/permissions/<?=$forumID?>/?delete=user&id=<?=$permission['userID']?>">Delete</a></div>
			</div>
<?
	} } else echo "\t\t\t\t<div class=\"tr\">No user level permissions for this forum.</div>\n";
	echo "\t\t\t\t<div class=\"tr\"><a href=\"".SITEROOT."/forums/acp/permissions/{$forumID}?new=user\">Add User Permission</a></div>\n";
?>
			</form>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>