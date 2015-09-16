app.controller('gamersList', ['$scope', '$http', '$sce', function ($scope, $http, $sce) {
	$scope.users = {};
	$scope.loading = true;
	$scope.getGamers = function () {
		$scope.$emit('pageLoading');
		$http.post(API_HOST + '/users/gamersList/', { 'page': $scope.pagination.current, 'showInactive': $scope.showInactive }).success(function (data) {
			$scope.users = data.users;
			$scope.pagination.numItems = data.totalUsers;
			$scope.$emit('pageLoading');
		});
	}
	$scope.pagination = { numItems: 0, itemsPerPage: 25 };
	$scope.showInactive = false;
	$scope.$watch(function () { return $scope.showInactive; }, function (val) {
		$scope.getGamers();
	})
	if ($.urlParam('page')) 
		$scope.pagination.current = parseInt($.urlParam('page'));
	else 
		$scope.pagination.current = 1;
}]);