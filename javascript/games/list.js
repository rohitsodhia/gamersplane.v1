function equalizeHeights() {
	$('#gamesList li').each(function () {
		var maxHeight = 0;
		var allSame = true;
		$(this).children().each(function () {
			if ($(this).height() > maxHeight) {
				if (maxHeight != 0) allSame = false;
				maxHeight = $(this).height();
			}
		});
		if (allSame) 
			$(this).children().height(maxHeight);
	});
}

controllers.controller('listGames', ['$scope', '$filter', 'CurrentUser', 'UsersService', 'GamesService', 'SystemsService', function ($scope, $filter, CurrentUser, UsersService, GamesService, SystemsService) {
	$scope.$emit('pageLoading');
	$scope.games = [];
	$scope.systems = {};
	$scope.filterOptions = {
		'createdOn_d': 'Created on (Desc)',
		'createdOn_a': 'Created on (Asc)',
		'name_a': 'Name (Asc)',
		'name_d': 'Name (Desc)',
		'system': 'System'
	}
	$scope.filter = { 'orderBy': 'createdOn_d', 'systems': [] };
	var reqLoading = 2;
	CurrentUser.load().then(function () {
		GamesService.getGames().then(function (data) {
			reqLoading = $scope.clearPageLoading(reqLoading);
			$scope.games = data;
			$scope.games.forEach(function (element) {
				element.lastActivity = UsersService.inactive(element.lastActivity);
			});
			equalizeHeights();
		});
		SystemsService.get({ 'getAll': true, 'basic': true }).then(function (data) {
			reqLoading = $scope.clearPageLoading(reqLoading);
			$scope.systems = {};
			data.systems.forEach(function (val) {
				$scope.systems[val.shortName] = val.fullName;
			});
		});
		$scope.clearSystems = function () {
			$scope.filter.systems = [];
		};
		$scope.filterGames = function () {
			$scope.$emit('pageLoading');
			var filter = copyObject($scope.filter);
			filter.orderBy = filter.orderBy.value;
			if (filter.systems.length == 0) 
				filter.systems = null;
			$scope.games = [];
			GamesService.getGames(filter).then(function (data) {
				$scope.$emit('pageLoading');
				$scope.games = data;
				$scope.games.forEach(function (element) {
					element.lastActivity = UsersService.inactive(element.lastActivity);
				});
				equalizeHeights();
			});
		};
	});
}]);