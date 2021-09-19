<? require_once(FILEROOT.'/header.php'); ?>
		<div class="sideWidget">
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
					<p class="alignCenter"><button type="submit" ng-click="saveLFG()" class="fancyButton smallButton" skew-element>Update</button></p>
				</div>
			</div>
		</div>

		<div class="mainColumn">
			<h1 class="headerbar" skew-element>My Games</h1>
			<div id="gamesPlaying">
				<div class="clearfix hbdTopper"><a href="/games/list/" class="fancyButton smallButton" skew-element>Join a Game</a></div>
				<h2 class="headerbar hbDark hb_hasButton hb_hasList" skew-element>Games I'm Playing</h2>
				<ul ng-show="inGames.notGM" class="gameList hbAttachedList hbdMargined">
					<li ng-repeat="game in games | filter: { isGM: false, isRetired:false } | orderBy: ['system', 'title']" class="gamePlaying">
						<a href="/games/{{game.gameID}}/" class="gameTitle">{{game.title}}{{game.status?'':' (Closed)'}}</a
						><div class="systemType" ng-bind-html="game.customType?game.customType:game.system"></div
						><div class="gmInfo"><a href="/user/{{game.gm.userID}}/" class="username">{{game.gm.username}}</a></div>
					</li>
				</ul>
				<div ng-hide="inGames.notGM" class="noneFound">It seems you aren't playing any games yet. <br>You might want to <a href="/games/list/">join one</a>!</div>
			</div>

			<div id="gamesRunning">
				<div class="clearfix hbdTopper"><a href="/games/new/" class="fancyButton smallButton" skew-element>Create a New Game</a></div>
				<h2 class="headerbar hbDark hb_hasButton hb_hasList" skew-element>Games I'm Running</h2>
				<ul ng-show="inGames.gm" class="gameList hbAttachedList hbdMargined">
					<li ng-repeat="game in games | filter: { isGM : true, isRetired:false } | orderBy: ['system', 'title']" class="gameRunning">
						<a href="/games/{{game.gameID}}/" class="gameTitle">{{game.title}}{{game.status?'':' (Closed)'}}</a
						><div class="systemType" ng-bind-html="game.customType?game.customType:game.system"></div>
					</li>
				</ul>
				<div ng-hide="inGames.gm" class="noneFound">It seems you aren't running any games yet. <br>You might want to <a href="/games/new/">get started</a>!</div>
			</div>

			<div id="gamesRetired" ng-show="inGames.retired">
				<blockquote class="spoiler closed"><div class="tag">[ <span class="open">+</span><span class="close">-</span> ] Retired games</div><div class="hidden">
					<ul class="gameList prettyList hbdMargined">
						<li ng-repeat="game in games | filter: { isRetired:true } | orderBy: ['system', 'title']" class="gameRunning">
							<a href="/games/{{game.gameID}}/" class="gameTitle">{{game.title}}{{game.status?'':' (Closed)'}}</a
							><div class="systemType" ng-bind-html="game.customType?game.customType:game.system"></div>
						</li>
					</ul>
				</div></blockquote>
			</div>

		</div>
<? require_once(FILEROOT.'/footer.php'); ?>
