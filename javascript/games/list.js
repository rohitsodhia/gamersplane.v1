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
	$scope.filter = { 'orderBy': 'createdOn_d', 'showFullGames': false, 'systems': [] };
	$scope.orderBy = '-start';
	var reqLoading = 2;
	CurrentUser.load().then(function () {
		SystemsService.get({ 'getAll': true }).then(function (data) {
			reqLoading = $scope.clearPageLoading(reqLoading);
			$scope.systems = {};
			data.systems.forEach(function (val) {
				$scope.systems[val.shortName] = val.fullName;
			});

			GamesService.getGames().then(function (data) {
				reqLoading = $scope.clearPageLoading(reqLoading);
				$scope.games = data;
				$scope.games.forEach(function (game) {
					game.system = $scope.systems[game.system];
					game.lastActivity = UsersService.inactive(game.lastActivity);
				});
				equalizeHeights();
			});
		});
		$scope.toggleShowFullGames = function () {
			$scope.filter.showFullGames = !$scope.filter.showFullGames;
		};
		$scope.clearSystems = function () {
			$scope.filter.systems = [];
		};
		$scope.filterGames = function () {
			$scope.$emit('pageLoading');
			var filter = copyObject($scope.filter);
			$scope.orderBy = filter.orderBy.value.slice(-1) == 'd'?'-':'';
			if (filter.orderBy.value.slice(0, -2) == 'createdOn') 
				$scope.orderBy += 'start';
			else if (filter.orderBy.value.slice(0, -2) == 'name')
				$scope.orderBy += 'title';
			else if (filter.orderBy.value == 'system')
				$scope.orderBy += filter.orderBy.value;
			console.log($scope.orderBy);
			if (filter.systems.length == 0) 
				filter.systems = null;
			$scope.games = [];
			GamesService.getGames({ 'systems': filter.systems, 'showFullGames': filter.showFullGames }).then(function (data) {
				$scope.$emit('pageLoading');
				$scope.games = data;
				$scope.games.forEach(function (game) {
					game.system = $scope.systems[game.system];
					game.lastActivity = UsersService.inactive(game.lastActivity);
				});
				equalizeHeights();
			});
		};
	});
}]);