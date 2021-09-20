controllers.controller('games_cu', ['$scope', '$http', '$filter', 'CurrentUser', 'GamesService', 'SystemsService', function ($scope, $http, $filter, CurrentUser, GamesService, SystemsService) {
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
		$scope.charSheet = null;
		$scope.combobox = {
			'periods': [
				{ 'value': 'd', 'display': 'day' },
				{ 'value': 'w', 'display': 'week' }
			]
		};
		$scope.errors = [];

		SystemsService.get(
			{
				'getAll': true,
				'fields': ['hasCharSheet']
			}
		).then(function (data) {
			$scope.allSystems = {};
			$scope.systemsWCharSheets = {};
			data.systems.forEach(function (val) {
				$scope.allSystems[val.shortName] = val.fullName;
				if (val.hasCharSheet) {
					$scope.systemsWCharSheets[val.shortName] = val.fullName;
				}
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
					if ($scope.game.description == 'None Provided') {
						$scope.game.description = '';
					}
					if ($scope.game.charGenInfo == 'None Provided') {
						$scope.game.charGenInfo = '';
					}
				});
			}

		});

		$scope.setSystem = function (system) {
			$scope.game.system = system;
		};

		$scope.setCharSheet = function (system) {
			$scope.charSheet = system;
		};
		$scope.addCharSheet = function () {
			if ($scope.errors.indexOf('noCharSheets') >= 0) {
				removeEle($scope.errors, 'noCharSheets');
			}
			$scope.game.allowedCharSheets.push($scope.charSheet);
			delete $scope.systemsWCharSheets[$scope.charSheet];
		};
		$scope.removeCharSheet = function (system) {
			key = $scope.game.allowedCharSheets.indexOf(system);
			if (key > -1) {
				$scope.game.allowedCharSheets.splice(key, 1);
				var hold = {},
					inserted = false,
					compSystemName = $scope.allSystems[system].toLowerCase();
				angular.forEach($scope.systemsWCharSheets, function (fullName, shortName) {
					if (!inserted && fullName.toLowerCase() > compSystemName) {
						this[system] = $scope.allSystems[system];
					}
					this[shortName] = fullName;
				}, hold);
				$scope.systemsWCharSheets = hold;
			}
		};

		$scope.validateTitle = function () {
			if ($scope.game.title.length > 0 && $scope.errors.indexOf('invalidTitle') >= 0) {
				removeEle($scope.errors, 'invalidTitle');
			} else if ($scope.game.title.length === 0) {
				$scope.errors.push('invalidTitle');
			}
		};

		$scope.setPeriod = function (period) {
			$scope.game.postFrequency.perPeriod = period;
		};

		$scope.save = function () {
			$scope.validateTitle();
			if ($scope.state == 'new' && $scope.game.allowedCharSheets.length === 0) {
				$scope.errors.push('noCharSheets');
			}
			if ($scope.errors.length) {
				return;
			}
			gameDetails = copyObject($scope.game);
			$scope.$emit('pageLoading');
			if ($scope.state == 'new') {
				GamesService.create(gameDetails).then(function (data) {
					if (data.success) {
						document.location.href = '/games/' + data.gameID + '/';
					} else if (data.failed) {
						$scope.errors = data.errors;
					}
					$scope.$emit('pageLoading');
				});
			} else {
				GamesService.update(gameDetails).then(function (data) {
					if (data.success) {
						document.location.href = '/games/' + data.gameID + '/';
					} else if (data.failed) {
						$scope.errors = data.errors;
					}
					$scope.$emit('pageLoading');
				});
			}
		};
	});
}]);

$(function () {

	var isValidJson=function (str) {
		try {
			str= str.replace(/[‘’]/g, "'").replace(/[“”]/g, '"');
			JSON.parse(str);
		} catch (e) {
			return false;
		}
		return true;
	}	

	$('#gameOptions').on('change keyup blur',function(){
		$('#gameOptionsError').hide();
		var testJson=$.trim($('#gameOptions').val());
		if(testJson!='' && !isValidJson(testJson)){
			$('#gameOptionsError').show();
		}
	});
	
});