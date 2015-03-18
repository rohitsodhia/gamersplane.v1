<div class="paginateDiv" ng-if="showPagination">
	<div class="pageDisplay">{{pagination.current}} of {{pagination.numItems}} &bull;</div>
	<a ng-if="pagination.current != 1" ng-click="changePage(1)">&lt;&lt; First</a>
	<a ng-if="pagination.current > 1" ng-click="changePage(pagination.current - 1)">&lt;</a>
	<a ng-repeat="page in pagination.pages" ng-class="{'currentPage': page == pagination.current, 'noPointer': page == pagination.current}" ng-click="changePage(page)">{{page}}</a>
	<a ng-if="pagination.current < pagination.numItems" ng-click="changePage(pagination.current + 1)">&gt;</a>
	<a ng-if="pagination.numItems != pagination.current" ng-click="changePage(pagination.numItems)">Last &gt;&gt;</a>
</div>