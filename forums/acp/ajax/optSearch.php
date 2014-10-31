<?
	if ($loggedIn) {
		$search = sanitizeString($_POST['search'], 'search_format');
		$permissionType = $_POST['permissionType'];
		$forumID = intval($_POST['forumID']);
		
		if ($permissionType == 'user') {
			$users = $mysql->prepare("SELECT u.username FROM users u LEFT JOIN forums_permissions_users per ON u.userID = per.userID AND per.forumID = {$forumID} WHERE u.username LIKE ? AND per.forumID IS NULL LIMIT 5");
			$users->execute(array("%$search%"));
			foreach ($users as $info) {
				echo "<a href=\"\">{$info['username']}</a>\n";
			}
		} elseif ($permissionType == 'group') {
			$users = $mysql->prepare("SELECT g.name FROM forums_groups g LEFT JOIN forums_permissions_groups per ON g.groupID = per.groupID AND per.forumID = {$forumID} WHERE g.name LIKE ? AND g.gameGroup = 0 AND per.forumID IS NULL LIMIT 5");
			$users->execute(array("%$search%"));
			foreach ($users as $info) {
				echo "<a href=\"\">{$info['name']}</a>\n";
			}
		}
	}
?>