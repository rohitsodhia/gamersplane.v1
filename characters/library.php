<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<div class="sideWidget left">
			<h2>Filter</h2>
			<form id="filterChars" method="get">
<? /*				<div class="tr">
					Filter by <select id="orderBy" name="orderBy">
						<option value="createdOn_d">Created on (Desc)</option>
						<option value="createdOn_a">Created on (Asc)</option>
						<option value="name_a">Name (Asc)</option>
						<option value="name_d">Name (Desc)</option>
						<option value="system">System</option>
					</select>
				</div> */ ?>
<!--				<div class="tr"><input id="search" name="search" type="text" class="placeholder" data-placeholder="Search for..."></div>-->
				<ul class="clearfix">
<?
	$systems = $mysql->query('SELECT systemID, shortName, fullName FROM systems WHERE enabled = 1');
	$totalNumSystems = $systems->rowCount();
	foreach ($systems as $info) echo "					<li><input id=\"system_{$info['shortName']}\" type=\"checkbox\" name=\"filterSystem[]\" value=\"{$info['systemID']}\"".(isset($_GET['filter']) && array_search($info['systemID'], $_GET['filterSystem']) !== FALSE || !isset($_GET['filter'])?' checked="checked"':'')."> <label for=\"system_{$info['shortName']}\">{$info['fullName']}</label></li>\n"
?>
				</ul>
				<input type="hidden" name="numSystems" value="<?=$systems->rowCount()?>">
				<div class="alignCenter"><button name="filter" value="filter" class="fancyButton">Filter</button></div>
			</form>
		</div>

		<div class="mainColumn right">
			<h1 class="headerbar hb_hasList">Character Library</h1>
			
			<ul id="charList" class="hbAttachedList hbMargined">
<?
/*	if (isset($_GET['filter']) && $_GET['orderBy'] == 'createdOn_d' || !isset($_GET['filter'])) $orderBy = 'games.created DESC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'createdOn_a') $orderBy = 'games.created ASC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'name_a') $orderBy = 'games.title ASC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'name_d') $orderBy = 'games.title DESC';
	elseif (isset($_GET['filter']) && $_GET['orderBy'] == 'system') $orderBy = 'systems.fullName ASC';*/
	$orderBy = 's.fullName ASC';
	$characters = $mysql->query("SELECT c.*, s.shortName, s.fullName, u.username FROM characterLibrary l INNER JOIN characters c ON l.characterID = c.characterID INNER JOIN systems s ON c.systemID = s.systemID INNER JOIN users u ON c.userID = u.userID LEFT JOIN players p ON c.gameID = p.gameID AND p.userID = $userID WHERE c.userID != $userID AND p.userID IS NULL  ORDER BY $orderBy");
	
	if ($characters->rowCount()) { foreach ($characters as $info) {
?>
				<li class="clearfix">
					<a href="/characters/<?=$info['shortName']?>/<?=$info['characterID']?>" class="charLabel"><?=$info['label']?></a
					><div class="systemType"><?=$info['fullName']?></div
					><div class="playerLink"><a href="/ucp/<?=$info['gmID']?>" class="username"><?=$info['username']?></a></div>
				</li>
<?
	} } else echo "\t\t\t\t<div id=\"noResults\">Doesn't seem like anyone is sharing any characters right now. Maybe you should share one of yours?</div>\n";
?>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>