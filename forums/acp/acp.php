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
	<a href="" ng-click="getForumDetails(forum.forumID)">{{forum.title}}</a>
	<ul ng-if="forum.children.length">
		<li ng-repeat="forum in forum.children" ng-include="'forumList'"></li>
	</ul>
</li>
</script>
	<div class="forumAcpManagement">
		<div class="sideWidget left"><ul id="forums">
			<li ng-repeat="forum in list" ng-include="'forumList'"></li>
		</ul></div>

		<div class="mainColumn right">
			<div class="clearfix"><div class="trapezoid sectionControls floatLeft" data-ratio=".8">
				<a ng-if="forumID != 0 && details.parentID != 2" href=""  ng-class="{ 'current': currentSection == 'details' }" ng-click="setSection('details')">Details</a
				><a id="ml_subforums" href="" ng-class="{ 'current': currentSection == 'subforums' }" ng-click="setSection('subforums')">Subforums</a
				><a ng-if="forumID != 0" id="ml_permissions" href="" ng-class="{ 'current': currentSection == 'groups' }" ng-click="setSection('groups')">Groups</a
				><a ng-if="details.isGameForum" href="" ng-class="{ 'current': currentSection == 'permissions' }" ng-click="setSection('permissions')">Permissions</a>
			</div></div>
			<h2 class="headerbar hbDark" ng-class="{ 'hb_hasList': currentSection == 'groups' }">
				<span ng-if="forumID != 0" ng-show="currentSection == 'details'" class="section_details">Details</span>
				<span ng-show="currentSection == 'subforums'" class="section_subforums">Subforums</span>
				<span ng-if="details.isGameForum" ng-show="currentSection == 'groups'">Groups</span>
				<span ng-if="forumID != 0" ng-show="currentSection == 'permissions'" class="section_permissions">Permissions</span>
			</h2>

			<form ng-if="forumID != 0" id="details"  ng-class="{ 'currentSection': currentSection == 'details', 'hideSection': currentSection != 'details' }" class="hbMargined" ng-submit="saveDetails()" hb-margined>
				<div ng-show="saveError" class="alertBox_error">
					There was a problem saving the forum details. Make sure you have a title.
				</div>
				<div class="tr">
					<label class="textLabel">Forum title:</label>
					<input type="text" name="title" ng-model="editDetails.title" maxlength="50" ng-disabled="[1, 2, 3].indexOf(forumID) != -1 || details.parentID == 2">
				</div>
				<div ng-if="details.type == 'f'" class="tr">
					<label class="textLabel">Forum description:</label>
					<textarea name="description" ng-model="editDetails.description"></textarea>
				</div>
				<div class="buttonPanel"><button type="submit" name="update" class="fancyButton">Update</button></div>
			</form>

			<div id="subforums"  ng-class="{ 'currentSection': currentSection == 'subforums', 'hideSection': currentSection != 'subforums' }" class="hbMargined" hb-margined>
				<div id="forumList">
					<div ng-repeat="(key, forum) in details.children | orderBy: 'order'" class="tr">
						<div class="buttonDiv"><a ng-if="!$first" href="" ng-click="changeOrder('up', forum)" alt="Up" title="Up" class="sprite upArrow"></a><span ng-if="$first">&nbsp;</span></div>
						<div class="buttonDiv"><a ng-if="!$last" href="" ng-click="changeOrder('down', forum)" alt="Down" title="Down" class="sprite downArrow"></a><span ng-if="$last">&nbsp;</span></div>
						<div class="buttonDiv"><a href="" ng-click="getForumDetails(forum.forumID, 'details')" alt="Edit" title="Edit" class="sprite editWheel"></a></div>
						<div class="buttonDiv"><a href="" ng-click="getForumDetails(forum.forumID, 'permissions')" alt="Permissions" title="Permissions" class="sprite permissions"></a></div>
						<div class="buttonDiv"><a ng-if="[1, 2, 3].indexOf(forum.forumID) == -1 && forumID != 2" href="" ng-click="toggleForumDelete(forum.forumID)" alt="Delete" title="Delete" class="sprite cross"></a></div>
						<div class="forumNames">({{forum.type.toUpperCase()}}) {{forum.title}}</div>
						<div ng-show="showForumDelete == forum.forumID" class="deleteConfirm">
							<p>Are you sure you want to delete <strong>{{forum.title}}</strong>? This cannot be reversed!</p>
							<p>This will delete all threads, posts, and relating content in this forum, as well as in any subforums and the subforums themselves.</p>
							<div class="buttonPanel alignCenter">
								<button type="submit" name="delete" class="fancyButton smallButton" ng-click="confirmForumDelete(forum, key)">Delete</button>
								<button type="submit" name="cancel" class="fancyButton smallButton" ng-click="cancelForumDelete()">Cancel</button>
							</div>
						</div>
					</div>
					<p ng-if="children.details.length == 0">No subforums</p>
				</div>

				<form id="newForum" ng-submit="createForum()">
					<label>New Forum</label>
					<input type="text" name="newForum" ng-model="newForum.name" maxlength="50">
					<button type="submit" name="addForum" class="fancyButton">Add</button>
				</form>
			</div>

			<div ng-if="details.isGameForum"  ng-class="{ 'currentSection': currentSection == 'groups', 'hideSection': currentSection != 'groups' }" class="hbMargined" hb-margined>
				<ul id="groups" class="hbAttachedList">
					<li ng-repeat="(key, group) in details.gameDetails.groups">
						<div ng-show="editingGroup != group.groupID">
							<span class="groupName">{{group.name}}</span> <span ng-if="details.gameDetails.groupID != group.groupID"><a href="" ng-click="editGroup(group.groupID, key)">[ Edit ]</a><a ng-if="confirmGroupDelete != group.groupID" href="" ng-click="deleteGroup(group.groupID)">[ Delete ]</a><span ng-if="confirmGroupDelete == group.groupID"><a href="" ng-click="confirmDelete(group.groupID, key)">[ Confirm ]</a><a href="" ng-click="cancelDelete()" class="cancelDelete">[ Cancel ]</a></span></span> <span ng-if="details.gameDetails.groupID == group.groupID">(Main Group)</span>
						</div>
						<form ng-show="editingGroup == group.groupID" ng-submit="saveGroup(group.groupID, key)">
							<input type="text" ng-model="details.gameDetails.groups[key].name">
							<button type="submit" name="action" value="save" class="action_edit_save sprite check green"></button>
							<button type="submit" name="action" value="cancelEdit" class="action_edit_cancel sprite cross" ng-click="cancelEditing(key)"></button>
						</form>
					</li>
				</ul>

				<form id="newGroup" ng-if="details.gameDetails.groups.length < 5" ng-submit="createGroup()">
					Create a new group: <input type="text" ng-model="newGroup.name"> <button type="submit" class="fancyButton smallButton">Create</button>
				</form>
			</div>

			<div ng-if="forumID != 0" id="permissions" ng-class="{ 'currentSection': currentSection == 'permissions', 'hideSection': currentSection != 'permissions' }" class="hbMargined" hb-margined>
				<div ng-if="!details.isGameForum" id="permissions_general">
					<h3>General</h3>
					<div ng-repeat="permission in [permissions.general]" ng-include="'/angular/templates/forums/acp/permissionSet.html'"></div>
				</div>

				<div id="permissions_groups">
					<h3 class="gapAbove">Groups</h3>
					<div ng-repeat="permission in permissions.group" ng-include="'/angular/templates/forums/acp/permissionSet.html'"></div>
					<form ng-if="details.isGameForum && newGroupPermission.data.length" class="newPermission" ng-submit="addGroupPermission()">
						Add permission for <combobox data="newGroupPermission.data" change="setNewGroup(search, value)" select></combobox> <button type="submit" class="fancyButton smallButton">Add</button>
					</form>
				</div>

				<div id="permissions_users">
					<h3 class="gapAbove">User</h3>
					<div ng-repeat="permission in permissions.user" ng-include="'/angular/templates/forums/acp/permissionSet.html'"></div>
					<div ng-if="details.isGameForum">
						<div ng-repeat="player in details.gameDetails.players | filter: { 'permissionSet': false }">
							{{player.username}} <a href="" ng-click="addUserPermission(player)">[ {{player.permissionSet?'Edit':'Create'}} ]</a>
						</div>
						<p ng-if="details.gameDetails.players.length == 0">
							There are currently no players in this game.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
<?	require_once(FILEROOT.'/footer.php'); ?>
