controllers.controller('editCharacter_tor', ['$scope', '$http', '$q', '$sce', '$timeout', 'CurrentUser', 'CharactersService', 'ACSearch', 'Range', function ($scope, $http, $q, $sce, $timeout, CurrentUser, CharactersService, ACSearch, Range) {
	CurrentUser.load().then(function () {
		$scope.range = Range.get;
		$scope.skills = {
			'Body': {
				'personality': 'Awe',
				'movement': 'Athletics',
				'perception': 'Awareness',
				'survival': 'Explore',
				'custom': 'Song',
				'vocation': 'Craft',
			},
			'Heart': {
				'personality': 'Inspire',
				'movement': 'Travel',
				'perception': 'Insight',
				'survival': 'Healing',
				'custom': 'Courtesy',
				'vocation': 'Battle',
			},
			'Wits': {
				'personality': 'Persuade',
				'movement': 'Stealth',
				'perception': 'Search',
				'survival': 'Hunting',
				'custom': 'Riddle',
				'vocation': 'Lore'
			}
		}
		$scope.skillGroups = [
			'personality',
			'movement',
			'perception',
			'survival',
			'custom',
			'vocation'
		]
		$scope.labels = {
		};
		blanks = {
			'weaponSkills': { 'name': '', 'rank': 0 },
			'weapons': { 'name': '', 'damage': 0, 'edge': 0, 'injury': 0, 'enc': 0 },
			'gear': { 'name': '', 'enc': 0 },
/*			'skills': { 'name': '', 'rating': 1, 'type': 'a' },
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
*/		};
		$scope.loadChar().then(function (data) {
			$scope.character.mainGear = {
				'armour': { 'name': '', 'enc': 0 },
				'headgear': { 'name': '', 'enc': 0 },
				'shield': { 'name': '', 'enc': 0 }
			}
		});
		$scope.setSkill = function (skill, rank) {
			$scope.character.skills[skill] = rank;
		};
		$scope.setSkillGroup = function (skill, rank) {
			$scope.character.skillGroups[skill] = rank;
		};
		$scope.setWeaponSkill = function (weapon, rank) {
			weapon.rank = rank;
		};
		$scope.toggleStatus = function (status) {
			$scope.character.status[status] = !$scope.character.status[status];
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
//		$scope.save = function () {
//			$parent.save();
//		};
	});
}]);