controllers.controller('editCharacter_cthulhu_brs7e', ['$scope', '$http', '$q', '$sce', '$filter', '$timeout', 'CurrentUser', 'CharactersService', 'Range', 'ACSearch', function ($scope, $http, $q, $sce, $filter, $timeout, CurrentUser, CharactersService, Range, ACSearch) {
	$scope.range = Range.get;
	CurrentUser.load().then(function () {
		$scope.labels = {
			'stats': [
				{ 'key': 'hp', 'value': 'Hit Points' },
				{ 'key': 'sanity', 'value': 'Sanity' },
				{ 'key': 'mp', 'value': 'Magic Points' }
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
		$scope.numCols = 3;
		$scope.loadChar().then(function() {
			$scope.character.dodge = Math.floor($scope.character.characteristics.dex / 2);
			$scope.character.skills = $filter('orderBy')($scope.character.skills, '+name');
		});
		$scope.getHalfValue = function (val) {
			return Math.floor(val / 2);
		};
		$scope.getFifthValue = function (val) {
			return Math.floor(val / 5);
		};
		$scope.addSkill = function () {
			$scope.character.skills.push({ 'name': '', 'value': 0 });
		};
		$scope.changeSkillName = function (skill, name) {
			skill.name = name;
			ACSearch.cil('skill', name, 'cthulhu_brs7e', true).then(function (items) {
				for (var key in items) {
					systemItem = items[key].systemItem;
					items[key] = {
						'value': items[key].itemID,
						'display': items[key].name,
						'class': []
					};
					if (!systemItem)
						items[key].class.push('nonSystemItem');
				}
				skill.search = items;
			});
		};
		$scope.computeDamage_Build = function (val) {
//			val = $scope.characteristics.str + $scope.characteristics.siz;
			for (var key in $scope.damage_build)
				if (val <= key)
					return $scope.damage_build[key];
		};
		$scope.save = function () {
			$scope.$parent.save();
		};
	});
}]);
