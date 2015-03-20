controllers.controller('systems', function ($scope, $http, $sce, $filter) {
	$http.post(API_HOST + '/systems/search/', { getAll: true, excludeCustom: true }).success(function (data) {
		$scope.systems = data.systems;

		$scope.pagination.numItems = Math.ceil(data.numSystems / 10);
		$scope.pagination.pages = new Array();
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

	$scope.wrapPublisher = function (system) {
		return system.publisher.site?'<a href="' + system.publisher.site + '" target="_blank">' + system.publisher.name + '</a>':system.publisher.name;
	}

	$scope.changePage = function (page) {
		page = parseInt(page);
		if (page < 0 && page > $scope.pagination.numItems) 
			page = 1;
		$scope.pagination.current = page;
	}
	$scope.adjustPagination = function () {
		$scope.pagination.current = 1;
		$scope.pagination.numItems = Math.ceil($filter('filter')($scope.systems, $scope.filter.fullName).length / 10);
		$scope.pagination.pages = new Array();
		for (count = $scope.pagination.numItems - 2 > 0?$scope.pagination.numItems - 2:1; count <= $scope.pagination.numItems + 2 && count <= $scope.pagination.numItems; count++) {
			$scope.pagination.pages.push(count);
		}
		$scope.showPagination = $scope.pagination.numItems > 0?true:false;
	}
});