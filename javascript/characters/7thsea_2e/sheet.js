controllers.controller('viewCharacter_7thsea_2e', ['$scope', '$http', '$sce', '$timeout', 'CurrentUser', 'CharactersService', 'Range', function ($scope, $http, $sce, $timeout, CurrentUser, CharactersService, Range) {
	CurrentUser.load().then(function () {
		$scope.range = Range.get;
		blanks = {
			'stories': { 'name': '', 'goal': '', 'reward': '', 'steps': '' },
			'backgrounds': { 'name': '', 'quirk': '' },
			'advantages': { 'name': '', 'description': '' }
		};
		$scope.loadChar();
	});
}]);
