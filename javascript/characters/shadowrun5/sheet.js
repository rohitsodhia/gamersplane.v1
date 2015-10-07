controllers.controller('viewCharacter_shadowrun5', ['$scope', '$http', '$sce', '$timeout', 'currentUser', 'character', 'Range', function ($scope, $http, $sce, $timeout, currentUser, character, Range) {
	currentUser.then(function (currentUser) {
		pathElements = getPathElements();
		$scope.range = Range.get;
		$scope.character = {};
		character.load(pathElements[2], true).then(function (data) {
			$scope.character = copyObject(data);
		});
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

		$scope.toggleNotes = function ($event) {
			$($event.target).siblings('.notes').slideToggle();
		}
	});
}]);