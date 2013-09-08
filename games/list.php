<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<div class="sidebar left">
			<div class="widget">
				<h3>Filter</h3>
				<div class="widgetBody">
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
<!--						<div class="tr"><input id="search" name="search" type="text" class="placeholder" data-placeholder="Search for..."></div>-->
						<ul class="clearfix">
<?
	$systems = $mysql->query('SELECT systemID, fullName FROM systems WHERE enabled = 1');
	$totalNumSystems = $systems->rowCount();
	foreach ($systems as $info) echo "							<li><input type=\"checkbox\" name=\"filterSystem[]\" value=\"{$info['systemID']}\"".(isset($_GET['filter']) && array_search($info['systemID'], $_GET['filterSystem']) !== FALSE || !isset($_GET['filter'])?' checked="checked"':'')."> {$info['fullName']}</li>\n"
?>
						</ul>
						<input type="hidden" name="numSystems" value="<?=$systems->rowCount()?>">
						<div class="alignRight"><button name="filter" value="filter" class="btn_filter"></button></div>
					</form>
				</div>
			</div>
		</div>

		<div class="mainColumn right">
			<h1>Join a Game</h1>
			
			<div id="gamesList">
<?
	if (isset($_GET['filter']) && $_GET['orderBy'] == 'createdOn_d' || !isset($_GET['filter'])) $orderBy = 'games.created DESC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'createdOn_a') $orderBy = 'games.created ASC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'name_a') $orderBy = 'games.title ASC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'name_d') $orderBy = 'games.title DESC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'system') $orderBy = 'systems.fullName ASC';
	$games = $mysql->query("SELECT games.gameID, games.title, systems.fullName system, games.gmID, gmUsers.username, IF(userChars.gameID IS NOT NULL, 1, IF(gms.gameID IS NOT NULL, 1, 0)) inGame FROM games INNER JOIN systems ON games.systemID = systems.systemID INNER JOIN users AS gmUsers ON games.gmID = gmUsers.userID LEFT JOIN characters AS userChars ON userChars.userID = $userID AND games.gameID = userChars.gameID LEFT JOIN gms ON (gms.userID, games.gameID) = ($userID, gms.gameID) WHERE games.gmID != $userID AND games.open = 1".(isset($_GET['filter'])?' AND games.systemID IN ('.implode(', ', $_GET['filterSystem']).')':'')." HAVING inGame = 0 ORDER BY $orderBy");
	
	if ($games->rowCount()) { foreach ($games as $gameInfo) {
		echo "\t\t\t\t<div class=\"tr\">\n";
		echo "\t\t\t\t\t".'<a href="'.SITEROOT.'/games/'.$gameInfo['gameID'].'" class="gameTitle">'.$gameInfo['title']."</a>\n";
		echo "\t\t\t\t\t".'<div class="systemType">'.$gameInfo['system']."</div>\n";
		echo "\t\t\t\t\t".'<div class="gmLink"><a href="'.SITEROOT.'/ucp/'.$gameInfo['gmID'].'" class="username">'.$gameInfo['username'].'</a></div>'."\n";
		echo "\t\t\t\t</div>\n";
		
		$gamesAvail = TRUE;
	} } else echo "\t\t\t\t<h2>Doesn't seem like any games are available at this time. Maybe you should <a href=\"".SITEROOT."/games/new\">make one</a>?</h2>\n";
?>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>