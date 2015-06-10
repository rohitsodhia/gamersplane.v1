<?
	addPackage('forum');

	$forumID = intval($pathOptions[1]);

/*	if (in_array($pathOptions[2], array('details', 'subforums', 'permissions'))) 
		$section = $pathOptions[2];
	elseif ($forumID == 0) 
		$section = 'subforums';
	else 
		$section = 'details';

	$forumManager = new ForumManager(0, ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
	$forum = $forumManager->forums[$forumID];
	if (!$forum->getPermissions('admin')) { header('Location: /forums/'); exit; }
	*/
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Forum ACP - {{details.title}}</h1>

		<div id="topLinks">
			<a href="/forums/{{forumID}}{{forumID != 0?'/':''}}">Return to forum</a>
		</div>

<script type="text/ng-template" id="forumList">
<li ng-class="{ 'notAdmin': !forum.admin, 'currentForum': forum.forumID == forumID }">
	<a href="">{{forum.title}}</a>
	<ul ng-if="forum.children.length">
		<li ng-repeat="forum in forum.children" ng-include="'forumList'"></li>
	</ul>
</li>
</script>
		<div class="sideWidget left"><ul id="forums">
			<li ng-repeat="forum in list" ng-include="'forumList'"></li>
		</ul></div>

		<div class="mainColumn right">
			<div class="clearfix"><div id="controls" class="trapezoid floatLeft" data-ratio=".8">
				<div>
					<a ng-if="forumID != 0" id="ml_forumDetails" href="" class="section_details" ng-class="{ 'current': currentSection == 'details' }" ng-click="setSection('details')">Details</a>
					<a id="ml_subforums" href="" class="section_subforums" ng-class="{ 'current': currentSection == 'subforums' }" ng-click="setSection('subforums')">Subforums</a>
					<a ng-if="forumID != 0" id="ml_permissions" href="" class="section_permissions" ng-class="{ 'current': currentSection == 'permissions' }" ng-click="setSection('permissions')">Permissions</a>
				</div>
			</div></div>
			<h2 class="headerbar hbDark">
				<span ng-if="forumID != 0" ng-show="currentSection == 'details'" class="section_details">Details</span>
				<span ng-show="currentSection == 'subforums'" class="section_subforums">Subforums</span>
				<span ng-if="forumID != 0" ng-show="currentSection == 'permissions'" class="section_permissions">Permissions</span>
			</h2>

			<form ng-if="forumID != 0" id="details" ng-show="currentSection == 'details'" class="acpContent hbdMargined section_details" ng-submit="saveDetails()">
				<div class="tr">
					<label class="textLabel">Forum title:</label>
					<input type="text" name="title" ng-model="details.title" maxlength="50" ng-disabled="[1, 2, 3].indexOf(forumID) != -1 || details.parentID == 2">
				</div>
				<div ng-if="details.type == 'f'" class="tr">
					<label class="textLabel">Forum description:</label>
					<textarea name="description" ng-model="details.description"></textarea>
				</div>
				<input type="hidden" name="forumID" value="{{forumID}}">
				<div class="buttonPanel"><button type="submit" name="update" class="fancyButton" skew-element>Update</button></div>
			</form>

			<form id="subforums" ng-show="currentSection == 'subforums'" class="acpContent hbdMargined section_subforums" ng-submit="saveSubforums()">
				<div id="forumList">
					<div ng-repeat="forum in details.children" class="tr">
						<div class="buttonDiv"><input ng-if="!$first" type="image" name="moveUp_{{forum.forumID}}" alt="Up" title="Up" class="sprite upArrow"><span ng-if="$first">&nbsp;</span></div>
						<div class="buttonDiv"><input ng-if="!$last" type="image" name="moveDown_{{forum.forumID}}" alt="Down" title="Down" class="sprite downArrow"><span ng-if="$last">&nbsp;</span></div>
						<div class="buttonDiv"><a href="/forums/acp/{{forum.forumID}}/" alt="Edit" title="Edit" class="sprite editWheel"></a></div>
						<div class="buttonDiv"><a href="/forums/acp/{{forum.forumID}}/permissions/" alt="Permissions" title="Permissions" class="sprite permissions"></a></div>
						<div class="buttonDiv"><a ng-if="[1, 2, 3].indexOf(forum.forumID) == -1 && forumID != 2" href="/forums/acp/{{forum.forumID}}/deleteForum/" alt="Delete" title="Delete" class="sprite cross"></a></div>
						<div class="forumNames">({{forum.type.toUpperCase()}}) {{forum.title}}</div>
					</div>
					<p ng-if="children.details.length == 0">No subforums</p>
				</div>

				<div id="newForum">
					<div class="tr">
						<label class="textLabel">New Forum</label>
						<input type="text" name="newForum" maxlength="50">
					</div>
					<input type="hidden" name="forumID" value="{{forumID}}">
					<div class="buttonPanel"><button type="submit" name="addForum" class="fancyButton" skew-element>Add</button></div>
				</div>
			</form>
<? /*

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
<? */ ?>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>
