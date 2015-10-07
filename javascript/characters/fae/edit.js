controllers.controller('editCharacter_fae', ['$scope', '$http', '$sce', '$timeout', 'currentUser', 'character', 'Range', function ($scope, $http, $sce, $timeout, currentUser, character, Range) {
	currentUser.then(function (currentUser) {
		pathElements = getPathElements();
		$scope.range = Range.get;
		$scope.character = {};
		blanks = {
			'aspects': { 'name': '' },
			'stunts': { 'name': '' }
		};
		character.load(pathElements[2]).then(function (data) {
			$scope.character = copyObject(data);
			for (key in $scope.character.aspects) 
				$scope.character.aspects[key] = { 'name': $scope.character.aspects[key] };
			for (key in $scope.character.stunts) 
				$scope.character.stunts[key] = { 'name': $scope.character.stunts[key] };
			character.loadBlanks($scope.character, blanks);
		});
		$scope.addItem = function (key) {
			$scope.character[key].push(copyObject($scope.blanks[key]));
		};
		$scope.setStress = function (stress) {
			if (stress >= 0 && stress <= 3) 
				$scope.character.stress = stress;
		};
		$scope.save = function () {
			character.save($scope.character.characterID, $scope.character).then(function (data) {
				if (data.saved) 
					window.location = '/characters/' + $scope.character.system + '/' + data.characterID;
			});
		};
	});
}]);