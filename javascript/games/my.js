controllers.controller('myGames', ['$scope', '$filter', 'CurrentUser', 'GamesService', 'SystemsService', function ($scope, $filter, CurrentUser, GamesService, SystemsService) {
	$scope.$emit('pageLoading');
	$scope.games = [];
	$scope.inGames = { 'notGM': false, 'gm': false };
	$scope.editLFG = false;
	$scope.lfg = [];
	$scope.systems = {};
	CurrentUser.load().then(function () {
		SystemsService.get({ 'getAll': true }).then(function (data) {
			$scope.systems = {};
			data.systems.forEach(function (val) {
				$scope.systems[val.shortName] = val.fullName;
			});
			CurrentUser.getLFG().then(function (data) {
				$scope.lfg = [];
				data.forEach(function (val) {
					$scope.lfg.push($scope.systems[val]);
				});
			});

			GamesService.getGames({ my: true }).then(function (data) {
				$scope.$emit('pageLoading');
				$scope.games = data;
				$scope.games.forEach(function (game) {
					game.system = $scope.systems[game.system];
				});
				if ($scope.games.length > 0) {
					if ($filter('filter')($scope.games, { 'isGM': false }).length) 
						$scope.inGames.notGM = true;
					if ($filter('filter')($scope.games, { 'isGM': true }).length) 
						$scope.inGames.gm = true;
				}
			});
		});
		$scope.saveLFG = function () {
			$scope.editLFG = false;
			CurrentUser.saveLFG($scope.lfg);
		};
	});
}]);