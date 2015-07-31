<div class="paginateDiv" ng-if="data.numItems > 0">
	<div class="pageDisplay">{{data.current}} of {{numPages}} &bull;</div>
	<a ng-if="data.current != 1" ng-click="changePage(1)">&lt;&lt; First</a>
	<a ng-if="data.current > 1" ng-click="changePage(data.current - 1)">&lt;</a>
	<a ng-repeat="page in pages" ng-class="{'currentPage noPointer': page == data.current}" ng-click="changePage(page)">{{page}}</a>
	<a ng-if="data.current < numPages" ng-click="changePage(data.current + 1)">&gt;</a>
	<a ng-if="data.numItems != data.current" ng-click="changePage(numPages)">Last &gt;&gt;</a>
</div>