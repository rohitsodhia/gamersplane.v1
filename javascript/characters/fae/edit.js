controllers.controller('editCharacter_fae', ['$scope', '$http', '$sce', '$timeout', 'CurrentUser', 'CharactersService', 'Range', function ($scope, $http, $sce, $timeout, CurrentUser, CharactersService, Range) {
	CurrentUser.load().then(function () {
		$scope.range = Range.get;
		blanks = {
			'aspects': { 'name': '' },
			'stunts': { 'name': '' }
		};
		$scope.loadChar().then(function () {
			console.log($scope.character.aspects);
			if ($scope.character.aspects.length > 1 || typeof $scope.character.aspects[0] == 'string') {
				for (key in $scope.character.aspects) {
					$scope.character.aspects[key] = { 'name': $scope.character.aspects[key] };
				}
			}
			if ($scope.character.stunts.length > 1 || typeof $scope.character.stunts[0] == 'string') {
				for (key in $scope.character.stunts) {
					$scope.character.stunts[key] = { 'name': $scope.character.stunts[key] };
				}
			}
		});
	});
	$scope.setStress = function (stress) {
		if (stress >= 0 && stress <= 3) {
			$scope.character.stress = stress;
		}
	};
}]);
