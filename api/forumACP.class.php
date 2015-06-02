<?
	class forumACP {
		function __construct() {
			global $loggedIn, $pathOptions;
			if (!$loggedIn) 
				displayJSON(array('failed' => true, 'errors' => array('loginRequired')), true);

			if ($pathOptions[0] == 'details' && intval($_POST['forumID'])) 
				$this->getDetails($_POST['forumID'], isset($_POST['full'])?true:false);
			else 
				displayJSON(array('failed' => true));
		}

		public function getDetails($forumID, $full = false) {
			global $currentUser, $mysql;

			$forumManager = new ForumManager(0, ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
			$forum = $forumManager->forums[$forumID];
			if (!$forum->getPermissions('admin')) 
				displayJSON(array('failed' => true, 'errors' => array('noPermissions')), true);

			$list = $forumManager->getAdminForums(0, $forumID);
			$details = array(
				'forumID' => (int) $forumID,
				'isGameForum' => $forum->isGameForum(),
				'title' => $forum->getTitle(true),
				'description' => $forum->getDescription()
				'children' => array()
			);
			if (sizeof($forum->getChildren())) { foreach ($forum->getChildren as $order => $childID) {
				$cForum = $forumManager->forums[$childID];
				$details['children'][] = array(
					'forumID' => (int) $childID,
					'order' => (int) $order,
					'type' => $cForum->getType(),
					'title' => $cForum->getTitle(true)
				);
			} }
			$permissions => array('general' => array(), 'group' => array(), 'user' => array());
			if (!$details['isGameForum'])
				$permissions['general'] = $mysql->query("SELECT `read`, `write`, editPost, deletePost, createThread, deleteThread, addRolls, addDraws, moderate FROM forums_permissions_general WHERE forumID = {$forumID}")->fetch();
			$permissions['group'] = $mysql->query("SELECT g.groupID, g.name, g.gameGroup, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_groups p INNER JOIN forums_groups g ON p.groupID = g.groupID WHERE p.forumID = {$forumID}")->fetchAll();
			$permissions['user'] = $mysql->query("SELECT u.userID, u.username, p.`read`, p.`write`, p.editPost, p.deletePost, p.createThread, p.deleteThread, p.addRolls, p.addDraws, p.moderate FROM forums_permissions_users p INNER JOIN users u ON p.userID = u.userID WHERE p.forumID = {$forumID}");
		}

		private function castPermissions($permissions) {
		}
	}
?>