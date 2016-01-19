app.controller('myCharacters', ['$scope', '$http', '$sce', '$timeout', 'CurrentUser', 'CharactersService', 'SystemsService', function ($scope, $http, $sce, $timeout, CurrentUser, CharactersService, SystemsService) {
	$scope.characters = {};
	$scope.library = {};
	$scope.systems = [];
	$scope.charTypes = ['PC', 'NPC', 'Mob'];
	$scope.newChar = { 'label': '', 'system': {}, 'charType': {} };
	$scope.editing = {
		'characterID': null,
		'label': ''
	};
	$scope.deleting = null;
	$scope.activeRequests = [];

	$scope.$emit('pageLoading');
	CurrentUser.load().then(function () {
		CharactersService.getMy(true).then(function (data) {
			$scope.$emit('pageLoading');
			$scope.characters = typeof data.characters != 'undefined'?data.characters:null;
			$scope.library = typeof data.library != 'undefined'?data.library:null;
		});
		SystemsService.get({ 'getAll': true }).then(function (data) {
			for (key in data.systems) 
				$scope.systems.push({ 'value': data.systems[key].shortName, 'display': data.systems[key].fullName });
		});

		$scope.editBasic = function (character) {
			$scope.editing.characterID = character.characterID;
			$scope.editing.label = character.label;
			character.cCharType = { 'value': character.charType, 'display': character.charType };
		};
		$scope.saveEdit = function (character) {
			CharactersService.saveBasic({
				'characterID': character.characterID,
				'label': character.label,
				'charType': character.cCharType.value
			}).then(function (data) {
				if (data.success) {
					character.charType = character.cCharType.value;
					$scope.editing = {
						'characterID': null,
						'label': ''
					};
				}
			});
		};
		$scope.cancelEditing = function (character) {
			$scope.editing.characterID = null;
			character.label = $scope.editing.label;
		};

		$scope.toggleLibrary = function (character, library) {
			CharactersService.toggleLibrary(character.characterID).then(function (data) {
				if (data.success) 
					character.inLibrary = data.state;
			});
		}

		$scope.deleteChar = function (character) {
			$scope.deleting = character.characterID;
		};
		$scope.confirmDelete = function (character) {
			CharactersService.delete({
				'characterID': character.characterID,
			}).then(function (data) {
				if (data.success) {
					$scope.deleting = null;
					index = $scope.characters.indexOf(character);
					$scope.characters.splice(index, 1);
				}
			});
		};
		$scope.cancelDeleting = function (character) {
			$scope.deleting = null;
		};

		$scope.unfavorite = function (character) {
			CharactersService.toggleFavorite(character.characterID).then(function (data) {
				removeEle($scope.library, character);
			});
		}

		$scope.createChar = function () {
			$scope.$emit('pageLoading');
			var data = copyObject($scope.newChar);
			data.label = data.label.trim();
			if (data.label.length == 0) 
				return;
			data.system = data.system.value;
			data.charType = data.charType.value;
			CharactersService.new(data).then(function (data) {
				if (data.success) 
					window.location.href = '/characters/' + data.system + '/' + data.characterID + '/edit/';
			});
		};
	});
}]);