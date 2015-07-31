app.directive('gamersList', ['$scope', '$http', function ($scope, $http) {
	$scope.users = {};
	$http.post(API_HOST + '/users/list/', { page: $scope.pagination.current }).success(function (data) {

		$scope.pagination.numItems = Math.ceil(data.totalUsers / 20);
		$scope.pagination.pages = [];
		for (count = $scope.pagination.numItems - 2 > 0?$scope.pagination.numItems - 2:1; count <= $scope.pagination.numItems + 2 && count <= $scope.pagination.numItems; count++) {
			$scope.pagination.pages.push(count);
		}
	});
	$scope.pagination = {};
	if ($.urlParam('page')) 
		$scope.pagination.current = parseInt($.urlParam('page'));
	else 
		$scope.pagination.current = 1;
	$scope.showPagination = true;
}]);