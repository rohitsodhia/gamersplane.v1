controllers.controller('games_cu', ['$scope', '$http', 'CurrentUser', 'GamesService', 'SystemsService', function ($scope, $http, CurrentUser, GamesService, SystemsService) {
	pathElements = getPathElements();
	CurrentUser.load().then(function () {
		CurrentUser = CurrentUser.get();
		$scope.CurrentUser = CurrentUser;
		$scope.state = pathElements[2] == 'edit'?'edit':'new';
		$scope.systems = {};
		$scope.lfg = [];
		$scope.game = {
			'title': '',
			'system': '',
			'timesPer': 1,
			'perPeriod': 'd',
			'numPlayers': 2,
			'charsPerPlayer': 1,
			'description': '',
			'charGenInfo': ''
		};
		$scope.addCharSheet = {};
		$scope.combobox = { 'periods': [{ 'value': 'd', 'display': 'day' }, { 'value': 'w', 'display': 'week' }] };
		$scope.errors = [];

		if ($scope.state == 'new') {
			SystemsService.get({ 'getAll': true, 'basic': true }).then(function (data) {
				$scope.systems = {};
				data.systems.forEach(function (val) {
					$scope.systems[val.shortName] = val.fullName;
				});
				GamesService.getLFG(10).then(function (data) {
					$scope.lfg = [];
					data.forEach(function (val) {
						$scope.lfg.push(val);
					});
				});
			});
		} else {
			$scope.game.gameID = parseInt(pathElements[1]);
			GamesService.getDetails($scope.game.gameID).then(function (data) {
				$scope.game.title = data.details.title;
				$scope.game.timesPer = data.details.postFrequency[0];
				$scope.game.perPeriod = data.details.postFrequency[1][0];
				$scope.game.numPlayers = data.details.numPlayers;
				$scope.game.charsPerPlayer = data.details.charsPerPlayer;
				$scope.game.description = data.details.description;
				if ($scope.game.description == 'None Provided') 
					$scope.game.description = '';
				$scope.game.charGenInfo = data.details.charGenInfo;
				if ($scope.game.charGenInfo == 'None Provided') 
					$scope.game.charGenInfo = '';
			});
		}
	});

	$scope.validateTitle = function () {
		if ($scope.game.title.length > 0 && $scope.errors.indexOf('invalidTitle') >= 0) 
			$scope.errors = removeEle($scope.errors, 'invalidTitle');
		else if ($scope.game.title.length == 0) 
			$scope.errors.push('invalidTitle');
	}

	$scope.save = function () {
		if ($scope.errors.length) 
			return;
		gameDetails = copyObject($scope.game);
		if (typeof gameDetails.perPeriod == 'object') 
			gameDetails.perPeriod = gameDetails.perPeriod.value;
		if ($scope.state == 'new') {
			gameDetails.system = gameDetails.system.value;
			GamesService.create(gameDetails).then(function (data) {
				if (data.success) 
					document.location.href = '/games/' + data.gameID + '/';
				else if (data.failed) 
					$scope.errors = data.errors;
			});
		} else {
			GamesService.update(gameDetails).then(function (data) {
				if (data.success) 
					document.location.href = '/games/' + data.gameID + '/';
				else if (data.failed) 
					$scope.errors = data.errors;
			});
		}
	};
}]);