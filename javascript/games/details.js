controllers.controller('games_details', ['$scope', '$http', '$sce', '$filter', '$timeout', 'CurrentUser', 'SystemsService', 'ToolsService', 'GamesService', 'CharactersService', 'ACSearch', function ($scope, $http, $sce, $filter, $timeout, CurrentUser, SystemsService, ToolsService, GamesService, CharactersService, ACSearch) {
	pathElements = getPathElements();
	CurrentUser.load().then(function () {
		CurrentUser = CurrentUser.get();
		$scope.ngInterface = '';
		$scope.skewedOut = {};
		$scope.loggedIn = !CurrentUser || CurrentUser.loggedOut?false:true;
		$scope.CurrentUser = CurrentUser;
		$scope.systems = [];
		$scope.deckTypes = {};
		$scope.gameID = parseInt(pathElements[1]);
		$scope.details = {};
		$scope.players = [];
		$scope.invites = { 'user': '', 'users': [], 'errorMsg': null, 'pending': [] };
		$scope.playersAwaitingApproval = false;
		$scope.curPlayer = {};
		$scope.characters = [];
		$scope.availChars = [];
		$scope.inGame = false;
		$scope.withdrawEarly = false;
		$scope.approved = false;
		$scope.isGM = false;
		$scope.isPrimaryGM = false;
		$scope.combobox = {};
		$scope.pendingInvite = false;

		setGameData = function () {
			$scope.$emit('pageLoading');
			SystemsService.get({ 'getAll': true, 'fields': ['hasCharSheet'] }).then(function (data) {
				data.systems.forEach(function (val) {
					$scope.systems[val.shortName] = val.fullName;
				});
			});
			$scope.deckTypes = ToolsService.deckTypes;
			GamesService.getDetails($scope.gameID).then(function (data) {
				if (data.success) {
					$scope.details = data.details;
					$scope.players = data.players;
					$scope.invites.pending = data.invites;
					$scope.decks = data.decks;
					$scope.playersAwaitingApproval = $filter('filter')($scope.players, { approved: false }).length > 0?true:false;
					$scope.details.playersInGame = $scope.players.length - 1;
					$scope.pendingInvite = $filter('filter')($scope.invites.pending, { userID: CurrentUser?CurrentUser.userID:0 }, true).length  == 1?true:false;
					var addAvilChars = function (data) {
						$scope.characters = data.characters;
						for (var key in $scope.characters)
							$scope.availChars.push({ 'value': $scope.characters[key].characterID, 'display': $scope.characters[key].label });
						$scope.availChars = $filter('orderBy')($scope.availChars, 'display');
					};
					for (var key in $scope.players) {
						if (CurrentUser && $scope.players[key].user.userID == CurrentUser.userID) {
							$scope.inGame = true;
							$scope.curPlayer = $scope.players[key];
							$scope.approved = $scope.curPlayer.approved?true:false;
							if ($scope.curPlayer.isGM)
								$scope.isGM = true;

							if ($scope.approved && ($scope.isGM || $scope.curPlayer.characters.length < $scope.details.charsPerPlayer)) {
								allowedSystems = $scope.details.allowedCharSheets;
								// addSystem = true;
								// for (key in allowedSystems) {
								// 	if (allowedSystems[key] == $scope.details.system) {
								// 		addSystem = false;
								// 		break;
								// 	}
								// }
								// if (addSystem)
								// 	allowedSystems.push($scope.details.system);
								CharactersService.getMy({ 'systems': allowedSystems, 'noGame': true }).then(addAvilChars);
							}
							break;
						}
					}
					if (CurrentUser && $scope.details.gm.userID == CurrentUser.userID && ($scope.details.retired === undefined || $scope.details.retired === null))
						$scope.isPrimaryGM = true;
				} //else
//					window.location = '/games/';
				$scope.$emit('pageLoading');
			});
		};
		setGameData();

		$scope.toggleGameStatus = function () {
			GamesService.toggleGameStatus($scope.gameID).then(function (data) {
				if (data.success)
					$scope.details.status = $scope.details.status == 'open'?'closed':'open';
			});
		};
		$scope.toggleForum = function () {
			GamesService.toggleForum($scope.gameID).then(function (data) {
				if (data.success)
					$scope.details.readPermissions = !$scope.details.readPermissions;
			});
		};

		$scope.displayRetireConfirm = false;
		$scope.toggleRetireConfirm = function () {
			$scope.displayRetireConfirm = !$scope.displayRetireConfirm;
		};
		$scope.confirmRetire = function () {
			GamesService.confirmRetire($scope.gameID).then(function (data) {
				if (data.success)
					window.location.href = '/games/?gameRetired=' + $scope.details.gameID;
			});
		};


		$scope.applyToGame = function () {
			GamesService.apply($scope.gameID).then(function (data) {
				if (data.success === true)
					$scope.inGame = true;
			});
		};

		$scope.$watch('modalWatch', function (newVal, oldVal) {
			if (typeof newVal != 'object')
				return;

			switch (newVal.action) {
				case 'approvePlayer':
					setGameData();
					break;
				case 'rejectPlayer':
					for (var key in $scope.players) {
						if ($scope.players[key].user.userID == newVal.playerID) {
							$scope.players.splice(key, 1);
							break;
						}
					}
					if (newVal.playerID == CurrentUser.userID)
						$scope.inGame = false;
					$scope.playersAwaitingApproval = $filter('filter')($scope.players, { approved: false }).length > 0?true:false;
					break;
				case 'playerRemoved':
					for (key in $scope.players) {
						if ($scope.players[key].user.userID == newVal.playerID) {
							$scope.players.splice(key, 1);
							if ($newVal.playerID == CurrentUser.userID) {
								$scope.inGame = false;
								$scope.pendingInvite = false;
							}
							break;
						}
					}
					break;
				case 'playerLeft':
					for (key in $scope.players) {
						if ($scope.players[key].user.userID == newVal.playerID) {
							$scope.players.splice(key, 1);
							break;
						}
					}
					$scope.inGame = false;
					$scope.pendingInvite = false;
					break;
				case 'toggleGM':
					for (key in $scope.players) {
						if ($scope.players[key].user.userID == newVal.playerID) {
							$scope.players[key].isGM = !$scope.players[key].isGM;
							break;
						}
					}
					break;
				case 'createDeck':
					$scope.decks.push(newVal.deck);
					break;
				case 'editDeck':
					deck = $filter('filter')($scope.decks, { deckID: newVal.deck.deckID })[0];
					index = $scope.decks.indexOf(deck);
					$scope.decks[index] = newVal.deck;
					break;
				case 'shuffleDeck':
					deck = $filter('filter')($scope.decks, { deckID: newVal.deckID })[0];
					index = $scope.decks.indexOf(deck);
					$scope.decks[index].cardsRemaining = newVal.deckSize;
					break;
				case 'deleteDeck':
					deck = $filter('filter')($scope.decks, { deckID: newVal.deckID })[0];
					index = $scope.decks.indexOf(deck);
					$scope.decks.splice(index, 1);
					break;
			}

			$.colorbox.close();
		});

		$scope.searchUsers = function (search) {
			return ACSearch.users(search, true).then(function (data) {
				for (var key in data)
					data[key] = {
						'value': data[key].userID,
						'display': data[key].username
					};
				return data;
			});
		};
		$scope.inviteUser = function () {
			if ($scope.invites.user.length === 0)
				return;
			if ($scope.invites.user == $scope.CurrentUser.username) {
				$scope.invites.errorMsg = 'You cannot invite yourself';
				return;
			}
			$http.post(API_HOST + '/games/invite/', { 'gameID': $scope.gameID, 'user': $scope.invites.user }).success(function (data) {
				if (data.failed && data.errors && data.errors.indexOf('invalidUser') != -1) {
					$scope.invites.errorMsg = 'Invalid user';
//					$timeout(function () { $scope.invites.errorMsg = null; }, 1000);
				} else if (data.success) {
					$scope.invites.errorMsg = null;
					$scope.invites.user = '';
					$scope.invites.pending.push(data.user);
				}
			});
		};
		$scope.withdrawInvite = function (invite) {
			$http.post(API_HOST + '/games/invite/withdraw/', { 'gameID': $scope.gameID, 'userID': invite.userID }).success(function (data) {
				if (data.success) {
					index = $scope.invites.pending.indexOf(invite);
					$scope.invites.pending.splice(index, 1);
				}
			});
		};
		$scope.declineInvite = function (invite) {
			$http.post(API_HOST + '/games/invite/decline/', { 'gameID': $scope.gameID, 'userID': CurrentUser.userID }).success(function (data) {
				if (data.success) {
					$scope.pendingInvite = false;
				}
			});
		};
		$scope.acceptInvite = function (invite) {
			$http.post(API_HOST + '/games/invite/accept/', { 'gameID': $scope.gameID }).success(function (data) {
				if (data.success) {
					setGameData();
				}
			});
		};

		$scope.submitChar = { 'character': {} };
		$scope.submitCharacter = function () {
			$http.post(API_HOST + '/games/characters/submit/', { 'gameID': $scope.gameID, 'characterID': $scope.submitChar.character.value }).success(function (data) {
				if (data.success) {
					for (var key in $scope.availChars) {
						if ($scope.availChars[key].id == $scope.submitChar.character.id)
							$scope.availChars.splice(key, 1);
					}
					for (var pKey in $scope.players) {
						if ($scope.players[pKey].user.userID == CurrentUser.userID) {
							if ($scope.isGM)
								data.character.approved = true;
							$scope.players[pKey].characters.push(data.character);
							break;
						}
					}
				}
			});
		};
		$scope.removeCharacter = function (character, userID) {
			$http.post(API_HOST + '/games/characters/remove/', { 'gameID': $scope.gameID, 'characterID': character.characterID }).success(function (data) {
				if (data.success) {
					for (var pKey in $scope.players) {
						if ($scope.players[pKey].user.userID == userID) {
							for (var cKey in $scope.players[pKey].characters) {
								if ($scope.players[pKey].characters[cKey].characterID == character.characterID)
									$scope.players[pKey].characters.splice(cKey, 1);
							}
							$scope.availChars.push({ 'value': character.characterID, 'display': character.label });
							$scope.availChars = $filter('orderBy')($scope.availChars, 'display');
							$scope.curPlayer = $scope.players[pKey];
							break;
						}
					}
				}
			});
		};
		$scope.approveCharacter = function (character, userID) {
			$http.post(API_HOST + '/games/characters/approve/', { 'gameID': $scope.gameID, 'characterID': character.characterID }).success(function (data) {
				if (data.success) {
					for (var pKey in $scope.players) {
						if ($scope.players[pKey].user.userID == userID) {
							for (var cKey in $scope.players[pKey].characters) {
								if ($scope.players[pKey].characters[cKey].characterID == character.characterID) {
									$scope.players[pKey].characters[cKey].approved = true;
									break;
								}
							}
							break;
						}
					}
				}
			});
		};
	});
}]);

app.animation('.slideToggle', ['$timeout', function ($timeout) {
	$timeout(function () { $('.slideToggle.ng-hide').hide(); }, 1);
	return {
		addClass: function (element, className, done) {
			element.slideUp();
		},
		removeClass: function (element, className, done) {
			element.slideDown();
		}
	};
}]);
