<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($_POST['forumID']);
	
	$adminForums = $mysql->query('SELECT forumID FROM forumAdmins WHERE userID = '.$userID);
	$temp = array();
	foreach ($adminForums as $aForumID) $temp[] = $aForumID['forumID'];
	$adminForums = $temp;
	$heritage = $mysql->query('SELECT heritage FROM forums WHERE forumID = '.$forumID);
	$heritage = $heritage->fetchColumn();
	$heritage = explode('-', $heritage);
	foreach ($heritage as $key => $hForumID) $heritage[$key] = intval($hForumID);
	
	if (!(in_array(0, $adminForums) || array_intersect($adminForums, $heritage))) { header('Location: '.SITEROOT.'/forums/'); exit; }
	
	$toDo = '';
	$actionKey = '';
	$forumNames = array();
	foreach ($_POST as $key => $value) {
		$parts = explode('_', $key);
		if ($parts[0] == 'moveUp') {
			$toDo = 'move';
			$direction = 'up';
			$actionKey = $parts[1];
			break;
		} elseif ($parts[0] == 'moveDown') {
			$toDo = 'move';
			$direction = 'down';
			$actionKey = $parts[1];
			break;
		}
	}
	
	if ($toDo == 'move') {
		$forumCount = $mysql->query('SELECT COUNT(forumID) FROM forums WHERE parentID = '.$forumID);
		$forumCount = $forumCount->fetchColumn();
		$order = $mysql->query('SELECT `order` FROM forums WHERE forumID = '.$actionKey);
		$order = $order->fetchColumn();
		if ($direction == 'up' && $order != 1) {
			$oldPosition = $order;
			$newPosition = $order - 1;
		} elseif ($direction == 'down' && $order != $forumCount) {
			$oldPosition = $order;
			$newPosition = $order + 1;
		}
		$mysql->query("UPDATE forums SET `order` = IF(`order` = $oldPosition, $newPosition, $oldPosition) WHERE `order` IN ($oldPosition, $newPosition) AND parentID = $forumID");
		
		header('Location: '.SITEROOT.'/forums/acp/'.$forumID);
	} else header('Location: '.SITEROOT.'/forums/');
?>