controllers.controller('editCharacter_cthulhud100_7e', ['$scope', '$http', '$q', '$sce', '$filter', '$timeout', 'CurrentUser', 'CharactersService', 'ACSearch', function ($scope, $http, $q, $sce, $filter, $timeout, CurrentUser, CharactersService, ACSearch) {
	CurrentUser.load().then(function () {
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
		$scope.damage_build = {
			64: ['-2', '-2'],
			84: ['-1', '-1'],
			124: ['None', '0'],
			164: ['+1d4', '+1'],
			204: ['+1d6', '+2']
		};
		$scope.skillCols = [[], [], []];
		$scope.colCount = 0;
		$scope.loadChar().then(function() {
			$scope.character.dodge = Math.floor($scope.character.characteristics.dex / 2);
			$scope.character.skills = $filter('orderBy')($scope.character.skills, '+name');
			for (key in $scope.character.skills) {
				if ($scope.character.skills[key].name.toLowerCase() == 'Dodge') 
					$scope.character.dodge = $scope.character.skills[key].value;
				$scope.skillCols[$scope.colCount++].push($scope.character.skills[key]);
				if ($scope.colCount == 3) 
					$scope.colCount = 0;
			}
		});
		$scope.getHalfValue = function (val) {
			return Math.floor(val / 2);
		};
		$scope.getFifthValue = function (val) {
			return Math.floor(val / 5);
		};
		$scope.addSkill = function () {
			$scope.skillCols[$scope.colCount++].push({ 'name': '', 'value': 0 });
			if ($scope.colCount == 3) 
				$scope.colCount = 0;
		}
		$scope.searchSkills = function (search) {
			return ACSearch.cil('skill', search, 'cthulhud100_7e', true).then(function (items) {
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
		$scope.computeDamage_Build = function (val) {
//			val = $scope.characteristics.str + $scope.characteristics.siz;
			for (key in $scope.damage_build) 
				if (val <= key) 
					return $scope.damage_build[key];
		};
		$scope.save = function () {
			$scope.character.skills = $scope.skillCols[0].concat($scope.skillCols[1], $scope.skillCols[2]);
			$scope.$parent.save();
		};
	});
}]);