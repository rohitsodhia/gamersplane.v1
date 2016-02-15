controllers.controller('viewCharacter_cthulhu_brs7e', ['$scope', '$http', '$sce', '$timeout', '$filter', 'CurrentUser', 'CharactersService', function ($scope, $http, $sce, $timeout, $filter, CurrentUser, CharactersService) {
	CurrentUser.load().then(function () {
		$scope.labels = {
			'stats': [
				{ 'key': 'hp', 'value': 'Hit Points' },
				{ 'key': 'sanity', 'value': 'Sanity' },
				{ 'key': 'mp', 'value': 'Magic Points' },
				{ 'key': 'luck', 'value': 'Luck' }
			]
		};
		$scope.damage_build = {
			64: ['-2', '-2'],
			84: ['-1', '-1'],
			124: ['None', '0'],
			164: ['+1d4', '+1'],
			204: ['+1d6', '+2']
		};
		$scope.defaultSkills = {
			'Accounting': 5,
			'Anthropolgy': 1,
			'Appraise': 5,
			'Archaeology': 1,
			'Art/Craft': 5,
			'Charm': 15,
			'Climb': 20,
			'Disguise': 5,
			'Drive Auto': 20,
			'Elec Repair': 10,
			'Fast Talk': 5,
			'Fighting (Brawl)': 25,
			'Firearms (Handgun)': 20,
			'Firearms (Rifle/Shotgun)': 25,
			'First Aid': 30,
			'History': 5,
			'Intimidate': 15,
			'Jump': 20,
			'Language (Other)': 1,
			'Law': 5,
			'Library Use': 20,
			'Listen': 20,
			'Locksmith': 1,
			'Mech Repair': 10,
			'Medicine': 1,
			'Natural Wonder': 10,
			'Navigate': 10,
			'Occult': 5,
			'Operate Heavy Machine': 1,
			'Persuade': 10,
			'Pilot': 1,
			'Psychology': 10,
			'Psychoanalysis': 1,
			'Ride': 5,
			'Science': 1,
			'Sleight of Hand': 10,
			'Spot Hidden': 25,
			'Stealth': 20,
			'Survival': 10,
			'Swim': 20,
			'Throw': 20,
			'Track': 10
		};
		$scope.skillCols = [[], [], []];
		var lastPos = 0;
		$scope.loadChar().then(function() {
			$scope.character.dodge = Math.floor($scope.character.characteristics.dex / 2);
			skillList = [];
			for (key in $scope.character.skills) {
				skillList.push($scope.character.skills[key].name);
				$scope.character.skills[key].default = false;
				if ($scope.character.skills[key].name.toLowerCase() == 'Dodge') 
					$scope.character.dodge = $scope.character.skills[key].value;
			}
			for (key in $scope.defaultSkills) 
				if (skillList.indexOf(key) == -1) 
					$scope.character.skills.push({ 'name': key, 'value': $scope.defaultSkills[key], 'default': true });
			$scope.character.skills = $filter('orderBy')($scope.character.skills, '+name');
			for (key in $scope.skillCols) {
				$scope.skillCols[key] = $scope.character.skills.slice(lastPos, lastPos + Math.floor($scope.character.skills.length / 3) + ($scope.character.skills.length % 3 > key?1:0));
				lastPos += Math.floor($scope.character.skills.length / 3) + ($scope.character.skills.length % 3 > key?1:0);
			}
		});
		$scope.getHalfValue = function (val) {
			return Math.floor(val / 2);
		};
		$scope.getFifthValue = function (val) {
			return Math.floor(val / 5);
		};
		$scope.computeDamage_Build = function (val) {
			for (key in $scope.damage_build) 
				if (val <= key) 
					return $scope.damage_build[key];
		};
	});
}]);