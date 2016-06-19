controllers.controller('editCharacter_7thsea_2e', ['$scope', '$http', '$sce', '$timeout', 'CurrentUser', 'CharactersService', 'Range', function ($scope, $http, $sce, $timeout, CurrentUser, CharactersService, Range) {
	CurrentUser.load().then(function () {
		blanks = {
			'stories': { 'name': '', 'goal': '', 'reward': '', 'steps': '' }
		};
		$scope.range = Range.get;
		$scope.loadChar().then(function () {
			if ($scope.character.reputations.length === 0) {
				$scope.character.reputations.push('');
			}
		});

		$scope.addReputation = function () {
			$scope.character.reputations.push('');
		};
		$scope.setDeathSpiral = function (value) {
			$scope.character.deathSpiral = parseInt(value);
		};
	});
}]);
