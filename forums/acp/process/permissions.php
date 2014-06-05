<?
	function getTrinary($value, $pType) {
		$multipliers = array('general' => 1, 'group' => 2, 'user' => 4);
		$value = (intval($value) == -1 || intval($value) == 1)?intval($value):0;
		return $value * $multipliers[$pType];
	}
	
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($_POST['forumID']);
	$pType = in_array($_POST['pType'], array('general', 'group', 'user'))?$_POST['pType']:FALSE;
	
	$isAdmin = $mysql->query("SELECT f.forumID, p.forumID, fa.forumID FROM forums f, forums p, forumAdmins fa WHERE fa.userID = 1 AND fa.forumID = p.forumID AND f.heritage LIKE CONCAT(p.heritage, '%') AND f.forumID = $forumID");
	$forumInfo = $mysql->query("SELECT forumID, title, forumType, parentID, heritage FROM forums WHERE forumID = $forumID");
	$forumInfo = $forumInfo->fetch();
	if (!$isAdmin->rowCount() || ($forumInfo['parentID'] == 2 && $forumID != 10) || (!$pType && !isset($_POST['save']))) {
		if (MODAL) echo 0;
		else { header('Location: /forums/'); exit; }
	} 

	$gameForum = strpos($forumInfo['heritage'], sql_forumIDPad(2)) !== FALSE && $forumID != 10?TRUE:FALSE;
	if ($gameForum) {
		$heritage = explode('-', $forumInfo['heritage']);
		$gameID = $mysql->query('SELECT gameID FROM games WHERE forumID = '.intval($heritage[1]));
		$gameID = $gameID->fetchColumn();
	} else $gameID = NULL;
	
	if (isset($_POST['add'])) {
		$permissions = $_POST['permissions'];
		if ($permissions['moderate']) foreach ($permissions as $key => $value) $permissions[$key] = 1;
		else foreach ($permissions as $key => $value) $permissions[$key] = getTrinary($value, $pType);

		if ($gameForum && $pType == 'user') $validOpt = $mysql->prepare("SELECT u.userID optID FROM users u INNER JOIN players p ON u.userID = p.userID and p.approved = 1 LEFT JOIN forums_permissions_users per ON u.userID = per.userID AND per.forumID = {$forumID} WHERE u.username = ? AND p.gameID = {$gameID} AND per.forumID IS NULL LIMIT 1");
		elseif ($pType == 'user') $validOpt = $mysql->prepare("SELECT u.userID optID FROM users u LEFT JOIN forums_permissions_users per ON u.userID = per.userID AND per.forumID = {$forumID} WHERE u.username = ? AND per.forumID IS NULL LIMIT 1");
		elseif ($pType == 'group') $validOpt = $mysql->prepare("SELECT fg.groupID optID FROM forums_groups fg LEFT JOIN forums_permissions_groups per ON fg.groupID = per.groupID AND per.forumID = {$forumID} WHERE fg.name = ? AND fg.ownerID = {$userID} LIMIT 1");

		$search = sanitizeString($_POST['option'], 'search_format');
		$validOpt->execute(array($search));
		$optID = $validOpt->fetchColumn();

		if ($optID) $mysql->query("INSERT INTO forums_permissions_{$pType}s SET {$pType}ID = {$optID}, forumID = {$forumID}, `read` = {$permissions['read']}, `write` = {$permissions['write']}, `editPost` = {$permissions['editPost']}, `deletePost` = {$permissions['deletePost']}, `createThread` = {$permissions['createThread']}, `deleteThread` = {$permissions['deleteThread']}, `addPoll` = {$permissions['addPoll']}, `addRolls` = {$permissions['addRolls']}, `addDraws` = {$permissions['addDraws']}, `moderate` = {$permissions['moderate']}");

		echo 1;
	} elseif (isset($_POST['save'])) {
		foreach ($_POST['permissions'] as $pType => $permissions) {
			if ($pType == 'general') {
				foreach ($permissions as $key => $value) $permissions[$key] = getTrinary($value, 'general');
				$mysql->query("UPDATE forums_permissions_general SET `read` = {$permissions['read']}, `write` = {$permissions['write']}, `editPost` = {$permissions['editPost']}, `deletePost` = {$permissions['deletePost']}, `createThread` = {$permissions['createThread']}, `deleteThread` = {$permissions['deleteThread']}, `addPoll` = {$permissions['addPoll']}, `addRolls` = {$permissions['addRolls']}, `addDraws` = {$permissions['addDraws']} WHERE forumID = {$forumID}");
			} else {
				foreach ($permissions as $typeID => $permission) {
					foreach ($permission as $key => $value) $permission[$key] = getTrinary($value, $pType);
					$mysql->query("UPDATE forums_permissions_{$pType}s SET `read` = {$permission['read']}, `write` = {$permission['write']}, `editPost` = {$permission['editPost']}, `deletePost` = {$permission['deletePost']}, `createThread` = {$permission['createThread']}, `deleteThread` = {$permission['deleteThread']}, `addPoll` = {$permission['addPoll']}, `addRolls` = {$permission['addRolls']}, `addDraws` = {$permission['addDraws']}, `moderate` = {$permission['moderate']} WHERE {$pType}ID = {$typeID} AND forumID = {$forumID}");
				}
			}
		}
		header('Location: /forums/acp/'.$forumID.'/permissions');
	} else header('Location: /forums/');
?>