<?	require_once(FILEROOT.'/header.php'); ?>
		<div class="sideWidget left">
			<h2>Filter</h2>
			<form>
				<div class="tr">Search:</div>
				<div class="tr"><input type="text" name="search" ng-model="filter.search" ng-change="adjustPagination()"></div>
<!--				<div class="alignCenter"><button name="filter" value="filter" class="fancyButton">Filter</button></div>-->
			</form>
		</div>

		<div class="mainColumn right">
			<h1 class="headerbar">Systems on Gamers' Plane</h1>
			<div class="alignRight">
				<div id="numResults" ng-show="filter.search.length != 0"><strong>{{numSystems}}</strong> Systems Found</div>
				<paginate class="tr"></paginate>
			</div>
			<div id="systems">
				<div class="system clearfix" ng-repeat="system in systems | filter:{ fullName: filter.search } | paginateItems:10:(pagination.current - 1) * 10">
					<div class="logoWrapper"><div class="logo"><img src="/images/logos/{{system.shortName}}.png"></div></div>
					<div class="info">
						<h2 ng-bind-html="system.fullName"></h2>
						<div class="tr publisher" ng-if="system.publisher.name.length">Publisher: <span ng-bind-html="wrapPublisher(system)"></span></div>
						<div class="tr genres" ng-if="system.genres.length">Genre<span ng-if="system.genres.length > 1">s</span>: <span ng-repeat="genre in system.genres">{{genre}}{{$last?'':','}}</span></div>
						<div class="tr basics" ng-if="system.basics.length">
							<h3>Buy the Basics!</h3>
							<p ng-repeat="basic in system.basics"><a href="{{basic.site}}" target="_blank" ng-bind-html="basic.text | trustHTML"></a></p>
						</div>
					</div>
				</div>
				<div id="noResults" ng-hide="pagination.numItems">No systems found</div>
			</div>
			<div class="alignRight"><paginate class="tr"></paginate></div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>