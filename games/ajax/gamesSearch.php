<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	
	if ($_POST['orderBy'] == 'createdOn_d') $order = 'g.created DESC';
	elseif ($_POST['orderBy'] == 'createdOn_a') $order = 'g.created';
	elseif ($_POST['orderBy'] == 'name_a') $order = 'g.title';
	elseif ($_POST['orderBy'] == 'name_d') $order = 'g.title DESC';
	elseif ($_POST['orderBy'] == 'system') $order = 's.fullName';
	
	if (sizeof($_POST['filterSystem']) != $_POST['numSystems']) $systems = implode(', ', $_POST['filterSystem']);
	else $systems = NULL;
	
	if (strlen(trim($_POST['search']))) {
		$search = array();
		foreach(preg_split('/\s+/', sanitizeString($_POST['search']), NULL, PREG_SPLIT_NO_EMPTY) as $part) $search[] = "games.title LIKE '%$part%'";
		$search = implode(' OR ', $search);
	}
	
	$games = $mysql->query("SELECT g.gameID, g.title, s.fullName system, g.gmID, u.username FROM games g INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN players p ON g.gameID = p.gameID AND p.userID = {$userID} INNER JOIN users u ON g.gmID = u.userID WHERE g.gmID != {$userID} AND p.userID IS NULL AND g.open = 1".($systems?" AND g.systemID IN ($systems)":'').(isset($search)?" AND ($search)":'')." ORDER BY $order");
	
	if ($games->rowCount()) { foreach ($games as $gameInfo) {
?>
				<li class="clearfix">
					<a href="/games/<?=$gameInfo['gameID']?>" class="gameTitle"><?=$gameInfo['title']?></a>
					<div class="systemType"><?=$gameInfo['system']?></div>
					<div class="gmLink"><a href="/user/<?=$gameInfo['gmID']?>" class="username"><?=$gameInfo['username']?></a></div>
				</li>
<?
	} } else echo "\t\t\t\t<li id=\"noResults\">Doesn't seem like any games are available at this time.<br>Maybe you should <a href=\"/games/new/\">make one</a>?</li>\n";
?>