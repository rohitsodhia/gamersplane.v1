<?	require_once(FILEROOT.'/header.php'); ?>
		<div class="flexWrapper">
			<div class="sideWidget left">
				<h2>Filter</h2>
				<form>
					<div class="tr">Search:</div>
					<div class="tr"><input type="text" ng-model="filter.search"></div>
				</form>
			</div>

			<div class="mainColumn right">
				<h1 class="headerbar">Systems on Gamers' Plane</h1>
				<div class="alignRight">
					<div id="numResults" ng-show="filter.search.length != 0"><strong>{{numSystems}}</strong> System{{numSystems > 1?'s':''}} Found</div>
					<div class="tr"><paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current"></paginate></div>
				</div>
				<div id="systems">
					<div class="system clearfix" ng-repeat="system in systems | filter:{ fullName: filter.search } | paginateItems: 10:(pagination.current - 1) * 10">
						<div class="leftCol">
							<div class="logo"><img src="/images/logos/{{system.shortName}}.png"></div>
							<p><a href="/games/list/?filter=filter&filterSystem%5B%5D={{system.shortName}}">Find games</a> | <a href="/games/new/?system={{system.shortName}}">Start a game</a></p>
						</div>
						<div class="info">
							<h2 ng-bind-html="system.fullName | trustHTML"></h2>
							<div ng-if="system.fullName=='Custom'">
								<br/>Play any type of game you want, from niche indie games to that one big system we forgot to list. You will have the option to freely enter any system name you want to your game.
							</div>
							<div ng-if="system.fullName!='Custom'">
								<div class="tr publisher" ng-if="system.publisher.name.length">Publisher: <span ng-bind-html="wrapPublisher(system)"></span></div>
								<div class="tr genres" ng-if="system.genres.length">Genre<span ng-if="system.genres.length > 1">s</span>: <span ng-repeat="genre in system.genres">{{genre}}{{$last?'':','}}</span></div>
								<div class="tr basics" ng-if="system.basics.length">
									<h3>Buy the Basics!</h3>
									<p ng-repeat="basic in system.basics"><a href="{{basic.site}}" target="_blank" ng-bind-html="basic.text | trustHTML"></a></p>
								</div>
							</div>
						</div>
					</div>
					<div id="noResults" ng-hide="pagination.numItems">No systems found</div>
				</div>
				<div class="alignRight tr"><paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current"></paginate></div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>