controllers.controller('viewCharacter_fae', ['$scope', '$http', '$sce', '$timeout', 'CurrentUser', 'character', 'Range', function ($scope, $http, $sce, $timeout, CurrentUser, character, Range) {
	CurrentUser.load().then(function () {
		$scope.range = Range.get;
		$scope.loadChar().then(function (data) {
			for (key in $scope.character.aspects) 
				$scope.character.aspects[key] = { 'name': $scope.character.aspects[key] };
			for (key in $scope.character.stunts) 
				$scope.character.stunts[key] = { 'name': $scope.character.stunts[key] };
		});
	});
}]);