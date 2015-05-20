controllers.controller('links', function ($scope, $http, $sce, $filter) {
	$scope.links = [];
	$http.post(API_HOST + '/links/list/').success(function (data) {
		$scope.links.partners = $filter('filter')(data.links, { 'level': 'Partner' });
		$scope.links.rpgan = $filter('filter')(data.links, { 'networks': 'rpga' });
		$scope.links.affiliates = $filter('filter')(data.links, { 'level': 'Affiliate' });
		$scope.links.links = $filter('filter')(data.links, { 'level': 'Link' });
		console.log(data.links);
	});
	$scope.categories = [
		{ 'slug': 'blog', 'label': 'Blog' },
		{ 'slug': 'podcast', 'label': 'Podcast' },
		{ 'slug': 'videocast', 'label': 'Videocast' },
		{ 'slug': 'liveplay', 'label': 'Liveplay' },
		{ 'slug': 'dev', 'label': 'Devs' },
		{ 'slug': 'accessories', 'label': 'Accessories' }
	];
	$scope.filter = {};
	for (key in $scope.categories) 
		$scope.filter[$scope.categories[key].slug] = true;

	$scope.maxHeight = {
		'partners': 0,
		'rpgan': 0,
		'affiliates': 0,
		'links': 0
	}
});