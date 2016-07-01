controllers.controller('editCharacter_7thsea_2e', ['$scope', '$http', '$sce', '$timeout', 'CurrentUser', 'CharactersService', 'Range', function ($scope, $http, $sce, $timeout, CurrentUser, CharactersService, Range) {
	CurrentUser.load().then(function () {
		blanks = {
			'stories': { 'name': '', 'goal': '', 'reward': '', 'steps': '' },
			'backgrounds': { 'name': '', 'quirk': '' },
			'advantages': { 'name': '', 'description': '' }
		};
		$scope.range = Range.get;
		$scope.loadChar().then(function () {
			if ($scope.character.reputations.length === 0) {
				$scope.addReputation();
			}
		});

		$scope.addReputation = function () {
			$scope.character.reputations.push('');
		};
		$scope.setDeathSpiral = function (value) {
			value = parseInt(value);
			if (value > $scope.character.deathSpiral)
				for (count = 1; count <= Math.floor(value / 5); count++)
					$scope.character.dramaticWounds[count] = true;
			$scope.character.deathSpiral = parseInt(value);
		};
	});
}]);
