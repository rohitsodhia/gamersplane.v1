<?	require_once(FILEROOT.'/header.php'); ?>
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
	$selectedSystems = array();
	foreach ($systems->getAllSystems() as $slug => $fullName) {
		$selectedSystem = isset($_GET['filter'], $_GET['filterSystem']) && array_search($system, $_GET['filterSystem']) !== false?true:false;
?>
					<li><input id="system_<?=$slug?>" type="checkbox" name="filterSystem[]" value="<?=$slug?>"<?=$selectedSystem?' checked="checked"':''?>> <label for="system_<?=$slug?>"><?=$fullName?></label></li>
<?
		if ($selectedSystem) 
			$selectedSystems[] = $slug;
	}
?>
				</ul>
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
	$characters = $mysql->query("SELECT c.*, u.username FROM characterLibrary l INNER JOIN characters c ON l.characterID = c.characterID INNER JOIN users u ON c.userID = u.userID ".(sizeof($selectedSystems)?'WHERE c.systemID IN ('.implode(', ', $selectedSystems).') ':'')."ORDER BY {$orderBy}");
	
	if ($characters->rowCount()) { foreach ($characters as $info) {
?>
				<li class="clearfix">
					<a href="/characters/<?=$info['system']?>/<?=$info['characterID']?>" class="charLabel"><?=$info['label']?></a
					><div class="systemType"><?=$systems->getFullName($info['system'])?></div
					><div class="playerLink"><a href="/ucp/<?=$info['gmID']?>" class="username"><?=$info['username']?></a></div>
				</li>
<?
	} } else echo "\t\t\t\t<div id=\"noResults\">Doesn't seem like anyone is sharing any characters right now. Maybe you should share one of yours?</div>\n";
?>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>