<? require_once(FILEROOT.'/header.php'); ?>
		<div class="sideWidget">
			<h2>Looking for Game</h2> 
			<div class="widgetBody">
				<p>Your current LFG Status:</p>
<?
	$lfgs = $mysql->query("SELECT system FROM lfg WHERE userID = {$currentUser->userID}")->fetchAll(PDO::FETCH_COLUMN);
	if (sizeof($lfgs)) {
?>
				<ul>
<?
		foreach ($systems->getAllSystems(true) as $slug => $system) {
			if (in_array($slug, $lfgs)) {
?>
					<li><?=$system?></li>
<?
			}
		}
?>
				</ul>
<?	} else echo "\t\t\t\t<p>No games selected.</p>\n"; ?>
				<p class="alignRight"><a id="lfgEdit" href="/games/lfg">Edit</a></p>
			</div>
		</div>

		<div class="mainColumn">
			<h1 class="headerbar">My Games</h1>
			
			<div id="gamesPlaying">
				<div class="clearfix hbdTopper"><a href="/games/list/" class="fancyButton smallButton">Join a Game</a></div>
				<h2 class="headerbar hbDark hb_hasButton hb_hasList">Games I'm Playing</h2>
<?
	$games = $mysql->query('SELECT g.gameID, g.title, g.status, u.userID, u.username, s.fullName system FROM players p, games g, users u, systems s WHERE p.gameID = g.gameID AND g.gmID = u.userID AND g.system = s.shortName AND g.gmID != p.userID AND p.userID = '.$currentUser->userID.' AND p.approved = 1 AND retired IS NULL ORDER BY g.status DESC, s.fullName, g.title');
	
	$currentSystem = '';
	$first = true;
	if ($games->rowCount()) {
		echo "				<ul class=\"gameList hbAttachedList hbdMargined\">\n";
		foreach ($games as $info) {
?>
					<li class="gamePlaying">
						<a href="/games/<?=$info['gameID']?>" class="gameTitle"><?=$info['title'].($info['status'] == 1?'':' ('.$status_names[$info['status']].')')?></a>
						<div class="systemType"><?=$info['system']?></div>
						<div class="gmInfo"><a href="/user/<?=$info['userID']?>" class="username"><?=$info['username']?></a></div>
					</li>
<?
			if ($first) $first = false;
		}
		echo "					</ul>\n";
	} else echo "\t\t\t\t".'<div class="noneFound">It seems you aren\'t playing any games yet. <br>You might want to <a href="/games/list/">join one</a>!</div>'."\n";
?>
			</div>
			
			<div id="gamesRunning">
				<div class="clearfix hbdTopper"><a href="/games/new/" class="fancyButton smallButton">Create a New Game</a></div>
				<h2 class="headerbar hbDark hb_hasButton hb_hasList">Games I'm Running</h2>
<?
	$games = $mysql->query('SELECT g.gameID, g.title, g.status, s.fullName system FROM games g INNER JOIN players p ON g.gameID = p.gameID AND p.isGM = 1 INNER JOIN systems s ON g.system = s.shortName WHERE p.userID = '.$currentUser->userID.' AND retired IS NULL ORDER BY g.status DESC, s.fullName, g.title');
	
	$currentSystem = '';
	$first = true;
	if ($games->rowCount()) {
		echo "				<ul class=\"gameList hbAttachedList hbdMargined\">\n";
		foreach ($games as $info) {
?>
					<li class="gameRunning">
						<a href="/games/<?=$info['gameID']?>" class="gameTitle"><?=$info['title'].($info['status'] == 1?'':' ('.$status_names[$info['status']].')')?></a>
						<div class="systemType"><?=$info['system']?></div>
					</li>
<?
			if ($first) $first = false;
		}
		echo "					</ul>\n";
	} else echo "\t\t\t\t".'<div class="noneFound">It seems you aren\'t running any games yet. <br>You might want to <a href="/games/new/">get started</a>!</div>'."\n";
?>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>