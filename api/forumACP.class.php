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
				'title' => $forum->getTitle(true),
				'description' => $forum->getDescription(),
				'type' => $forum->getType(),
				'parentID' => $forum->getParentID(),
				'children' => array()
			);
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
			if (!sizeof($permissions['general'])) {
				$permissions['general'] = array('type' => 'general');
				global $permissionTypes;
				foreach ($permissionTypes as $key => $value) 
					$permissions['general'][$key] = 0;
			}
			$permissions['general']['ref'] = 'general';
			$permissions['group'] = $mysql->query("SELECT 'group' as `type`, g.groupID as id, g.name, g.gameGroup, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_groups p INNER JOIN forums_groups g ON p.groupID = g.groupID WHERE p.forumID = {$forumID}")->fetchAll();
			foreach ($permissions['group'] as $key => $permission) {
				$permissions['group'][$key] = $this->castPermissions($permission, 2);
				$permissions['group'][$key]['ref'] = 'group_'.$permission['id'];
			}
			$permissions['user'] = $mysql->query("SELECT 'user' as `type`, u.userID as id, u.username, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_users p INNER JOIN users u ON p.userID = u.userID WHERE p.forumID = {$forumID}")->fetchAll();
			foreach ($permissions['user'] as $key => $permission) {
				$permissions['user'][$key] = $this->castPermissions($permission, 4);
				$permissions['user'][$key]['ref'] = 'user_'.$permission['id'];
			}

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

			$forumManager = new ForumManager(0, ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
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
				$pFields[] = "`{$field}` = ".(intval($permission->$field) * $multiplier);
			$query = "UPDATE forums_permissions_{$permission->type}".($permission->type != 'general'?'s':'')." SET ".implode(', ', $pFields)." WHERE forumID = {$forumID}".($permission->type != 'general'?" AND {$permission->type}ID = {$permission->id}":'');
			echo $query;
		}
	}
?>