controllers.controller('games_new', ['$scope', '$http', 'CurrentUser', 'GamesService', 'SystemsService', function ($scope, $http, CurrentUser, GamesService, SystemsService) {
	pathElements = getPathElements();
	CurrentUser.load().then(function () {
		CurrentUser = CurrentUser.get();
		$scope.CurrentUser = CurrentUser;
		$scope.state = 'new';
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
		$scope.errors = [];

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
		gameDetails.system = gameDetails.system.value;
		GamesService.create(gameDetails).then(function (data) {
			if (data.success) 
				document.location.href = '/games/' + data.gameID + '/';
			else if (data.failed) 
				$scope.errors = data.errors;
		});
	};
}]);

controllers.controller('games_edit', ['$scope', '$http', 'CurrentUser', 'GamesService', 'SystemsService', function ($scope, $http, CurrentUser, GamesService, SystemsService) {
	pathElements = getPathElements();
	CurrentUser.load().then(function () {
		CurrentUser = CurrentUser.get();
		$scope.CurrentUser = CurrentUser;
		$scope.state = 'edit';
		$scope.systems = {};
		$scope.lfg = [];
		$scope.game = {
			'gameID': pathElements[1],
			'title': '',
			'system': '',
			'timesPer': 1,
			'perPeriod': 'd',
			'numPlayers': 2,
			'charsPerPlayer': 1,
			'description': '',
			'charGenInfo': ''
		};
		$scope.errors = [];

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
		GamesService.update(gameDetails).then(function (data) {
			if (data.success) 
				document.location.href = '/games/' + data.gameID + '/';
			else if (data.failed) 
				$scope.errors = data.errors;
		});
	};

}]);