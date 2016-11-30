controllers.controller('editCharacter_shadowrun5', ['$scope', '$http', '$q', '$sce', '$timeout', 'CurrentUser', 'CharactersService', 'ACSearch', 'Range', function ($scope, $http, $q, $sce, $timeout, CurrentUser, CharactersService, ACSearch, Range) {
	CurrentUser.load().then(function () {
		$scope.range = Range.get;
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
			'skills': { 'name': '', 'rating': 1, 'type': 'a', 'search': [] },
			'qualities': { 'name': '', 'notes': '', 'type': 'p', 'search': [] },
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
		$scope.loadChar();
		function setupCILData(items) {
			for (var key in items) {
				systemItem = items[key].systemItem;
				items[key] = {
					'value': items[key].itemID,
					'display': items[key].name,
					'class': []
				};
				if (!systemItem) {
					items[key].class.push('nonSystemItem');
				}
			}
			return items;
		}
		$scope.changeSkillName = function (skill, name) {
			skill.name = name;
			ACSearch.cil('skill', name, 'shadowrun5', true).then(function (items) {
				skill.search = setupCILData(items);
			});
		};
		$scope.changeQualityName = function (quality, name) {
			quality.name = name;
			ACSearch.cil('quality', name, 'shadowrun5e', true).then(function (items) {
				quality.search = setupCILData(items);
			});
		};
		$scope.changeProgramName = function (program, name) {
			program.name = name;
			ACSearch.cil('program', name, 'shadowrun5e', true).then(function (items) {
				program.search = setupCILData(items);
			});
		};
		$scope.changeAugmentationName = function (augmentation, name) {
			augmentation.name = name;
			ACSearch.cil('augmentation', name, 'shadowrun5e', true).then(function (items) {
				augmentation.search = setupCILData(items);
			});
		};
		$scope.changeSPRCFName = function (sprcf, name) {
			sprcf.name = name;
			ACSearch.cil('sprcf', name, 'shadowrun5e', true).then(function (items) {
				sprcf.search = setupCILData(items);
			});
		};
		$scope.changePowerName = function (power, name) {
			power.name = name;
			ACSearch.cil('power', name, 'shadowrun5e', true).then(function (items) {
				power.search = setupCILData(items);
			});
		};
	});
}]);
