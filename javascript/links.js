controllers.controller('links', function ($scope, $http, $sce, $filter) {
	$http.post(API_HOST + '/links/list/', {}).success(function (data) {
		$scope.links = data.links;

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
	$scope.links = [];
	$scope.categories = [
		{ 'slug': 'blog', 'label': 'Blog' },
		{ 'slug': 'podcast', 'label': 'Podcast' },
		{ 'slug': 'videocast', 'label': 'Videocast' },
		{ 'slug': 'liveplay', 'label': 'Liveplay' },
		{ 'slug': 'accessories', 'label': 'Accessories' }
	];
	$scope.filter = { 'blog': true, 'podcast': true, 'videocast': true, 'liveplay': true };

	$scope.maxHeight = {
		'partners': 0,
		'rpgan': 0,
		'affiliates': 0,
		'links': 0
	}
});