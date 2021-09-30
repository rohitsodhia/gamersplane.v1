<? $responsivePage=true;
require_once(FILEROOT.'/header.php'); ?>
	<div class="flex-row">
		<div class="col-3-4 mob-col-1">
			<div class="mainColumn">
				<h1 class="headerbar">My Games</h1>
				<div id="gamesPlaying">
					<div class="clearfix hbdTopper"><a href="/games/list/" class="fancyButton smallButton">Join a Game</a><a href="/forums/10/" class="fancyButton smallButton buttonSecondary"><i class="ra ra-beer"></i> <span class="mob-hide">Visit the </span>Games Tavern</a></div>
					<h2 class="headerbar hbDark hb_hasButton hb_hasList">Games I'm Playing</h2>
					<ul ng-show="inGames.notGM" class="gameList hbAttachedList hbdMargined">
						<li ng-repeat="game in games | filter: { isGM: false, isRetired:false } | orderBy: ['system', 'title']" class="gamePlaying">
							<span class="gameTitle"><a href="/forums/{{game.forumID}}"><i class="ra ra-speech-bubble"></i></a> <a href="/games/{{game.gameID}}/">{{game.title}}</a></span
							><div class="systemType" ng-bind-html="game.customType?game.customType:game.system"></div
							><div class="gmInfo"><a href="/user/{{game.gm.userID}}/" class="username">{{game.gm.username}}</a></div>
						</li>
					</ul>
					<div ng-hide="inGames.notGM" class="noneFound">It seems you aren't playing any games yet. <br>You might want to <a href="/games/list/">join one</a>!</div>
				</div>

				<div id="gamesRunning">
					<div class="clearfix hbdTopper"><a href="/games/new/" class="fancyButton smallButton">Create a New Game</a></div>
					<h2 class="headerbar hbDark hb_hasButton hb_hasList">Games I'm Running</h2>
					<ul ng-show="inGames.gm" class="gameList hbAttachedList hbdMargined">
						<li ng-repeat="game in games | filter: { isGM : true, isRetired:false } | orderBy: ['system', 'title']" class="gameRunning">
							<span class="gameTitle"><a href="/forums/{{game.forumID}}"><i class="ra ra-speech-bubble"></i></a> <a href="/games/{{game.gameID}}/">{{game.title}}</a></span
							><div class="systemType" ng-bind-html="game.customType?game.customType:game.system"></div><span ng-if="game.status=='open'" class="badge badge-gameOpen">Open</span>
						</li>
					</ul>
					<div ng-hide="inGames.gm" class="noneFound">It seems you aren't running any games yet. <br>You might want to <a href="/games/new/">get started</a>!</div>
				</div>

				<div id="gamesRetired" ng-show="inGames.retired">
					<blockquote class="spoiler closed"><div class="tag">[ <span class="open">+</span><span class="close">-</span> ] Retired games</div><div class="hidden">
						<ul class="gameList prettyList hbdMargined">
							<li ng-repeat="game in games | filter: { isRetired:true } | orderBy: ['system', 'title']" class="gameRunning">
							<span  class="gameTitle"><a href="/forums/{{game.forumID}}"><i class="ra ra-speech-bubble"></i></a> <a href="/games/{{game.gameID}}/">{{game.title}}</a></span
							><div class="systemType" ng-bind-html="game.customType?game.customType:game.system"></div>
							</li>
						</ul>
					</div></blockquote>
				</div>
			</div>
		</div>
		<div class="col-1-4 mob-col-1 ">
			<div class="sideWidget ml-1 mob-ml-0">
				<h2>Looking for Game</h2>
				<div class="widgetBody">
					<div id="currentLFG" ng-hide="editLFG">
						<p>Your current LFG Status:</p>
						<ul ng-if="lfg.length">
							<li ng-repeat="system in lfg | orderBy: '+'" ng-bind-html="system"></li>
						</ul>
						<p ng-if="!lfg.length">No games selected.</p>
						<p class="alignRight"><a id="lfgEdit" href="" ng-click="editLFG = true">Edit</a></p>
					</div>
					<div id="editLFG" ng-show="editLFG">
						<ul>
							<li ng-repeat="system in systems"><label><pretty-checkbox checkbox="lfg" value="system"></pretty-checkbox> <span ng-bind-html="system"></span></label></li>
						</ul>
						<p class="alignCenter"><button type="submit" ng-click="saveLFG()" class="fancyButton smallButton">Update</button></p>
					</div>
				</div>
			</div>
		</div>
	</div>
<? require_once(FILEROOT.'/footer.php'); ?>
