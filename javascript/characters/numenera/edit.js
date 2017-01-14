controllers.controller('editCharacter_numenera', ['$scope', '$timeout', 'CurrentUser', 'CharactersService', 'ACSearch', function ($scope, $timeout, CurrentUser, CharactersService, ACSearch) {
	CurrentUser.load().then(function () {
		$scope.labels = {
			'stats': { 'tier': 'Tier', 'effort': 'Effort', 'xp': 'XP' },
			'attributes': ['might', 'speed', 'intellect'],
			'recoveries': { 'action': 'Action', 'ten_min': '10 Minutes', 'hour': 'Hour', 'ten_hours': '10 Hours' }
		};
		blanks = {
			'attacks': { 'name': '', 'mod': 0, 'dmg': 0 },
			'skills': { 'name': '', 'attr': 'm', 'prof': '' },
			'specialAbilities': { 'name': '', 'notes': '' },
			'cyphers': { 'name': '', 'notes': '' }
		};
		$scope.loadChar();
	});

	var cycleVars = {
		'attrs': ['m', 's', 'i'],
		'profs': ['', 't', 's']
	};
	$scope.cycleValues = function (field, attrObj, list) {
		var cur = cycleVars[list].indexOf(attrObj[field]),
			next = 0;
		if (cur != cycleVars[list].length - 1) {
			next = cur + 1;
		}
		attrObj[field] = cycleVars[list][next];
	};
}]);
