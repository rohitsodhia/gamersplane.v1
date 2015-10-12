<div class="paginateDiv" ng-if="numPages > 1">
	<div class="pageDisplay">{{current}} of {{numPages}} &bull;</div>
	<a ng-if="current != 1" ng-click="changePage(1)">&lt;&lt; First</a>
	<a ng-if="current > 1" ng-click="changePage(current - 1)">&lt;</a>
	<a ng-repeat="page in pages" ng-class="{'currentPage noPointer': page == current}" ng-click="changePage(page)">{{page}}</a>
	<a ng-if="current < numPages" ng-click="changePage(current + 1)">&gt;</a>
	<a ng-if="numPages > 1 && numPages != current" ng-click="changePage(numPages)">Last &gt;&gt;</a>
</div>