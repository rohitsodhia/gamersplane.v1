<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<div class="sideWidget left">
			<h2>Filter</h2>
			<form id="filterGames" method="get">
				<div class="tr">
					Filter by <select id="orderBy" name="orderBy">
						<option value="createdOn_d">Created on (Desc)</option>
						<option value="createdOn_a">Created on (Asc)</option>
						<option value="name_a">Name (Asc)</option>
						<option value="name_d">Name (Desc)</option>
						<option value="system">System</option>
					</select>
				</div>
<!--				<div class="tr"><input id="search" name="search" type="text" class="placeholder" data-placeholder="Search for..."></div>-->
				<ul class="clearfix">
<?
	$allSystems = $systems->getAllSystems();
	foreach ($allSystems as $systemID => $systemInfo) echo "					<li><input id=\"system_{$systemInfo['shortName']}\" type=\"checkbox\" name=\"filterSystem[]\" value=\"{$systemID}\"".(isset($_GET['filter']) && array_search($systemID, $_GET['filterSystem']) !== FALSE || !isset($_GET['filter'])?' checked="checked"':'')."> <label for=\"system_{$systemInfo['shortName']}\">{$systemInfo['fullName']}</label></li>\n"
?>
				</ul>
				<input type="hidden" name="numSystems" value="<?=sizeof($allSystems)?>">
				<div class="alignCenter"><button name="filter" value="filter" class="fancyButton">Filter</button></div>
			</form>
		</div>

		<div class="mainColumn right">
			<h1 class="headerbar hb_hasList">Join a Game</h1>
			
			<ul id="gamesList" class="hbAttachedList hbMargined">
<?
	if (isset($_GET['filter']) && $_GET['orderBy'] == 'createdOn_d' || !isset($_GET['filter'])) $orderBy = 'games.created DESC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'createdOn_a') $orderBy = 'games.created ASC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'name_a') $orderBy = 'games.title ASC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'name_d') $orderBy = 'games.title DESC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'system') $orderBy = 'systems.fullName ASC';
	$games = $mysql->query("SELECT g.gameID, g.title, s.fullName system, g.gmID, u.username FROM games g INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN players p ON g.gameID = p.gameID AND p.userID = {$userID} INNER JOIN users u ON g.gmID = u.userID WHERE g.gmID != {$userID} AND p.userID IS NULL AND g.open = 1".(isset($_GET['filter'])?' AND games.systemID IN ('.implode(', ', $_GET['filterSystem']).')':'')." ORDER BY $order");
	
	if ($games->rowCount()) { foreach ($games as $gameInfo) {
?>
				<li class="clearfix">
					<a href="/games/<?=$gameInfo['gameID']?>" class="gameTitle"><?=$gameInfo['title']?></a>
					<div class="systemType"><?=$gameInfo['system']?></div>
					<div class="gmLink"><a href="/ucp/<?=$gameInfo['gmID']?>" class="username"><?=$gameInfo['username']?></a></div>
				</li>
<?
	} } else echo "\t\t\t\t<div id=\"noResults\">Doesn't seem like any games are available at this time.<br>Maybe you should <a href=\"/games/new\">make one</a>?</div>\n";
?>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>