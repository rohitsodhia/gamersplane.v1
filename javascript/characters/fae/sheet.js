controllers.controller('viewCharacter_fae', ['$scope', '$http', '$sce', '$timeout', 'currentUser', 'character', 'Range', function ($scope, $http, $sce, $timeout, currentUser, character, Range) {
	currentUser.then(function (currentUser) {
		pathElements = getPathElements();
		$scope.range = Range.get;
		$scope.character = {};
		character.load(pathElements[2], true).then(function (data) {
			$scope.character = copyObject(data);
			for (key in $scope.character.aspects) 
				$scope.character.aspects[key] = { 'name': $scope.character.aspects[key] };
			for (key in $scope.character.stunts) 
				$scope.character.stunts[key] = { 'name': $scope.character.stunts[key] };
		});
	});
}]);