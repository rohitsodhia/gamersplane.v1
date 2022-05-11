function equalizeHeights() {
	$('#gamesList li').each(function () {
		var maxHeight = 0;
		var allSame = true;
		$(this).children().each(function () {
			if ($(this).height() > maxHeight) {
				if (maxHeight !== 0)
					allSame = false;
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
	};
	$scope.filter = { search: '' };
	$scope.pagination = { numItems: 0, itemsPerPage: 25 };
	$scope.orderBy = '-start';
	var reqLoading = 2;
	CurrentUser.load().then(function () {
		SystemsService.get({ 'getAll': true }).then(function (data) {
			reqLoading = $scope.clearPageLoading(reqLoading);
			$scope.systems = {};
			data.systems.forEach(function (val) {
				$scope.systems[val.shortName] = val.fullName;
			});

			GamesService.getGames({'systems': null,'showFullGames': true,'showInactiveGMs': true}).then(function (data) {
				reqLoading = $scope.clearPageLoading(reqLoading);
				$scope.games = data;
				$scope.games.forEach(function (game) {
					game.lastActivity = UsersService.inactive(game.lastActivity);
				});
				$scope.pagination.numItems = $scope.games.length;
			});
		});
		$scope.$watch(function () { return $scope.filter.search; }, function () {
			$scope.pagination.numItems = $filter('filter')($scope.games, { $: $scope.filter.search }).length;
			$scope.pagination.current = 1;
		});
	});
}]);
