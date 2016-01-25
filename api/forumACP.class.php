<?
	class forumACP {
		function __construct() {
			global $loggedIn, $pathOptions;
			if (!$loggedIn) 
				displayJSON(array('failed' => true, 'errors' => array('loginRequired')));

			if ($pathOptions[1] == 'details' && isset($_POST['forumID'])) 
				$this->getDetails(intval($_POST['forumID']), isset($_POST['full'])?true:false);
			elseif ($pathOptions[1] == 'updateForum' && isset($_POST['forumID'])) 
				$this->updateForum(intval($_POST['forumID']), isset($_POST['full'])?true:false);
			elseif ($pathOptions[1] == 'changeOrder' && isset($_POST['forumID'], $_POST['direction'])) 
				$this->changeOrder(intval($_POST['forumID']), $_POST['direction']);
			elseif ($pathOptions[1] == 'deleteForum' && isset($_POST['forumID'])) 
				$this->deleteForum(intval($_POST['forumID']));
			elseif ($pathOptions[1] == 'createForum' && isset($_POST['parentID'], $_POST['name'])) 
				$this->createForum((int) $_POST['parentID'], $_POST['name']);
			elseif ($pathOptions[1] == 'createGroup' && isset($_POST['forumID'], $_POST['name'])) 
				$this->createGroup(intval($_POST['forumID']), $_POST['name']);
			elseif ($pathOptions[1] == 'editGroup' && isset($_POST['groupID'], $_POST['name'])) 
				$this->editGroup(intval($_POST['groupID']), $_POST['name']);
			elseif ($pathOptions[1] == 'deleteGroup' && isset($_POST['groupID'])) 
				$this->deleteGroup(intval($_POST['groupID']));
			elseif ($pathOptions[1] == 'savePermission' && isset($_POST['forumID'])) 
				$this->savePermission(intval($_POST['forumID']), $_POST['permission']);
			elseif ($pathOptions[1] == 'addPermission' && isset($_POST['type'], $_POST['forumID'])) 
				$this->addPermission($_POST['type'], (int) $_POST['forumID'], isset($_POST['typeID'])?(int) $_POST['typeID']:null);
			elseif ($pathOptions[1] == 'deletePermission' && isset($_POST['type'], $_POST['typeID'], $_POST['forumID'])) 
				$this->deletePermission($_POST['type'], (int) $_POST['typeID'], (int) $_POST['forumID']);
			else 
				displayJSON(array('failed' => true));
		}

		public function getDetails($forumID, $full = false) {
			global $currentUser, $mysql, $mongo;

			$forumManager = new ForumManager($forumID, ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

			$lForumManager = new ForumManager(0, ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$list = $lForumManager->getAdminForums(0, $forumID);
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
				$gameDetails = $mongo->games->findOne(array('forumID' => $gameForumID), array('gameID' => true, 'groupID' => true, 'players' => true));
				$groups = $mysql->query("SELECT groupID, name FROM forums_groups WHERE gameID = {$gameDetails['gameID']}")->fetchAll();
				foreach ($groups as &$group) 
					$group['groupID'] = (int) $group['groupID'];
				foreach ($gameDetails['players'] as &$player) 
					$player = $player['user'];
				$details['gameDetails'] = array('forumID' => $gameForumID, 'groupID' => (int) $gameDetails['groupID'], 'groups' => $groups, 'players' => $gameDetails['players']);
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
			if ($details['isGameForum']) 
				foreach ($details['gameDetails']['groups'] as &$group) 
					$group['permissionSet'] = in_array($group['groupID'], $pGroups)?true:false;
			$permissions['user'] = $mysql->query("SELECT 'user' as `type`, u.userID as id, u.username name, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_users p INNER JOIN users u ON p.userID = u.userID WHERE p.forumID = {$forumID}")->fetchAll();
			$pUsers = array();
			foreach ($permissions['user'] as $key => $permission) {
				$permissions['user'][$key] = $this->castPermissions($permission, 4);
				$permissions['user'][$key]['ref'] = 'user_'.$permission['id'];
				$pUsers[] = $permission['id'];
			}
			if ($details['isGameForum']) 
				foreach ($details['gameDetails']['players'] as &$player) 
					$player['permissionSet'] = in_array($player['userID'], $pUsers)?true:false;

			displayJSON(array('success' => true, 'list' => array($list), 'details' => $details, 'permissions' => $permissions));
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

		public function updateForum($forumID) {
			global $currentUser, $mysql;

			$title = sanitizeString($_POST['title']);
			$desc = sanitizeString($_POST['desc']);

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

			if ($forum->parentID == 2) 
				displayJSON(array('failed' => true, 'errors' => array('gameForum')));

			$updateForum = $mysql->prepare("UPDATE forums SET ".($forum->parentID != 2?'title = :title, ':'')."description = :description WHERE forumID = {$forumID} LIMIT 1");
			if ($forum->parentID != 2) 
				$updateForum->bindValue(':title', $title);
			$updateForum->bindValue(':description', $desc);
			$updateForum->execute();

			if ($updateForum->rowCount()) 
				displayJSON(array('success' => true));
			else 
				displayJSON(array('failed' => true));
		}

		public function changeOrder($forumID, $direction) {
			global $currentUser, $mysql;

			if (!in_array($direction, array('up', 'down'))) 
				displayJSON(array('failed' => true, 'errors' => array('invalidDirection')));

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			$parent = $forumManager->forums[$forum->parentID];
			if ($forum == null || $parent == null || !$parent->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

			if (($direction == 'up' && $forum->order == 1) || ($direction == 'down' && $forum->order == $parent->childCount)) 
				displayJSON(array('failed' => true, 'errors' => array('invalidReorder')));
			$curPos = $newPos = $forum->order;
			$newPos += $direction == 'up'?-1:1;
			$mysql->query("UPDATE forums SET `order` = {$curPos} WHERE parentID = {$parent->forumID} AND `order` = {$newPos}");
			$mysql->query("UPDATE forums SET `order` = {$newPos} WHERE forumID = {$forumID}");
			displayJSON(array('success' => true));
		}

		public function deleteForum($forumID) {
			global $currentUser, $mysql;

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

			$forum->deleteForum();
			displayJSON(array('success' => true));
		}

		public function createForum($parentID, $name) {
			global $currentUser, $mysql;

			if (strlen($name) < 3) 
				displayJSON(array('failed' => true, 'errors' => ('shortName')));

			$forumManager = new ForumManager($parentID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$parentID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

			$addForum = $mysql->prepare("INSERT INTO forums (title, parentID, heritage, `order`, gameID) VALUES (:title, {$parentID}, '".time()."', :order, ".($forum->isGameForum()?$forum->gameID:'NULL').')');
			$addForum->bindValue(':title', sanitizeString($name));
			$addForum->bindValue(':order', intval($forum->childCount) + 1);
			$addForum->execute();
			$forumID = (int) $mysql->lastInsertId();
			$mysql->query('UPDATE forums SET heritage = "'.$forum->getHeritage(true).'-'.sql_forumIDPad($forumID).'" WHERE forumID = '.$forumID);
			$mysql->query('INSERT INTO forums_permissions_general (forumID) VALUES ('.$forumID.')');

			displayJSON(array('success' => true, 'forum' => array('forumID' => $forumID, 'order' => $forum->childCount + 1, 'type' => 'f', 'title' => $name)));
		}

		public function createGroup($forumID, $name) {
			global $currentUser, $mysql;

			if (strlen($name) < 3) 
				displayJSON(array('failed' => true, 'errors' => array('noName')));

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

			if ($forum->isGameForum()) {
				$groupCount = $mysql->query("SELECT COUNT(groupID) FROM forums_groups WHERE gameID = {$forum->gameID}")->fetchColumn();
				if ($groupCount >= 5) 
					displayJSON(array('failed' => true, 'errors' => array('tooManyGroups')));

				$addGroup = $mysql->prepare("INSERT INTO forums_groups SET name = :name, ownerID = {$currentUser->userID}, gameID = {$forum->gameID}");
				$addGroup->execute(array(':name' => $name));
				$groupID = $mysql->lastInsertId();
				$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$groupID}, userID = {$currentUser->userID}");

				displayJSON(array('success' => true, 'groupID' => $groupID));
			}
		}

		public function editGroup($groupID, $name) {
			global $currentUser, $mysql, $mongo;

			$gameInfo = $mongo->games->findOne(array('groupID' => (int) $groupID), array('_id' => false, 'forumID' => true, 'groupID' => true));
			if ($gameInfo) 
				displayJSON(array('failed' => true, 'errors' => array('mainGroup')));
			if (strlen($name) < 3) 
				displayJSON(array('failed' => true, 'errors' => array('noName')));

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

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
			global $currentUser, $mysql, $mongo;

			$gameInfo = $mongo->games->findOne(array('groupID' => (int) $groupID), array('_id' => false, 'forumID' => true, 'groupID' => true));
			if ($gameInfo) 
				displayJSON(array('failed' => true, 'errors' => array('mainGroup')));

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

			if ($forum->isGameForum()) {
				$mysql->query("DELETE g, m, p FROM forums_groups g INNER JOIN forums_groupMemberships m ON g.groupID = m.groupID LEFT JOIN forums_permissions_groups p ON g.groupID = p.groupID WHERE g.groupID = {$groupID}");
				displayJSON(array('success' => true, 'deleted' => true));
			}
		}

		public function savePermission($forumID, $permission) {
			global $currentUser, $mysql, $permissionTypes;

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

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

		public function addPermission($type, $forumID, $typeID = null) {
			global $mysql, $currentUser, $permissionTypes;

			if (!in_array($type, array('general', 'group', 'user'))) 
				displayJSON(array('failed' => true, 'errors' => array('noType')));

			if ($type == 'general') 
				$exists = $mysql->query("SELECT forumID FROM forums_permissions_general WHERE forumID = {$forumID}");
			else 
				$exists = $mysql->query("SELECT forumID FROM forums_permissions_{$type}s WHERE forumID = {$forumID} AND {$type}ID = {$typeID}");
			if ($exists->rowCount()) 
				displayJSON(array('failed' => true, 'errors' => array('alreadyExists')));

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

			if ($type == 'general' && $forum->isGameForum()) 
				$create = $mysql->query("INSERT INTO forums_permissions_general SET forumID = {$forumID}");
			elseif ($type == 'group' || $type == 'user')
				$create = $mysql->query("INSERT INTO forums_permissions_{$type}s SET forumID = {$forumID}, {$type}ID = {$typeID}");

			if ($create->rowCount()) {
				$newPermission = array('type' => $type);
				if ($newPermission != 'general') 
					$newPermission['id'] = $typeID;
				foreach ($permissionTypes as $key => $value) 
					$newPermission[$key] = 0;
				displayJSON(array('success' => true, 'newPermission' => $newPermission));
			} else 
				displayJSON(array('failed' => true, 'errors' => array('didntInsert')));

		}

		public function deletePermission($type, $typeID, $forumID) {
			global $mysql, $currentUser;

			if (!in_array($type, array('general', 'group', 'user'))) 
				displayJSON(array('failed' => true, 'errors' => array('noType')));

			if ($type == 'general') 
				$exists = $mysql->query("SELECT forumID FROM forums_permissions_general WHERE forumID = {$forumID}");
			else 
				$exists = $mysql->query("SELECT forumID FROM forums_permissions_{$type}s WHERE forumID = {$forumID} AND {$type}ID = {$typeID}");
			if (!$exists->rowCount()) 
				displayJSON(array('failed' => true, 'errors' => array('doesntExist')));

			$forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if ($forum == null || !$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')));

			if ($type == 'general' && $forum->isGameForum()) 
				$create = $mysql->query("DELETE FROM forums_permissions_general WHERE forumID = {$forumID}");
			elseif ($type == 'group' || $type == 'user')
				$create = $mysql->query("DELETE FROM forums_permissions_{$type}s WHERE forumID = {$forumID} AND {$type}ID = {$typeID}");

			displayJSON(array('success' => true));
		}
	}
?>