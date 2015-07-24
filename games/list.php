<?	require_once(FILEROOT.'/header.php'); ?>
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
					<li id="clearCheckboxes"<?=isset($_GET['filter'])?'':' class="hideDiv"'?>><a href="" class="sprite cross small"></a> Clear choices</li>
<?	foreach ($systems->getAllSystems() as $slug => $system) { ?>
					<li><input id="system_<?=$slug?>" type="checkbox" name="filterSystem[]" value="<?=$slug?>"<?=isset($_GET['filter']) && array_search($slug, $_GET['filterSystem']) !== false?' checked="checked"':''?>> <label for="system_<?=$slug?>"><?=$system?></label></li>
<?	} ?>
				</ul>
				<div class="alignCenter"><button name="filter" value="filter" class="fancyButton">Filter</button></div>
			</form>
		</div>

		<div class="mainColumn right">
			<h1 class="headerbar hb_hasList">Join a Game</h1>
			
			<ul id="gamesList" class="hbAttachedList hbMargined">
<?
	if (isset($_GET['filter']) && $_GET['orderBy'] == 'createdOn_d' || !isset($_GET['filter']) || !isset($_GET['orderBy'])) 
		$orderBy = 'g.created DESC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'createdOn_a') 
		$orderBy = 'g.created ASC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'name_a') 
		$orderBy = 'g.title ASC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'name_d') 
		$orderBy = 'g.title DESC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'system') 
		$orderBy = 's.fullName ASC';
	$games = $mysql->query("SELECT g.gameID, g.title, s.fullName system, g.gmID, u.username, u.lastActivity FROM games g INNER JOIN systems s ON g.system = s.shortName LEFT JOIN players p ON g.gameID = p.gameID AND p.userID = {$currentUser->userID} INNER JOIN users u ON g.gmID = u.userID WHERE g.gmID != {$currentUser->userID} AND p.userID IS NULL AND g.status = 1".(isset($_GET['filter'])?' AND g.system IN ("'.implode('", "', $_GET['filterSystem']).'")':'')." ORDER BY $orderBy");
	
	if ($games->rowCount()) { foreach ($games as $gameInfo) {
?>
				<li class="clearfix">
					<a href="/games/<?=$gameInfo['gameID']?>/" class="gameTitle"><?=$gameInfo['title']?></a>
					<div class="systemType"><?=$gameInfo['system']?></div>
					<div class="gmLink"><a href="/user/<?=$gameInfo['gmID']?>/" class="username"><?=$gameInfo['username']?></a><?=User::inactive($gameInfo['lastActivity'])?></div>
				</li>
<?
	} } else 
		echo "\t\t\t\t<li id=\"noResults\">Doesn't seem like any games are available at this time.<br>Maybe you should <a href=\"/games/new/\">make one</a>?</li>\n";
?>
			</ul>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>