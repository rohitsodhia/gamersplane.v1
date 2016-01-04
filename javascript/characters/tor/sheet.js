controllers.controller('viewCharacter_tor', ['$scope', '$http', '$sce', '$timeout', 'CurrentUser', 'CharactersService', function ($scope, $http, $sce, $timeout, CurrentUser, CharactersService) {
	CurrentUser.load().then(function () {
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
		$scope.loadChar();
	});
}]);