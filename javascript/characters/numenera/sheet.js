controllers.controller('viewCharacter_numenera', ['$scope', 'CurrentUser', 'CharactersService', function ($scope, CurrentUser, CharactersService) {
	CurrentUser.load().then(function () {
		$scope.labels = {
			'stats': { 'tier': 'Tier', 'effort': 'Effort', 'xp': 'XP' },
			'attributes': ['might', 'speed', 'intellect'],
			'recoveries': { 'action': 'Action', 'ten_min': '10 Minutes', 'hour': 'Hour', 'ten_hours': '10 Hours' }
		};
		$scope.loadChar();
	});
}]);
