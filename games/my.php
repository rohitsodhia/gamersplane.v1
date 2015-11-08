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
				<p class="alignRight"><a id="lfgEdit" href="/games/lfg/" colorbox>Edit</a></p>
			</div>
		</div>

		<div class="mainColumn">
			<h1 class="headerbar" skew-element>My Games</h1>
			<div id="gamesPlaying">
				<div class="clearfix hbdTopper"><a href="/games/list/" class="fancyButton smallButton" skew-element>Join a Game</a></div>
				<h2 class="headerbar hbDark hb_hasButton hb_hasList" skew-element>Games I'm Playing</h2>
				<ul ng-show="inGames.notGM" class="gameList hbAttachedList hbdMargined">
					<li ng-repeat="game in games | filter: { isGM: false } | orderBy: ['system', 'title']" class="gamePlaying">
						<a href="/games/{{game.gameID}}/" class="gameTitle">{{game.title}}{{game.status == 1?'':'(Closed)'}}</a>
						<div class="systemType" ng-bind-html="game.system | trustHTML"></div>
						<div class="gmInfo"><a href="/user/{{game.gm.userID}}/" class="username">{{game.gm.username}}</a></div>
					</li>
				</ul>
				<div ng-hide="inGames.notGM" class="noneFound">It seems you aren't playing any games yet. <br>You might want to <a href="/games/list/">join one</a>!</div>
			</div>
			
			<div id="gamesRunning">
				<div class="clearfix hbdTopper"><a href="/games/new/" class="fancyButton smallButton" skew-element>Create a New Game</a></div>
				<h2 class="headerbar hbDark hb_hasButton hb_hasList" skew-element>Games I'm Running</h2>
				<ul ng-show="inGames.gm" class="gameList hbAttachedList hbdMargined">
					<li ng-repeat="game in games | filter: { isGM : true } | orderBy: ['system', 'title']" class="gameRunning">
						<a href="/games/{{game.gameID}}/" class="gameTitle">{{game.title}}{{game.status == 1?'':'(Closed)'}}</a>
						<div class="systemType" ng-bind-html="game.system | trustHTML"></div>
					</li>
				</ul>
				<div ng-hide="inGames.gm" class="noneFound">It seems you aren't running any games yet. <br>You might want to <a href="/games/new/">get started</a>!</div>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>