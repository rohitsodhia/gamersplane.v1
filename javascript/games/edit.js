controllers.controller('games_cu', ['$scope', '$http', 'CurrentUser', 'GamesService', 'SystemsService', function ($scope, $http, CurrentUser, GamesService, SystemsService) {
	pathElements = getPathElements();
	CurrentUser.load().then(function () {
		CurrentUser = CurrentUser.get();
		$scope.CurrentUser = CurrentUser;
		$scope.state = pathElements[2] == 'edit'?'edit':'new';
		$scope.allSystems = {};
		$scope.systemsWCharSheets = {};
		$scope.lfg = [];
		$scope.game = {
			'title': '',
			'system': '',
			'allowedCharSheets': [],
			'postFrequency': {
				'timesPer': 1,
				'perPeriod': 'd',
			},
			'numPlayers': 2,
			'charsPerPlayer': 1,
			'description': '',
			'charGenInfo': ''
		};
		$scope.charSheet = { val: {} };
		$scope.combobox = { 'periods': [{ 'value': 'd', 'display': 'day' }, { 'value': 'w', 'display': 'week' }] };
		$scope.errors = [];

		SystemsService.get({ 'getAll': true, 'fields': ['hasCharSheet'] }).then(function (data) {
			$scope.allSystems = {};
			$scope.systemsWCharSheets = {};
			data.systems.forEach(function (val) {
				$scope.allSystems[val.shortName] = val.fullName;
				if (val.hasCharSheet) 
					$scope.systemsWCharSheets[val.shortName] = val.fullName;
			});
			if ($scope.state == 'new') {
				GamesService.getLFG(10).then(function (data) {
					$scope.lfg = [];
					data.forEach(function (val) {
						$scope.lfg.push(val);
					});
				});
			} else {
				$scope.game.gameID = parseInt(pathElements[1]);
				GamesService.getDetails($scope.game.gameID).then(function (data) {
					$scope.game = data.details;
					$scope.game.allowedCharSheets.forEach(function (val) {
						delete $scope.systemsWCharSheets[val];
					});
					if ($scope.game.description == 'None Provided') 
						$scope.game.description = '';
					if ($scope.game.charGenInfo == 'None Provided') 
						$scope.game.charGenInfo = '';
				});
			}

		});

		$scope.addCharSheet = function () {
			if ($scope.errors.indexOf('noCharSheets') >= 0) 
				removeEle($scope.errors, 'noCharSheets');
			$scope.game.allowedCharSheets.push($scope.charSheet.val);
			delete $scope.systemsWCharSheets[$scope.charSheet.val];
		};

		$scope.removeCharSheet = function (system) {
			console.log(system);
			key = $scope.game.allowedCharSheets.indexOf(system);
			if (key > -1) {
				$scope.game.allowedCharSheets.splice(key, 1);
				$scope.systemsWCharSheets[system] = $scope.allSystems[system];
			}
		};

		$scope.validateTitle = function () {
			if ($scope.game.title.length > 0 && $scope.errors.indexOf('invalidTitle') >= 0) 
				removeEle($scope.errors, 'invalidTitle');
			else if ($scope.game.title.length == 0) 
				$scope.errors.push('invalidTitle');
		}

		$scope.save = function () {
			$scope.validateTitle();
			if ($scope.state == 'new' && $scope.game.allowedCharSheets.length == 0) 
				$scope.errors.push('noCharSheets');
			if ($scope.errors.length) 
				return;
			gameDetails = copyObject($scope.game);
			if ($scope.state == 'new') {
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
	});
}]);