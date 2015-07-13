controllers.controller('links', function ($scope, $http, $sce, $filter) {
	$scope.links = [];
	$http.post(API_HOST + '/links/list/').success(function (data) {
		$scope.links.partners = $filter('filter')(data.links, { 'level': 'Partner' });
		$scope.links.rpgan = $filter('filter')(data.links, { 'networks': 'rpga' });
		$scope.links.affiliates = $filter('filter')(data.links, { 'level': 'Affiliate' });
		$scope.links.links = $filter('filter')(data.links, { 'level': 'Link' });
	});
	$scope.categories = [ 'Blog', 'Podcast', 'Videocast', 'Liveplay', 'Devs', 'Accessories' ];
	$scope.filter = [];

	$scope.maxHeight = {
		'partners': 0,
		'rpgan': 0,
		'affiliates': 0,
		'links': 0
	}
});