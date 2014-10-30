<?
	$forumID = intval($pathOptions[1]);
	$redirect = FALSE;

	if (in_array($pathOptions[2], array('details', 'subforums', 'permissions'))) $section = $pathOptions[2];
	elseif ($forumID == 0) $section = 'subforums';
	else $section = 'details';

	$isAdmin = $mysql->query("SELECT f.forumID, p.forumID, fa.forumID FROM forums f, forums p, forumAdmins fa WHERE fa.userID = 1 AND fa.forumID = p.forumID AND f.heritage LIKE CONCAT(p.heritage, '%') AND f.forumID = $forumID");
	if (!$isAdmin->rowCount()) { header('Location: /forums/'); exit; }
	$forumInfo = $mysql->query("SELECT forumID, title, forumType, heritage FROM forums WHERE forumID = $forumID");
	$forumInfo = $forumInfo->fetch();
	$adminForums = $mysql->query("SELECT forumID, title, heritage, `order`, MAX(adminForum) adminForum FROM (SELECT p.forumID, p.title, p.heritage, p.order, 0 adminForum FROM forumAdmins fa, forums f, forums p WHERE f.heritage LIKE CONCAT(p.heritage, '%') AND fa.forumID = f.forumID AND fa.userID = {$currentUser->userID} UNION SELECT c.forumID, c.title, c.heritage, c.order, 1 adminForum FROM forumAdmins fa, forums f, forums c WHERE c.heritage LIKE CONCAT(f.heritage, '%') AND fa.forumID = f.forumID AND fa.userID = {$currentUser->userID}) adminForums GROUP BY forumID ORDER BY LENGTH(heritage), `order`");
	$forums = buildForumStructure($adminForums->fetchAll());
	
	$gameInfo = $mysql->query('SELECT gameID, groupID FROM games WHERE forumID = '.$forumID);
	$gameInfo = $gameInfo->fetch();
	$gameInfo = $gameInfo?$gameInfo:FALSE;
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Forum ACP - <?=$forumInfo['title']?></h1>
		
		<div id="topLinks">
			<a href="<?='/forums/'.($forumID?$forumID:'')?>">Return to forum</a>
		</div>
		
		<div class="sideWidget left"><ul id="forums">
<?
	function displayForums($forums, $tabs = 3) {
		global $forumID;
		foreach ($forums as $forum) {
			$classes = array();
			if ($forum['info']['adminForum']) $classes[] = 'adminLink';
			if ($forum['info']['forumID'] == $forumID) $classes[] = 'currentForum';
			echo str_repeat("\t", $tabs)."<li".(sizeof($classes)?' class="'.implode(' ', $classes).'"':'').">\n";
			if ($forum['info']['adminForum']) echo str_repeat("\t", $tabs + 1)."<a href=\"/forums/acp/{$forum['info']['forumID']}\">{$forum['info']['title']}</a>\n";
			else echo str_repeat("\t", $tabs + 1)."<div>{$forum['info']['title']}</div>\n";
			if (sizeof($forum['children'])) {
				echo str_repeat("\t", $tabs + 1)."<ul>\n";
				displayForums($forum['children'], $tabs + 2);
				echo str_repeat("\t", $tabs + 1)."</ul>\n";
			}
			echo str_repeat("\t", $tabs)."</li>\n";
		}
	}
	displayForums($forums);
?>
		</ul></div>

		<div class="mainColumn right">
			<div class="clearfix"><div id="controls" class="wingDiv hbDark floatLeft" data-ratio=".8">
				<div>
<?	if ($forumID != 0) { ?>
				<a id="ml_forumDetails" href="" class="section_details<?=$section == 'details'?' current':''?>">Details</a>
<?	} ?>
				<a id="ml_subforums" href="" class="section_subforums<?=$section == 'subforums'?' current':''?>">Subforums</a>
<?	if ($forumID != 0) { ?>
				<a id="ml_permissions" href="" class="section_permissions<?=$section == 'permissions'?' current':''?>">Permissions</a>
<?	} ?>
				</div>
				<div class="wing dlWing"></div>
				<div class="wing drWing"></div>
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
			<form id="details" method="post" action="/forums/process/acp/edit" class="acpContent hbdMargined section_details<?=$pathOptions[2] == 'details' || !isset($pathOptions[2])?' current':''?>">
				<div class="tr">
					<label class="textLabel">Forum title:</label>
					<input type="text" name="title" maxlength="50" value="<?=printReady($forumInfo['title'])?>"<?=(in_array($forumID, array(1, 2, 3)) || $parentID == 2)?' disabled="disabled"':''?>>
				</div>
<?		if ($forumInfo['forumType'] == 'f') { ?>
				<div class="tr">
					<label class="textLabel">Forum description:</label>
					<textarea name="description"><?=printReady($curForumInfo['description'])?></textarea>
				</div>
<?		} ?>
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<div class="buttonPanel"><button type="submit" name="update" class="fancyButton">Update</button></div>
			</form>
<?	} ?>
			
			<form id="subforums" method="post" action="/forums/process/acp/subforums" class="acpContent hbdMargined section_subforums<?=$forumID == 0 || $pathOptions[2] == 'subforums'?' current':''?>">
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<div id="forumList">
<?
	$forums = array();
	$forumInfos = $mysql->query('SELECT forumID, title, forumType, parentID FROM forums WHERE parentID = '.$forumID.' ORDER BY `order`');
	
	$forumCount = 1;
	$numForums = $forumInfos->rowCount();
	foreach ($forumInfos as $forumInfo) {
?>
					<div class="tr">
						<div class="buttonDiv"><?=($forumCount > 1)?'<input type="image" name="moveUp_'.$forumInfo['forumID'].'" alt="Up" title="Up" class="sprite upArrow">':'&nbsp;'?></div>
						<div class="buttonDiv"><?=($forumCount < $numForums && $numForums > 1)?'<input type="image" name="moveDown_'.$forumInfo['forumID'].'" alt="Down" title="Down" class="sprite downArrow">':'&nbsp;'?></div>
						<div class="buttonDiv"><a href="/forums/acp/<?=$forumInfo['forumID']?>" alt="Edit" title="Edit" class="sprite editWheel"></a></div>
						<div class="buttonDiv"><a href="/forums/acp/<?=$forumInfo['forumID']?>/permissions" alt="Permissions" title="Permissions" class="sprite permissions"></a></div>
						<div class="buttonDiv"><?=(!in_array($forumInfo['forumID'], array(1, 2, 3)) && $forumInfo['parentID'] != 2)?'<a href="/forums/acp/'.$forumInfo['forumID'].'/deleteForum/" alt="Delete" title="Delete" class="sprite cross"></a>':'&nbsp;'?></div>
						<div class="forumNames"> (<?=strtoupper($forumInfo['forumType'])?>) <?=$forumInfo['title']?></div>
					</div>
<?
		$forumCount++;
	}
	if ($forumInfos->rowCount() == 0) echo "\t\t\t\t\t<p>No subforums</p>\n";
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
			<form id="permissions" method="post" action="/forums/process/acp/permissions" class="acpContent hbdMargined section_permissions<?=$section == 'permissions'?' current':''?>">
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
			
<?		if (array_intersect(array_keys($_GET), array('invalidName', 'notInGame'))) { ?>
				<div class="alertBox_error">
<?
		if (isset($_GET['invalidName'])) echo "\t\t\t\tThat {$pType} was not found\n";
		if (isset($_GET['notInGame'])) echo "\t\t\t\tThat user is not in this game\n";
?>
				</div>
<?			} ?>
				
				<form id="newPermission" method="post" action="/forums/process/acp/permissions">
					<input type="hidden" name="forumID" value="<?=$forumID?>">
					<input type="hidden" name="type" value="<?=$pType?>">
<?
		if ($updateType == 'new' && ($pType == 'group' || $pType == 'user')) {
			echo "\t\t\t\t\t<div class=\"tr\">\n";
			echo "\t\t\t\t\t\t<div class=\"permissions_label textLabel\">".($pType == 'group'?'Group name':'Username')."</div>\n";
			echo "\t\t\t\t\t\t<div class=\"permissionsType\"><input type=\"text\" name=\"typeName\"></div>\n";
			echo "\t\t\t\t\t</div>\n";
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
	$permissions = $mysql->query("SELECT `read`, `write`, editPost, deletePost, createThread, deleteThread, addPoll, addRolls, addDraws, moderate FROM forums_permissions_general WHERE forumID = {$forumID}");
	$permissions = $permissions->fetch();
	if (!$gameInfo) permissionSet('general', 'General', $permissions);
?>
				</div>
				
				<div id="permissions_groups">
					<h3 class="gapAbove">Groups</h3>
<?
	$gPermissions = $mysql->query('SELECT g.groupID, g.name, g.gameGroup, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addPoll, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_groups p, forums_groups g WHERE p.forumID = '.$forumID.' AND p.groupID = g.groupID');
	if ($gPermissions->rowCount()) { foreach ($gPermissions as $permission) {
		permissionSet('group', $permission['name'], $permission, $forumID, $permission['groupID'], $permission['gameGroup']);
	} } else echo "\t\t\t\t\t<div class=\"tr\">No group level permissions for this forum.</div>\n";
?>
					<div class="tr"><a href="/forums/acp/<?=$forumID?>/newPermission/group" class="newPermission">Add Group Permission</a></div>
				</div>
				
				<div id="permissions_users">
					<h3 class="gapAbove">User</h3>
	
<?
	$uPermissions = $mysql->query("SELECT u.userID, u.username, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addPoll, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_users p, users u WHERE p.forumID = {$forumID} AND p.userID = u.userID");
	if ($uPermissions->rowCount()) { foreach ($uPermissions as $permission) {
		permissionSet('user', $permission['username'], $permission, $forumID, $permission['userID']);
	} } else echo "\t\t\t\t\t<div class=\"tr\">No user level permissions for this forum.</div>\n";
?>
					<div class="tr"><a href="/forums/acp/<?=$forumID?>/newPermission/user" class="newPermission">Add User Permission</a></div>
				</div>
				<input type="hidden" name="forumID" value="<?=$forumID?>">
				<div class="tr alignCenter"><button type="submit" name="save" class="fancyButton" value="<?=$updateType?>">Save</button></div>
			</form>
		</div>
<?	} ?>
<?	require_once(FILEROOT.'/footer.php'); ?>
