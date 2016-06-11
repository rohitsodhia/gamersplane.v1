controllers.controller('editCharacter_shadowrun5', ['$scope', '$http', '$q', '$sce', '$timeout', 'CurrentUser', 'CharactersService', 'ACSearch', 'Range', function ($scope, $http, $q, $sce, $timeout, CurrentUser, CharactersService, ACSearch, Range) {
	CurrentUser.load().then(function () {
		$scope.range = Range.get;
		$scope.loadChar();
	});
});
