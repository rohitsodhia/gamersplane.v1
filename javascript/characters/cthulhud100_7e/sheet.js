controllers.controller('viewCharacter_cthulhud100_7e', ['$scope', '$http', '$sce', '$timeout', 'CurrentUser', 'CharactersService', 'Range', function ($scope, $http, $sce, $timeout, CurrentUser, CharactersService, Range) {
	CurrentUser.load().then(function () {
		$scope.range = Range.get;
		$scope.loadChar();
	});
}]);