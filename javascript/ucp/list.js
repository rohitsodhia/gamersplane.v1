app.controller('gamersList', ['$scope', '$http', '$sce', '$filter', function ($scope, $http, $sce, $filter) {
	$scope.users = {};
	$scope.loading = true;
	$scope.pagination = { numItems: 0, itemsPerPage: 25 };
	$scope.filter = { search: '' };

	$scope.getGamers = function () {
		$scope.$emit('pageLoading');
		$http.post(API_HOST + '/users/gamersList/', { 'page': $scope.pagination.current, 'showInactive': $scope.showInactive }).success(function (data) {
			$scope.users = data.users;
			$scope.pagination.numItems = data.totalUsers;
			$scope.$emit('pageLoading');
		});
	}
	$scope.showInactive = false;
	$scope.$watch(function () { return $scope.showInactive; }, function (val) {
		$scope.getGamers();
	});
	$scope.$watch(function () { return $scope.filter.search; }, function () {
		$scope.pagination.numItems = $filter('filter')($scope.users, { $: $scope.filter.search }).length;
		$scope.pagination.current = 1;
	});

	if ($.urlParam('page'))
		$scope.pagination.current = parseInt($.urlParam('page'));
	else
		$scope.pagination.current = 1;
}]);