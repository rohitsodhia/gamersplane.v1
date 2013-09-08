<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<div id="lfg" class="sidebar">
			<div class="widget">
				<h3>Looking for Game</h3> 
				<div class="widgetBody">
					<p>Your current LFG Status:</p>
<?
	$lfgs = $mysql->query('SELECT systems.fullName FROM lfg, systems WHERE lfg.systemID = systems.systemID AND lfg.userID = '.$userID);
	$lfgStatus = '';
	if ($lfgs->rowCount()) {
		echo "\t\t\t\t\t<ul>\n";
		while ($game = $lfgs->fetchColumn()) echo "\t\t\t\t\t\t<li>{$game}</li>\n";
		echo "\t\t\t\t\t</ul>\n";
	} else echo "\t\t\t\t\t<p>No games selected.</p>\n";
?>
<!--					<p><b>Custom/Notes:</b> <?=strlen($lfgVals['custom'])?printReady($lfgVals['custom']):'None'?></p>-->
					<p class="alignRight"><a id="lfgEdit" href="<?=SITEROOT?>/games/lfg">Edit</a></p>
				</div>
			</div>
		</div>

		<div class="mainColumn">
			<h1>My Games</h1>
			
<? if ($_GET['submitted']) { ?>
			<div class="alertBox_success">
				Character successfully submitted!
			</div>
			
<? } ?>
			<div id="gamesList">
				<h3>Games I'm Playing</h3>
				<a href="<?=SITEROOT?>/games/list">Join a Game</a>
<?
	$games = $mysql->query('SELECT g.gameID, g.title, g.open, u.userID, u.username, s.fullName system FROM characters c, games g, users u, systems s WHERE c.gameID = g.gameID AND g.gmID = u.userID AND c.systemID = s.systemID AND c.userID = '.$userID.' AND c.gameID != 0 ORDER BY g.open DESC, s.fullName, g.title');
	
	$currentSystem = '';
	$first = TRUE;
	if ($games->rowCount()) { foreach ($games as $info) {
		echo "\t\t\t\t<div class=\"tr".($first?' firstTR':'')." gamePlaying\">\n";
		echo "\t\t\t\t\t".'<a href="'.SITEROOT.'/games/'.$info['gameID'].'" class="gameTitle">'.$info['title'].($info['open']?'':' (Closed)')."</a>\n";
		echo "\t\t\t\t\t".'<div class="systemType">'.$info['system']."</div>\n";
		echo "\t\t\t\t\t".'<div class="gmInfo"><a href="'.SITEROOT.'/ucp/'.$info['userID'].'" class="username">'.$info['username']."</a></div>\n";
//		echo "\t\t\t\t\t".'<div class="playerLinks"><a href="'.SITEROOT.'/games/leave/'.$info['gameID'].'">Leave Game</a></div>'."\n";
		echo "\t\t\t\t</div>\n";
		
		if ($first) { $first = FALSE; }
	} } else { echo "\t\t\t\t".'<h2>It seems you aren\'t playing any games yet. You might wanna join one!</h2>'."\n"; }
?>
				
				<h3 id="gamesRunning">Games I'm Running</h3>
				<a href="<?=SITEROOT?>/games/new">Create a new game</a>
<?
	$games = $mysql->query('SELECT g.gameID, g.title, g.open, s.fullName system FROM games g INNER JOIN gms ON g.gameID = gms.gameID INNER JOIN systems s ON g.systemID = s.systemID WHERE gms.userID = '.$userID.' ORDER BY g.open DESC, s.fullName, g.title');
	
	$currentSystem = '';
	$first = TRUE;
	if ($games->rowCount()) { foreach ($games as $info) {
		echo "\t\t\t\t<div class=\"tr".($first?' firstTR':'')." gameRunning\">\n";
		echo "\t\t\t\t\t".'<a href="'.SITEROOT.'/games/'.$info['gameID'].'" class="gameTitle">'.$info['title'].($info['open']?'':' (Closed)')."</a>\n";
		echo "\t\t\t\t\t".'<div class="systemType">'.$info['system']."</div>\n";
//		echo "\t\t\t\t\t".'<div class="gmLinks"><a href="'.SITEROOT.'/games/changeStatus/'.$info['gameID'].'" class="changeStatus">'.($info['open']?'Close':'Open').' Game</a></div>'."\n";
		echo "\t\t\t\t</div>\n";
		
		if ($first) { $first = FALSE; }
	} } else { echo "\t\t\t\t".'<h2>It seems you aren\'t running any games yet. You might wanna get started!</h2>'."\n"; }
?>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>