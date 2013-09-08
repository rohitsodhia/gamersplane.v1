<?
	function getTrinary($value) { return (intval($value) >= -1 && intval($value) <= 1)?intval($value):0; }
	
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($_POST['forumID']);
	$pType = in_array($_POST['type'], array('general', 'group', 'user'))?$_POST['type']:FALSE;
	$updateType = in_array($_POST['save'], array('new', 'edit'))?$_POST['save']:FALSE;
/*	if (isset($_POST['typeName']) && $pType != 'general') {
		$typeName = sanatizeString($_POST['typeName']);
		$mysql->query('SELECT gameID, title FROM games WHERE forumID = '.$forumID);
		if ($mysql->rowCount() && $pType == 'user') {
			$gameInfo = $mysql->fetch();
			$mysql->query("SELECT users.userID FROM users, characters WHERE users.username = \"{$typeName}\" AND characters.gameID = {$gameInfo['gameID']} AND characters.approved = 1 AND users.userID = characters.userID");
			if (!$mysql->rowCount()) { header('Location: '.SITEROOT."/forums/acp/permissions/{$forumID}?{$updateType}={$pType}&notInGame=1"); exit; }
			list($typeID) = $mysql->getList();
		} elseif ($mysql->rowCount() && $pType == 'group') { header('Location: '.SITEROOT."/forums/acp/permissions/{$forumID}?noGameGroups=1"); exit; }
		else {
			$mysql->query("SELECT {$pType}ID FROM {$pType}s WHERE ".($pType == 'user'?'user':'').'name = "'.$typeName.'"');
			if (!$mysql->rowCount()) { header('Location: '.SITEROOT."/forums/acp/permissions/{$forumID}?{$updateType}={$pType}&invalidName=1"); exit; }
			list($typeID) = $mysql->getList();
		}
	} else $typeID = intval($_POST['typeID']);
	if ($updateType == 'new') {
	}*/
	
	$permissions = $_POST['permissions'];
	if ($permissions['moderate']) foreach ($permissions as $key => $value) $permissions[$key] = 1;
	else foreach ($permissions as $key => $value) $permissions[$key] = getTrinary($value);
	
	$family = retrieveHeritage($forumID);
	$adminCheck = $mysql->query('SELECT forumID FROM forumAdmins WHERE userID = '.$userID.' AND forumID IN ('.implode(', ', array_keys($family)).', 0)');
	if (!$adminCheck->rowCount()) { header('Location: '.SITEROOT.'/forums/'); exit; }
	
	if ($updateType == 'new') {
		if (isset($_POST['typeName']) && $pType != 'general') {
			$typeName = sanatizeString($_POST['typeName']);
			$gameID = $mysql->query('SELECT gameID FROM games WHERE forumID = '.$forumID);
			if ($gameID->rowCount() && $pType == 'user') {
				$gameID = $gameID->fetchColumn();
				$typeID = $mysql->query("SELECT users.userID FROM users, characters WHERE LOWER(users.username) = '".strtolower($typeName)."' AND characters.gameID = $gameID AND characters.approved = 1 AND users.userID = characters.userID");
				if (!$mysql->rowCount()) { header('Location: '.SITEROOT."/forums/acp/permissions/{$forumID}?{$updateType}={$pType}&notInGame=1"); exit; }
				$typeID = $typeID->fetchColumn();
				$adminCheck = $mysql->query('SELECT forumID FROM forumAdmins WHERE userID = '.$typeID.' AND forumID IN ('.implode(', ', array_keys($family)).', 0)');
				if ($adminCheck->rowCount()) { header('Location: '.SITEROOT."/forums/acp/permissions/{$forumID}?admin=".urlencode($typeName)); exit; }
			} elseif ($gameID->rowCount() && $pType == 'group') { header('Location: '.SITEROOT."/forums/acp/permissions/{$forumID}?noGameGroups=1"); exit; }
			else {
				$typeID = $mysql->query("SELECT {$pType}ID FROM ".($pType == 'user'?'users':'forums_groups').' WHERE LOWER('.($pType == 'user'?'user':'').'name) = "'.strtolower($typeName).'"');
				if (!$typeID->rowCount()) { header('Location: '.SITEROOT."/forums/acp/permissions/{$forumID}?{$updateType}={$pType}&invalidName=1"); exit; }
				$typeID = $typeID->fetchColumn();
				if ($pType == 'user') {
					$adminCheck = $mysql->query("SELECT forumID FROM forumAdmins WHERE userID = $typeID AND forumID IN (".implode(', ', array_keys($family)).', 0)');
					if ($adminCheck->rowCount()) { header('Location: '.SITEROOT."/forums/acp/permissions/{$forumID}?admin=".urlencode($typeName)); exit; }
				}
			}
		
			$mysql->query("INSERT INTO forums_permissions_{$pType}s ({$pType}ID, forumID) VALUES ($typeID, $forumID)");
			header('Location: '.SITEROOT.'/forums/acp/permissions/'.$forumID.'?success=1');
		} else header('Location: '.SITEROOT.'/forums/acp/permissions/'.$forumID);
	} elseif ($updateType == 'edit') {
		if ($pType == 'general') $mysql->query('UPDATE forums_permissions_general SET '.$mysql->setupUpdates($permissions).' WHERE forumID = '.$forumID);
		else {
			$typeID = intval($_POST['typeID']);
			if ($pType == 'user') {
				$adminCheck = $mysql->query('SELECT forumID FROM forumAdmins WHERE userID = '.$typeID.' AND forumID IN ('.implode(', ', array_keys($family)).', 0)');
				if ($adminCheck->rowCount()) { header('Location: '.SITEROOT."/forums/acp/permissions/{$forumID}?admin=".urlencode($typeName)); exit; }
			}
			$mysql->query("UPDATE forums_permissions_{$pType}s SET ".$mysql->setupUpdates($permissions)." WHERE forumID = {$forumID} AND {$pType}ID = {$typeID}");
		}
		header('Location: '.SITEROOT.'/forums/acp/permissions/'.$forumID.'?success=1');
	} else header('Location: '.SITEROOT.'/forums/');
?>