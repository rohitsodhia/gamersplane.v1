<?
	class forumACP {
		function __construct() {
			global $loggedIn, $pathOptions;
			if (!$loggedIn) 
				displayJSON(array('failed' => true, 'errors' => array('loginRequired')), true);

			if ($pathOptions[1] == 'details' && isset($_POST['forumID'])) 
				$this->getDetails(intval($_POST['forumID']), isset($_POST['full'])?true:false);
			elseif ($pathOptions[1] == 'savePermission' && isset($_POST['forumID'])) 
				$this->savePermission(intval($_POST['forumID']), $_POST['permission']);
			elseif ($pathOptions[1] == 'createGroup' && isset($_POST['forumID'], $_POST['name'])) 
				$this->createGroup(intval($_POST['forumID']), $_POST['name']);
			elseif ($pathOptions[1] == 'editGroup' && isset($_POST['groupID'], $_POST['name'])) 
				$this->editGroup(intval($_POST['groupID']), $_POST['name']);
			elseif ($pathOptions[1] == 'deleteGroup' && isset($_POST['groupID'])) 
				$this->deleteGroup(intval($_POST['groupID']));
			else 
				displayJSON(array('failed' => true));
		}

		public function getDetails($forumID, $full = false) {
			global $currentUser, $mysql;

			$forumManager = new ForumManager(0, ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')), true);

			$list = $forumManager->getAdminForums(0, $forumID);
			$details = array(
				'forumID' => (int) $forumID,
				'isGameForum' => (bool) $forum->isGameForum(),
				'gameDetails' => null,
				'title' => $forum->getTitle(true),
				'description' => $forum->getDescription(),
				'type' => $forum->getType(),
				'parentID' => $forum->getParentID(),
				'children' => array()
			);
			if ($details['isGameForum']) {
				$gameForumID = $forum->heritage[2];
				$gameDetails = $mysql->query("SELECT gameID, groupID FROM games WHERE forumID = {$gameForumID}")->fetch();
				$groups = $mysql->query("SELECT groupID, name FROM forums_groups WHERE gameID = {$gameDetails['gameID']}")->fetchAll();
				foreach ($groups as &$group) 
					$group['groupID'] = (int) $group['groupID'];
				$players = $mysql->query("SELECT u.userID, u.username FROM players p INNER JOIN users u ON p.userID = u.userID WHERE p.gameID = {$gameDetails['gameID']} AND p.approved = 1 AND p.isGM = 0")->fetchAll();
				foreach ($players as &$player) 	
					$player['userID'] = (int) $player['userID'];
				$details['gameDetails'] = array('forumID' => $gameForumID, 'groupID' => (int) $gameDetails['groupID'], 'groups' => $groups, 'players' => $players);
			}
			if (sizeof($forum->getChildren())) { foreach ($forum->getChildren() as $order => $childID) {
				$cForum = $forumManager->forums[$childID];
				$details['children'][] = array(
					'forumID' => (int) $childID,
					'order' => (int) $order,
					'type' => $cForum->getType(),
					'title' => $cForum->getTitle(true)
				);
			} }
			$permissions = array('general' => array(), 'group' => array(), 'user' => array());
			if (!$details['isGameForum']) 
				$permissions['general'] = $this->castPermissions($mysql->query("SELECT 'general' as `type`, `read`, `write`, editPost, deletePost, createThread, deleteThread, addRolls, addDraws, moderate FROM forums_permissions_general WHERE forumID = {$forumID}")->fetch());
			if (!$details['isGameForum'] && !sizeof($permissions['general'])) {
				$permissions['general'] = array('type' => 'general');
				global $permissionTypes;
				foreach ($permissionTypes as $key => $value) 
					$permissions['general'][$key] = 0;
			}
			$permissions['general']['ref'] = 'general';
			$permissions['group'] = $mysql->query("SELECT 'group' as `type`, g.groupID as id, g.name, IF(g.gameID IS NULL, 0, 1) gameGroup, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_groups p INNER JOIN forums_groups g ON p.groupID = g.groupID WHERE p.forumID = {$forumID}")->fetchAll();
			$pGroups = array();
			foreach ($permissions['group'] as $key => $permission) {
				$permissions['group'][$key] = $this->castPermissions($permission, 2);
				$permissions['group'][$key]['ref'] = 'group_'.$permission['id'];
				$pGroups[] = $permission['id'];
			}
			foreach ($details['gameDetails']['groups'] as &$group) 
				$group['permissionSet'] = in_array($group['groupID'], $pGroups)?true:false;
			$permissions['user'] = $mysql->query("SELECT 'user' as `type`, u.userID as id, u.username, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_users p INNER JOIN users u ON p.userID = u.userID WHERE p.forumID = {$forumID}")->fetchAll();
			$pUsers = array();
			foreach ($permissions['user'] as $key => $permission) {
				$permissions['user'][$key] = $this->castPermissions($permission, 4);
				$permissions['user'][$key]['ref'] = 'user_'.$permission['id'];
				$pUsers[] = $permission['id'];
			}
			foreach ($details['gameDetails']['players'] as &$player) 
				$player['permissionSet'] = in_array($player['userID'], $pUsers)?true:false;

			displayJSON(array('list' => array($list), 'details' => $details, 'permissions' => $permissions));
		}

		private function castPermissions($permissions, $divideBy = 1) {
			foreach ($permissions as $key => &$value) {
				if (!in_array($key, array('type', 'id', 'name', 'gameGroup', 'username')))
					$value = (int) $value / $divideBy;
				elseif (in_array($key, array('id'))) 
					$value = (int) $value;
				elseif ($key == 'gameGroup') 
					$value = (bool) $value;
			}
			return $permissions;
		}

		public function savePermission($forumID, $permission) {
			global $currentUser, $mysql, $permissionTypes;

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')), true);

			$multiplier = 1;
			if ($permission->type == 'group') 
				$multiplier = 2;
			elseif ($permission->type == 'user') 
				$multiplier = 4;

			$pFields = array();
			foreach ($permissionTypes as $field => $label) 
				if ($permission->type != 'general' || $field != 'moderate') 
					$pFields[] = "`{$field}` = ".(intval($permission->$field) * $multiplier);
			$query = "UPDATE forums_permissions_{$permission->type}".($permission->type != 'general'?'s':'')." SET ".implode(', ', $pFields)." WHERE forumID = {$forumID}".($permission->type != 'general'?" AND {$permission->type}ID = {$permission->id}":'');
			$update = $mysql->query($query);
			if (is_int($update->rowCount())) 
				displayJSON(array('success' => true));
			else 
				displayJSON(array('failed' => true));
		}

		public function createGroup($forumID, $name) {
			global $currentUser, $mysql;

			if (strlen($name) < 3) 
				displayJSON(array('failed' => true, 'errors' => array('noName')), true);

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')), true);

			if ($forum->isGameForum()) {
				$groupCount = $mysql->query("SELECT COUNT(groupID) FROM forums_groups WHERE gameID = {$forum->gameID}")->fetchColumn();
				if ($groupCount >= 5) 
					displayJSON(array('failed' => true, 'errors' => array('tooManyGroups')), true);

				$addGroup = $mysql->prepare("INSERT INTO forums_groups SET name = :name, ownerID = {$currentUser->userID}, gameID = {$forum->gameID}");
				$addGroup->execute(array(':name' => $name));
				$groupID = $mysql->lastInsertId();
				$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$groupID}, userID = {$currentUser->userID}");

				displayJSON(array('success' => true, 'groupID' => $groupID));
			}
		}

		public function editGroup($groupID, $name) {
			global $currentUser, $mysql;

			list($gName, $forumID, $mGroupID) = $mysql->query("SELECT f.name, g.forumID, g.groupID FROM forums_groups f INNER JOIN games g ON f.gameID = g.gameID WHERE f.groupID = {$groupID}")->fetch(PDO::FETCH_NUM);
			if ($mGroupID == $groupID) 
				displayJSON(array('failed' => true, 'errors' => array('mainGroup')), true);
			if (strlen($name) < 3) 
				displayJSON(array('failed' => true, 'errors' => array('noName')), true);

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')), true);

			if ($forum->isGameForum()) {
				$updateName = $mysql->prepare("UPDATE forums_groups SET name = :name WHERE groupID = {$groupID}");
				$updateName->execute(array(':name' => $name));
				if ($updateName->rowCount()) 
					displayJSON(array('success' => true, 'updated' => true, 'name' => $name));
				else 
					displayJSON(array('failed' => true, 'queryFailed' => true));
			}
		}

		public function deleteGroup($groupID) {
			global $currentUser, $mysql;

			list($forumID, $mGroupID) = $mysql->query("SELECT g.forumID, g.groupID FROM forums_groups f INNER JOIN games g ON f.gameID = g.gameID WHERE f.groupID = {$groupID}")->fetch(PDO::FETCH_NUM);
			if ($mGroupID == $groupID) 
				displayJSON(array('failed' => true, 'errors' => array('mainGroup')), true);

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')), true);

			if ($forum->isGameForum()) {
				$mysql->query("DELETE g, m, p FROM forums_groups g INNER JOIN forums_groupMemberships m ON g.groupID = m.groupID LEFT JOIN forums_permissions_groups p ON g.groupID = p.groupID WHERE g.groupID = {$groupID}");
				displayJSON(array('success' => true, 'deleted' => true));
			}
		}
	}
?>