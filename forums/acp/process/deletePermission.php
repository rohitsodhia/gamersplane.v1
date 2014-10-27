<?
	$userID = intval($_SESSION['userID']);
	$forumID = intval($_POST['forumID']);
	$pType = in_array($_POST['pType'], array('group', 'user'))?$_POST['pType']:NULL;
	$typeID = intval($_POST['typeID']);
	
	$isAdmin = $mysql->query("SELECT f.forumID, p.forumID, fa.forumID FROM forums f, forums p, forumAdmins fa WHERE fa.userID = 1 AND fa.forumID = p.forumID AND f.heritage LIKE CONCAT(p.heritage, '%') AND f.forumID = $forumID");
	$forumInfo = $mysql->query("SELECT forumID, title, forumType, parentID, heritage FROM forums WHERE forumID = $forumID");
	$forumInfo = $forumInfo->fetch();
	if (!$isAdmin->rowCount() || ($forumInfo['parentID'] == 2 && $pType == 'group') || $pType == NULL || $typeID == 0) { header('Location: /forums/'); exit; }
	
	if (isset($_POST['delete'])) {
		if ($pType == 'group') $mysql->query("DELETE FROM forums_permissions_groups WHERE groupID = $typeID");
		else $typeInfo = $mysql->query("DELETE FROM forums_permissions_users WHERE userID = $typeID");
		echo 1;
	} else echo 0;
?>