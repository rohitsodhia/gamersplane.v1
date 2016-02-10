controllers.controller('editCharacter_cthulhud100_7e', ['$scope', '$http', '$q', '$sce', '$timeout', 'CurrentUser', 'CharactersService', 'ACSearch', 'Range', function ($scope, $http, $q, $sce, $timeout, CurrentUser, CharactersService, ACSearch, Range) {
	CurrentUser.load().then(function () {
		$scope.range = Range.get;
		$scope.labels = {
			'stats': [
				{ 'key': 'hp', 'value': 'Hit Points' },
				{ 'key': 'sanity', 'value': 'Sanity' },
				{ 'key': 'mp', 'value': 'Magic Points' },
				{ 'key': 'luck', 'value': 'Luck' }
			]
		};
		blanks = {
			'skills': { 'name': '', 'value': 0 },
			'items': { 'name': '', 'notes': '' },
			'weapons': { 'name': '', 'regular': 0, 'hard': 0, 'extreme': 0, 'damage': '', 'range': '', 'attacks': 1, 'ammo': '', 'malf': '' }
		};
		$scope.loadChar();
		$scope.getHalfValue = function (val) {
			return Math.floor(val / 2);
		};
		$scope.getFifthValue = function (val) {
			return Math.floor(val / 5);
		};
		$scope.columnSplitC = function (column) {
			if (typeof $scope.character == 'undefined') 
				return;
			return Math.floor($scope.character.skills.length / 3) + ($scope.character.skills.length % 3 > column?1:0);
		};
		$scope.columnSplitS = function (column) {
			if (typeof $scope.character == 'undefined') 
				return;
			else 
				return column * Math.floor($scope.character.skills.length / 3) + (column <= $scope.character.skills.length % 3?column:$scope.character.skills.length % 3);
		};
		$scope.searchSkills = function (search) {
			return ACSearch.cil('skill', search, 'cthulhud100_7e').then(function (items) {
				for (key in items) {
					systemItem = items[key].systemItem;
					items[key] = {
						'value': items[key].itemID,
						'display': items[key].name,
						'class': []
					}
					if (!systemItem) 
						items[key].class.push('nonSystemItem');
				}
				return items;
			});
		};
//		$scope.save = function () {
//			$parent.save();
//		};
	});
}]);