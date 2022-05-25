<?	$responsivePage=true;
	require_once(FILEROOT.'/header.php'); ?>
	<div class="flex-row">
		<div class="mainColumn right mlr-20 small-mlr-0">
			<h1 class="headerbar hb_hasList"><i class="ra ra-d6"></i> Browse Games <input type="text" ng-model="filter.search" placeholder="Search..." class="headerSearch"/></h1>

			<ul id="gamesList" class="hbAttachedList hbMargined" hb-margined>
				<li ng-repeat="game in games | filter:{ $ : filter.search }| orderBy: orderBy | paginateItems: 25:(pagination.current - 1) * 25 " class="clearfix">
					<a href="/games/{{game.gameID}}/" class="gameTitle" ng-bind-html="game.title"></a>
					<div class="systemType" ng-bind-html="game.customType?game.customType:game.system"></div>
					<a href="/user/{{game.gm.userID}}/" class="username" ng-bind-html="game.gm.username"></a><span ng-if="game.lastActivity" ng-bind-html="game.lastActivity"></span>
					<div class="gameTags">
						<span class="badge badge-game-{{game.status}}">{{game.status}}</span>
						<span class="badge badge-gamePrivate" ng-if='game.public'>private</span>
						<a class="badge badge-gamePublic"  ng-if='!game.public' href="/forums/{{game.forumID}}">public</a>
						<span class="badge {{game.playerCount<game.numPlayers?'badge-gameHasPlaces':'badge-gameNoPlaces'}}">{{game.playerCount}}/{{game.numPlayers}}</span>
					</div>
				</li>
				<paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current"></paginate>

				<li ng-hide="games.length" id="noResults">Doesn't seem like any games are available at this time.<br>Maybe you should <a href="/games/new/">make one</a>?</li>
			</ul>
		</div>
	</div>
<?	require_once(FILEROOT.'/footer.php'); ?>
