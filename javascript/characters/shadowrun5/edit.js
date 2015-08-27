controllers.controller('editCharacter_shadowrun5', ['$scope', '$http', '$sce', '$timeout', 'currentUser', 'character', 'range', function ($scope, $http, $sce, $timeout, currentUser, character, range) {
	currentUser.then(function (currentUser) {
		pathElements = getPathElements();
		$scope.range = range.get;
		$scope.character = {};
		$scope.blanks = {
			'skills': { 'name': '', 'rating': 1, 'type': 'a' },
			'qualities': { 'name': '', 'notes': '', 'type': 'p' },
			'contacts': { 'name': '', 'loyalty': 0, 'connection': 0, 'notes': '' },
			'weapons.ranged': { 'name': '', 'damage': '', 'acc': 0, 'ap': 0, 'mode': '', 'rc': '', 'ammo': '', 'notes': '' },
			'weapons.melee': { 'name': '', 'reach': 0, 'damage': '', 'acc': 0, 'ap': 0, 'notes': '' },
			'armor': { 'name': '', 'rating': 0, 'notes': '' },
			'programs': { 'name': '', 'notes': '' },
			'augmentations': { 'name': '', 'rating': 0, 'notes': '', 'essence': 0 },
			'sprcf': { 'name': '', 'tt': '', 'range': 0, 'duration': 0, 'drain': '', 'notes': '' },
			'powers': { 'name': '', 'rating': 0, 'notes': '' },
			'gear': { 'name': '', 'rating': 0, 'notes': '' }
		};
		character.load(pathElements[2]).then(function (data) {
			$scope.character = copyObject(data);
			character.loadBlanks($scope.character, blanks);
		});
		$scope.addItem = function (key) {
			$scope.character[key].push(copyObject($scope.blanks[key]));
		};
		$scope.save = function () {
			character.save($scope.character.characterID, $scope.character).then(function (data) {
				if (data.saved) 
					window.location = '/characters/' + $scope.character.system + '/' + data.characterID;
			});
		};
	});
}]);