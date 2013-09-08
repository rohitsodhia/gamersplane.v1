<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	
	if ($_POST['orderBy'] == 'createdOn_d') $order = 'games.created DESC';
	elseif ($_POST['orderBy'] == 'createdOn_a') $order = 'games.created';
	elseif ($_POST['orderBy'] == 'name_a') $order = 'games.title';
	elseif ($_POST['orderBy'] == 'name_d') $order = 'games.title DESC';
	elseif ($_POST['orderBy'] == 'system') $order = 'systems.fullName';
	
	if (sizeof($_POST['filterSystem']) != $_POST['numSystems']) $systems = implode(', ', $_POST['filterSystem']);
	else $systems = NULL;
	
	if (strlen(trim($_POST['search']))) {
		$search = array();
		foreach(preg_split('/\s+/', sanatizeString($_POST['search']), NULL, PREG_SPLIT_NO_EMPTY) as $part) $search[] = "games.title LIKE '%$part%'";
		$search = implode(' OR ', $search);
	}
	
	$games = $mysql->query("SELECT games.gameID, games.title, systems.fullName system, games.gmID, gmUsers.username, IF(userChars.gameID IS NOT NULL, 1, IF(gms.gameID IS NOT NULL, 1, 0)) inGame FROM games INNER JOIN systems ON games.systemID = systems.systemID INNER JOIN users AS gmUsers ON games.gmID = gmUsers.userID LEFT JOIN characters AS userChars ON userChars.userID = $userID AND games.gameID = userChars.gameID LEFT JOIN gms ON (gms.userID, games.gameID) = ($userID, gms.gameID) WHERE games.gmID != $userID AND games.open = 1".($systems?" AND games.systemID IN ($systems)":'').(isset($search)?" AND ($search)":'')." HAVING inGame = 0 ORDER BY $order");
	
	if ($games->rowCount()) { foreach ($games as $gameInfo) {
		echo "\t\t\t<div class=\"tr\">\n";
		echo "\t\t\t\t".'<a href="'.SITEROOT.'/games/'.$gameInfo['gameID'].'" class="gameTitle">'.$gameInfo['title']."</a>\n";
		echo "\t\t\t\t".'<div class="systemType">'.$gameInfo['system']."</div>\n";
		echo "\t\t\t\t".'<div class="gmLink"><a href="'.SITEROOT.'/ucp/'.$gameInfo['gmID'].'" class="username">'.$gameInfo['username'].'</a></div>'."\n";
		echo "\t\t\t</div>\n";
	} } else echo "\t\t\t<h2>Doesn't seem like any games are available at this time. Maybe you should <a href=\"".SITEROOT."/games/new\">make one</a>?</h2>\n";
?>