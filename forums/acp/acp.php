<?
	addPackage('forum');

	$forumID = intval($pathOptions[1]);

	if (in_array($pathOptions[2], array('details', 'subforums', 'permissions'))) 
		$section = $pathOptions[2];
	elseif ($forumID == 0) 
		$section = 'subforums';
	else 
		$section = 'details';

	$forumManager = new ForumManager(0, ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
	$forum = $forumManager->forums[$forumID];
	if (!$forum->getPermissions('admin')) { header('Location: /forums/'); exit; }
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Forum ACP - <?=$forumManager->getForumProperty($forumID, 'title')?></h1>
		
		<div id="topLinks">
			<a href="<?='/forums/'.($forumID?$forumID:'')?>/">Return to forum</a>
		</div>
		
		<div class="sideWidget left"><ul id="forums">
<?	$forumManager->displayAdminSidelist(0, $forumID); ?>
		</ul></div>

		<div class="mainColumn right">
			<div class="clearfix"><div id="controls" class="trapezoid floatLeft" data-ratio=".8">
				<div>
<?	if ($forumID != 0) { ?>
				<a id="ml_forumDetails" href="" class="section_details<?=$section == 'details'?' current':''?>">Details</a>
<?	} ?>
				<a id="ml_subforums" href="" class="section_subforums<?=$section == 'subforums'?' current':''?>">Subforums</a>
<?	if ($forumID != 0) { ?>
				<a id="ml_permissions" href="" class="section_permissions<?=$section == 'permissions'?' current':''?>">Permissions</a>
<?	} ?>
				</div>
			</div></div>
			<h2 class="headerbar hbDark">
<?	if ($forumID != 0) { ?>
				<span class="section_details<?=$section != 'details'?' hideDiv':''?>">Details</span>
<?	} ?>
				<span class="section_subforums<?=$section != 'subforums'?' hideDiv':''?>">Subforums</span>
<?	if ($forumID != 0) { ?>
				<span class="section_permissions<?=$section != 'permissions'?' hideDiv':''?>">Permissions</span>
<?	} ?>
			</h2>
			
<?	if ($forumID != 0) { ?>
			<form id="details" method="post" action="/forums/process/acp/edit/" class="acpContent hbdMargined section_details<?=$pathOptions[2] == 'details' || !isset($pathOptions[2])?' current':''?>">
				<div class="tr">
					<label class="textLabel">Forum title:</label>
					<input type="text" name="title" maxlength="50" value="<?=$forum->getTitle(true)?>"<?=in_array($forumID, array(1, 2, 3)) || $forum->getParentID() == 2?' disabled="disabled"':''?>>
				</div>
<?		if ($forum->getType() == 'f') { ?>
				<div class="tr">
					<label class="textLabel">Forum description:</label>
					<textarea name="description"><?=$forum->getDescription(true)?></textarea>
				</div>
<?		} ?>
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<div class="buttonPanel"><button type="submit" name="update" class="fancyButton">Update</button></div>
			</form>
<?	} ?>
			
			<form id="subforums" method="post" action="/forums/process/acp/subforums/" class="acpContent hbdMargined section_subforums<?=$forumID == 0 || $pathOptions[2] == 'subforums'?' current':''?>">
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<div id="forumList">
<?
	foreach ($forum->getChildren() as $order => $childID) {
		$cForum = $forumManager->forums[$childID];
?>
					<div class="tr">
						<div class="buttonDiv"><?=($order > 1)?'<input type="image" name="moveUp_'.$childID.'" alt="Up" title="Up" class="sprite upArrow">':'&nbsp;'?></div>
						<div class="buttonDiv"><?=($order < sizeof($forum->getChildren()) && sizeof($forum->getChildren()) > 1)?'<input type="image" name="moveDown_'.$childID.'" alt="Down" title="Down" class="sprite downArrow">':'&nbsp;'?></div>
						<div class="buttonDiv"><a href="/forums/acp/<?=$childID?>/" alt="Edit" title="Edit" class="sprite editWheel"></a></div>
						<div class="buttonDiv"><a href="/forums/acp/<?=$childID?>/permissions/" alt="Permissions" title="Permissions" class="sprite permissions"></a></div>
						<div class="buttonDiv"><?=!in_array($childID, array(1, 2, 3)) && $cForum->getParentID() != 2?'<a href="/forums/acp/'.$childID.'/deleteForum/" alt="Delete" title="Delete" class="sprite cross"></a>':'&nbsp;'?></div>
						<div class="forumNames">(<?=strtoupper($cForum->getType())?>) <?=$cForum->getTitle(true)?></div>
					</div>
<?
	}
	if (sizeof($forum->getChildren()) == 0) echo "\t\t\t\t\t<p>No subforums</p>\n";
?>
				</div>

				<div id="newForum">
					<div class="tr">
						<label class="textLabel">New Forum</label>
						<input type="text" name="newForum" maxlength="50">
					</div>
					<input type="hidden" name="forumID" value="<?=$forumID?>">
					<div class="buttonPanel"><button type="submit" name="addForum" class="fancyButton">Add</button></div>
				</div>
			</form>

<?	if ($forumID != 0) { ?>
			<form id="permissions" method="post" action="/forums/process/acp/permissions/" class="acpContent hbdMargined section_permissions<?=$section == 'permissions'?' current':''?>">
<?
	if (isset($_GET['edit']) && (($_GET['edit'] == 'general' && !$forum->isGameForum()) || (($_GET['edit'] == 'group' || $_GET['edit'] == 'user') && isset($_GET['id']))) || isset($_GET['new']) && in_array($_GET['new'], array('group', 'user'))) {
		$pType = isset($_GET['edit'])?$_GET['edit']:$_GET['new'];
		$typeID = isset($_GET['id'])?intval($_GET['id']):0;
		$updateType = isset($_GET['new'])?'new':'edit';
		if ($updateType == 'edit') {
			$permissions = $mysql->query('SELECT * FROM forums_permissions_'.$pType.($pType != 'general'?'s':'').' WHERE forumID = '.$forumID.($pType != 'general'?" AND {$pType}ID = {$typeID}":''));
			$permissions = $permissions->fetch();
		}
?>
				<h2>Add <?=ucwords($pType)?> Permissions</h2>
			
<?		if (array_intersect(array_keys($_GET), array('invalidName', 'notInGame'))) { ?>
				<div class="alertBox_error">
<?
			if (isset($_GET['invalidName'])) echo "\t\t\t\tThat {$pType} was not found\n";
			if (isset($_GET['notInGame'])) echo "\t\t\t\tThat user is not in this game\n";
?>
				</div>
<?		} ?>
				
				<form id="newPermission" method="post" action="/forums/process/acp/permissions/">
					<input type="hidden" name="forumID" value="<?=$forumID?>">
					<input type="hidden" name="type" value="<?=$pType?>">
<?		if ($updateType == 'new' && ($pType == 'group' || $pType == 'user')) { ?>
					<div class="tr">
						<div class="permissions_label textLabel"><?=$pType == 'group'?'Group name':'Username'?></div>
						<div class="permissionsType"><input type="text" name="typeName"></div>
					</div>
<?
		} else {
			if ($pType == 'group' || $pType == 'user') echo "\t\t\t\t\t<input type=\"hidden\" name=\"typeID\" value=\"$typeID\">\n";
			foreach ($permissionTypes as $type => $title) { if (!($title == 'Moderate' && $pType == 'general')) {
?>
					<div class="tr">
						<div class="permissions_label textLabel"><?=$title?></div>
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
		}
?>
				<hr>
<?	} ?>
				
<?	if (isset($_GET['success'])) { ?>
				<div class="alertBox_success">
					Permission saved
				</div>
				
<?	} ?>
				<div id="permissions_general">
					<h3>General</h3>
<?
	$permissions = $mysql->query("SELECT `read`, `write`, editPost, deletePost, createThread, deleteThread, addRolls, addDraws, moderate FROM forums_permissions_general WHERE forumID = {$forumID}");
	$permissions = $permissions->fetch();
	if (!$forum->isGameForum()) permissionSet('general', 'General', $permissions);
?>
				</div>
				
				<div id="permissions_groups">
					<h3 class="gapAbove">Groups</h3>
<?
	$gPermissions = $mysql->query('SELECT g.groupID, g.name, g.gameGroup, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_groups p, forums_groups g WHERE p.forumID = '.$forumID.' AND p.groupID = g.groupID');
	if ($gPermissions->rowCount()) { foreach ($gPermissions as $permission) {
		permissionSet('group', $permission['name'], $permission, $forumID, $permission['groupID'], $permission['gameGroup']);
	} } else echo "\t\t\t\t\t<div class=\"tr\">No group level permissions for this forum.</div>\n";
	if (!$forum->isGameForum()) {
?>
					<div class="tr"><a href="/forums/acp/<?=$forumID?>/newPermission/group" class="newPermission">Add Group Permission</a></div>
<?	} ?>
				</div>
				
				<div id="permissions_users">
					<h3 class="gapAbove">User</h3>
	
<?
	$uPermissions = $mysql->query("SELECT u.userID, u.username, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_users p, users u WHERE p.forumID = {$forumID} AND p.userID = u.userID");
	if ($uPermissions->rowCount()) { foreach ($uPermissions as $permission) {
		permissionSet('user', $permission['username'], $permission, $forumID, $permission['userID']);
	} } else echo "\t\t\t\t\t<div class=\"tr\">No user level permissions for this forum.</div>\n";
	if (!$forum->isGameForum()) {
?>
					<div class="tr"><a href="/forums/acp/<?=$forumID?>/newPermission/user" class="newPermission">Add User Permission</a></div>
<?	} ?>
				</div>
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<div class="tr alignCenter"><button type="submit" name="save" class="fancyButton" value="<?=$updateType?>">Save</button></div>
			</form>
		</div>
<?	} ?>
<?	require_once(FILEROOT.'/footer.php'); ?>
