controllers.controller('myGames', ['$scope', '$filter', 'CurrentUser', 'GamesService', function ($scope, $filter, CurrentUser, GamesService) {
	$scope.$emit('pageLoading');
	$scope.games = [];
	$scope.inGames = { 'notGM': false, 'gm': false };
	CurrentUser.load().then(function () {
		GamesService.get({ my: true }).then(function (data) {
			$scope.$emit('pageLoading');
			$scope.games = data.games;
			if ($scope.games.length > 0) {
				if ($filter('filter')($scope.games, { 'isGM': false }).length) 
					$scope.inGames.notGM = true;
				if ($filter('filter')($scope.games, { 'isGM': true }).length) 
					$scope.inGames.gm = true;
			}
		});
	})
}]);
