controllers.controller('systems', ['$scope', '$http', '$sce', '$filter', 'SystemsService', function ($scope, $http, $sce, $filter, SystemsService) {
	$scope.$emit('pageLoading');
	SystemsService.get().then(function (data) {
		$scope.$emit('pageLoading');
		$scope.systems = data.systems;
		$scope.numSystems = data.num_systems;
		$scope.pagination.numItems = data.num_systems;
	});
	$scope.pagination = { numItems: 0, itemsPerPage: 10 };
	if ($.urlParam('page'))
		$scope.pagination.current = parseInt($.urlParam('page'));
	else
		$scope.pagination.current = 1;
	$scope.systems = [];
	$scope.filter = { search: '' };
	$scope.num_systems = 0;

	$scope.wrapPublisher = function (system) {
		return system.publisher.site ? '<a href="' + system.publisher.site + '" target="_blank">' + system.publisher.name + '</a>' : system.publisher.name;
	};

	$scope.$watch(function () { return $scope.filter.search; }, function () {
		$scope.num_systems = $filter('filter')($scope.systems, { 'fullName': $scope.filter.search }).length;
		$scope.pagination.numItems = $filter('filter')($scope.systems, { 'fullName': $scope.filter.search }).length;
		$scope.pagination.current = 1;
	});
}]);
