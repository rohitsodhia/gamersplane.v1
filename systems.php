<? require_once(FILEROOT.'/header.php'); ?>
		<div class="sideWidget left">
			<h2>Filter</h2>
			<form>
				<div class="tr">Search:</div>
				<div class="tr"><input type="text" name="search" ng-model="filter.fullName" ng-change="adjustPagination()"></div>
				<div class="alignCenter"><button name="filter" value="filter" class="fancyButton">Filter</button></div>
			</form>
		</div>

		<div class="mainColumn right">
			<h1 class="headerbar">Systems on Gamers' Plane</h1>
			<div class="clearfix"><paginate class="tr"></paginate></div>
			<div id="systems">
				<div class="system clearfix" ng-repeat="system in systems | filter:filter | paginateItems:10:(pagination.current - 1) * 10">
					<div class="logoWrapper"><div class="logo"><img src="/images/logos/{{system.shortName}}.png"></div></div>
					<div class="info">
						<h2 ng-bind-html="system.fullName"></h2>
						<div class="tr publisher" ng-if="system.publisher">Publisher: <span ng-bind-html="wrapPublisher(system)"></span></div>
						<div class="tr genres" ng-if="system.genres.length">Genre(s): <span ng-repeat="genre in system.genres">{{genre}}{{$last?'':','}}</span></div>
					</div>
				</div>
				<div id="noResults" ng-hide="pagination.numItems">No systems found</div>
			</div>
			<div class="clearfix"><paginate class="tr"></paginate></div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>