controllers.controller('editCharacter_shadowrun5', ['$scope', '$http', '$q', '$sce', '$timeout', 'currentUser', 'character', 'ACSearch', 'range', function ($scope, $http, $q, $sce, $timeout, currentUser, character, ACSearch, range) {
	currentUser.then(function (currentUser) {
		pathElements = getPathElements();
		$scope.range = range.get;
		$scope.character = {};
		$scope.labels = {
			'rep': { 'street': 'Street Cred', 'notoriety': 'Notoriety', 'public': 'Public Awareness' },
			'stats': [
				{ 'key': 'body', 'value': 'Body' },
				{ 'key': 'agility', 'value': 'Agility' },
				{ 'key': 'reaction', 'value': 'Reaction' },
				{ 'key': 'strength', 'value': 'Strength' },
				{ 'key': 'willpower', 'value': 'Willpower' },
				{ 'key': 'logic', 'value': 'Logic' },
				{ 'key': 'intuition', 'value': 'Intuition' },
				{ 'key': 'charisma', 'value': 'Charisma' },
				{ 'key': 'edge', 'value': 'Edge' },
				{ 'key': 'essence', 'value': 'Essence' },
				{ 'key': 'mag_res', 'value': 'Magic/Resonance' },
				{ 'key': 'initiative', 'value': 'Initiative' },
				{ 'key': 'matrix_initiative', 'value': 'Matrix Initiative' },
				{ 'key': 'astral_initiative', 'value': 'Astral Initiative' }
			]
		};
		blanks = {
			'skills': { 'name': '', 'rating': 1, 'type': 'a' },
			'qualities': { 'name': '', 'notes': '', 'type': 'p' },
			'contacts': { 'name': '', 'loyalty': 0, 'connection': 0, 'notes': '' },
			'weapons.ranged': { 'name': '', 'damage': '', 'accuracy': 0, 'ap': 0, 'mode': '', 'rc': '', 'ammo': '', 'notes': '' },
			'weapons.melee': { 'name': '', 'reach': 0, 'damage': '', 'accuracy': 0, 'ap': 0, 'notes': '' },
			'armor': { 'name': '', 'rating': 0, 'notes': '' },
			'cyberdeck.programs': { 'name': '', 'notes': '' },
			'augmentations': { 'name': '', 'rating': 0, 'essence': 0, 'notes': '' },
			'sprcf': { 'name': '', 'tt': '', 'range': '', 'duration': '', 'drain': 0, 'notes': '' },
			'powers': { 'name': '', 'rating': 0, 'notes': '' },
			'gear': { 'name': '', 'rating': 0, 'notes': '' }
		};
		character.load(pathElements[2]).then(function (data) {
			$scope.character = copyObject(data);
			character.loadBlanks($scope.character, blanks);
		});
		$scope.addItem = function (key) {
			keyParts = key.split('.');
			if (keyParts.length == 2) 
				$scope.character[keyParts[0]][keyParts[1]].push(copyObject(blanks[key]));
			else 
				$scope.character[key].push(copyObject(blanks[key]));
		};
		$scope.searchSkills = function (search) {
			return ACSearch.cil('skill', search, 'shadowrun5').then(function (items) {
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
		$scope.searchQualities = function (search) {
			return ACSearch.cil('quality', search, 'shadowrun5', true);
		};
		$scope.searchPrograms = function (search) {
			return ACSearch.cil('program', search, 'shadowrun5', true);
		};
		$scope.searchAugmentations = function (search) {
			return ACSearch.cil('augmentation', search, 'shadowrun5', true);
		};
		$scope.searchSPRCF = function (search) {
			return ACSearch.cil('sprcf', search, 'shadowrun5', true);
		};
		$scope.searchPowers = function (search) {
			return ACSearch.cil('powers', search, 'shadowrun5', true);
		};
		$scope.save = function () {
			character.save($scope.character.characterID, $scope.character).then(function (data) {
				if (data.saved) 
					window.location = '/characters/' + pathElements[1] + '/' + pathElements[2];
			});
		};

		$scope.toggleNotes = function ($event) {
			$($event.target).siblings('textarea').slideToggle();
		}
	});
}]);