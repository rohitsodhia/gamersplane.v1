app.controller('myCharacters', ['$scope', '$http', '$sce', '$timeout', '$filter', 'CurrentUser', 'CharactersService', 'SystemsService', function ($scope, $http, $sce, $timeout, $filter, CurrentUser, CharactersService, SystemsService) {
	$scope.characters = [];
	$scope.library = [];
	$scope.systems = [];
	$scope.charTypes = ['PC', 'NPC', 'Mob'];
	$scope.newChar = { 'label': '', 'system': '', 'charType': '' };
	$scope.editing = {
		'characterID': null,
		'new': {
			'label': '',
			'cCharType': ''
		}
	};
	$scope.deleting = null;
	$scope.activeRequests = [];
	$scope.pagination = { numItems: 0, itemsPerPage: 25 };
	$scope.filter = { search: '' };

	$scope.$emit('pageLoading');
	CurrentUser.load().then(function () {
		CharactersService.getMy({ 'library': true }).then(function (data) {
			$scope.$emit('pageLoading');
			$scope.characters = typeof data.characters != 'undefined' ? data.characters : null;
			$scope.library = typeof data.library != 'undefined' ? data.library : null;
			$scope.pagination.numItems = $scope.characters.length;
		});
		SystemsService.get({ 'getAll': true }).then(function (data) {
			for (var key in data.systems)
				if (data.systems[key].hasCharSheet)
					$scope.systems.push({ 'value': data.systems[key].shortName, 'display': data.systems[key].fullName });
		});

		$scope.editBasic = function (character) {
			$scope.editing.characterID = character.characterID;
			$scope.editing.new.label = character.label;
			$scope.editing.new.cCharType = character.charType;
		};
		$scope.updateCharType = function (character, type) {
			$scope.editing.new.cCharType = type;
		};
		$scope.saveEdit = function (character) {
			CharactersService.saveBasic({
				'characterID': character.characterID,
				'label': $scope.editing.new.label,
				'charType': $scope.editing.new.cCharType
			}).then(function (data) {
				if (data.success) {
					character.label = $scope.editing.new.label;
					character.charType = $scope.editing.new.cCharType;
					$scope.editing = {
						'characterID': null,
						'new': {
							'label': '',
							'cCharType': ''
						}
					};
				}
			});
		};
		$scope.cancelEditing = function (character) {
			$scope.editing = {
				'characterID': null,
				'new': {
					'label': '',
					'cCharType': ''
				}
			};
		};

		$scope.toggleLibrary = function (character, library) {
			CharactersService.toggleLibrary(character.characterID).then(function (data) {
				if (data.success)
					character.inLibrary = data.state;
			});
		};

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
		};

		$scope.setSystem = function (search, system) {
			$scope.newChar.system = system;
		};
		$scope.createChar = function () {
			$scope.$emit('pageLoading');
			var data = copyObject($scope.newChar);
			data.label = data.label.trim();
			if (data.label.length === 0) {
				return;
			}
			data.system = data.system;
			data.charType = data.charType;
			CharactersService.new(data).then(function (data) {
				if (data.success) {
					window.location.href = '/characters/' + data.system + '/' + data.characterID + '/edit/';
				}
			});
		};

		$scope.$watch(function () { return $scope.filter.search; }, function () {
			$scope.pagination.numItems = $filter('filter')($scope.characters, { $: $scope.filter.search }).length;
			$scope.pagination.current = 1;
		});

	});
}]);
