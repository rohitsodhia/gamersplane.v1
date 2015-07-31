controllers.controller('systems', ['$scope', '$http', '$sce', '$filter', function ($scope, $http, $sce, $filter, $timeout) {
	$http.post(API_HOST + '/systems/search/', { getAll: true }).success(function (data) {
		$scope.systems = data.systems;
		$scope.numSystems = data.numSystems;
		$scope.pagination.numItems = data.numSystems;
	});
	$scope.pagination = { numItems: 0 };
	if ($.urlParam('page')) 
		$scope.pagination.current = parseInt($.urlParam('page'));
	else 
		$scope.pagination.current = 1;
	$scope.systems = [];
	$scope.filter = { search: '' };
	$scope.numSystems = 0;

	$scope.wrapPublisher = function (system) {
		return system.publisher.site?'<a href="' + system.publisher.site + '" target="_blank">' + system.publisher.name + '</a>':system.publisher.name;
	}

	$scope.adjustPagination = function () {
		$scope.numSystems = $filter('filter')($scope.systems, { 'fullName': $scope.filter.search }).length;

		$scope.pagination.current = 1;
		$scope.pagination.numItems = Math.ceil($filter('filter')($scope.systems, { 'fullName': $scope.filter.search }).length / 10);
	}
}]);