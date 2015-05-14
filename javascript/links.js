controllers.controller('links', function ($scope, $http, $sce, $filter) {
	$http.post(API_HOST + '/links/list/', { level: 'Partner' }).success(function (data) {
		$scope.links.partners = data.links;

		$scope.pagination.partners.numItems = Math.ceil(data.numSystems / 10);
		$scope.pagination.partners.pages = new Array();
		for (count = $scope.pagination.partners.numItems - 2 > 0?$scope.pagination.partners.numItems - 2:1; count <= $scope.pagination.partners.numItems + 2 && count <= $scope.pagination.partners.numItems; count++) {
			$scope.pagination.partners.pages.push(count);
		}
	});
	$http.post(API_HOST + '/links/list/', { networks: 'rpga' }).success(function (data) {
		$scope.links.rpgan = data.links;

		$scope.pagination.rpgan.numItems = Math.ceil(data.numSystems / 10);
		$scope.pagination.rpgan.pages = new Array();
		for (count = $scope.pagination.rpgan.numItems - 2 > 0?$scope.pagination.rpgan.numItems - 2:1; count <= $scope.pagination.rpgan.numItems + 2 && count <= $scope.pagination.rpgan.numItems; count++) {
			$scope.pagination.rpgan.pages.push(count);
		}
	});
	$http.post(API_HOST + '/links/list/', { level: 'Affiliate' }).success(function (data) {
		$scope.links.affiliates = data.links;

		$scope.pagination.affiliates.numItems = Math.ceil(data.numSystems / 10);
		$scope.pagination.affiliates.pages = new Array();
		for (count = $scope.pagination.affiliates.numItems - 2 > 0?$scope.pagination.affiliates.numItems - 2:1; count <= $scope.pagination.affiliates.numItems + 2 && count <= $scope.pagination.affiliates.numItems; count++) {
			$scope.pagination.affiliates.pages.push(count);
		}
	});
	$http.post(API_HOST + '/links/list/', { level: 'Link' }).success(function (data) {
		$scope.links.links = data.links;

		$scope.pagination.links.numItems = Math.ceil(data.numSystems / 10);
		$scope.pagination.links.pages = new Array();
		for (count = $scope.pagination.links.numItems - 2 > 0?$scope.pagination.links.numItems - 2:1; count <= $scope.pagination.links.numItems + 2 && count <= $scope.pagination.links.numItems; count++) {
			$scope.pagination.links.pages.push(count);
		}
	});
	$scope.pagination = {'partners': {}, 'rpgan': {}, 'affiliates': {}, 'links': {}};
	for (key in $scope.pagination) 
		$scope.pagination[key].showPagination = true;
	$scope.links = [];
	$scope.categories = [
		{ 'slug': 'blog', 'label': 'Blog' },
		{ 'slug': 'podcast', 'label': 'Podcast' },
		{ 'slug': 'videocast', 'label': 'Videocast' },
		{ 'slug': 'liveplay', 'label': 'Liveplay' },
		{ 'slug': 'dev', 'label': 'Devs' },
		{ 'slug': 'accessories', 'label': 'Accessories' }
	];
	$scope.filter = { 'blog': true, 'podcast': true, 'videocast': true, 'liveplay': true, 'dev': true, 'accessories': true };

	$scope.maxHeight = {
		'partners': 0,
		'rpgan': 0,
		'affiliates': 0,
		'links': 0
	}
});