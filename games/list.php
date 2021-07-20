<?	require_once(FILEROOT.'/header.php'); ?>
		<div class="sideWidget left">
			<h2>Filter</h2>
			<form id="filterGames" ng-submit="filterGames()">
				<div class="tr">
					Filter by <combobox data="filterOptions" change="setFilter(value)" select></combobox>
				</div>
<!--				<div class="tr"><input id="search" name="search" type="text" class="placeholder" data-placeholder="Search for..."></div>-->
				<ul class="clearfix">
					<li id="clearCheckboxes" ng-show="filter.systems.length"><a href="" ng-click="clearSystems()" class="sprite cross small"></a> Clear choices</li>
					<li ng-repeat="(short, system) in systems"><label><pretty-checkbox checkbox="filter.systems" value="short"></pretty-checkbox> <span ng-bind-html="system"></span></label></li>
				</ul>
				<div id="toggleFullGames" class="tr"><div ng-click="slideToggle('showFullGames')" class="ofToggle mini" ng-class="{ 'on': filter.showFullGames }"></div> <span>Show full games</span></div>
				<div id="toggleInactiveGMs" class="tr"><div ng-click="slideToggle('showInactiveGMs')" class="ofToggle mini" ng-class="{ 'on': filter.showInactiveGMs }"></div> <span>Show inactive GMs</span></div>
				<div class="alignCenter"><button name="filter" value="filter" class="fancyButton" skew-element>Filter</button></div>
			</form>
		</div>

		<div class="mainColumn right">
			<h1 class="headerbar hb_hasList" skew-element>Join a Game</h1>

			<ul id="gamesList" class="hbAttachedList hbMargined" hb-margined>
				<li ng-repeat="game in games | orderBy: orderBy " class="clearfix">
					<a href="/games/{{game.gameID}}/" class="gameTitle" ng-bind-html="game.title"></a>
					<div class="systemType" ng-bind-html="game.customType?game.customType:game.system"></div>
					<div class="gmLink"><a href="/user/{{game.gm.userID}}/" class="username" ng-bind-html="game.gm.username"></a><span ng-if="game.lastActivity" ng-bind-html="game.lastActivity"></span></div>
				</li>
				<li ng-hide="games.length" id="noResults">Doesn't seem like any games are available at this time.<br>Maybe you should <a href="/games/new/">make one</a>?</li>
			</ul>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>
