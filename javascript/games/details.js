controllers.controller('games_details', ['$scope', '$http', '$sce', '$filter', '$timeout', 'CurrentUser', 'GamesService', 'ACSearch', function ($scope, $http, $sce, $filter, $timeout, CurrentUser, GamesService, ACSearch) {
	pathElements = getPathElements();
	CurrentUser.load().then(function () {
		CurrentUser = CurrentUser.get();
		$scope.ngInterface = '';
		$scope.skewedOut = {};
		$scope.loggedIn = CurrentUser.loggedOut?false:true;
		$scope.CurrentUser = CurrentUser;
		$scope.gameID = pathElements[1];
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
			GamesService.getDetails($scope.gameID).then(function (data) {
				if (data.success) {
					$scope.details = data.details;
					$scope.players = data.players;
					$scope.invites.pending = data.invites;
					$scope.decks = data.decks;
					$scope.playersAwaitingApproval = $filter('filter')($scope.players, { approved: false }).length > 0?true:false;
					$scope.details.playersInGame = $scope.players.length - 1;
					$scope.pendingInvite = $filter('filter')($scope.invites.pending, { userID: CurrentUser.userID }, true).length  == 1?true:false;
					for (key in $scope.players) {
						if (CurrentUser && $scope.players[key].userID == CurrentUser.userID) {
							$scope.inGame = true;
							$scope.curPlayer = $scope.players[key];
							$scope.approved = $scope.curPlayer.approved?true:false;
							if ($scope.curPlayer.isGM) 
								$scope.isGM = true;

							if ($scope.approved && ($scope.isGM || $scope.curPlayer.characters.length < $scope.details.charsPerPlayer)) {
								$http.post(API_HOST + '/characters/my/', { 'system': $scope.details.system['_id'], 'noGame': true }).then(function (data) {
									$scope.characters = data.data.characters;
									for (key in $scope.characters) 
										$scope.availChars.push({ 'value': $scope.characters[key].characterID, 'display': $scope.characters[key].label });
									$scope.availChars = $filter('orderBy')($scope.availChars, 'display');
								});
							}
							break;
						}
					}
					if (CurrentUser && $scope.details.gm.userID == CurrentUser.userID && $scope.details.retired == null) 
						$scope.isPrimaryGM = true;
				} //else 
//					window.location = '/games/';
				$scope.$emit('pageLoading');
			});
		};
		setGameData();

		$scope.toggleGameStatus = function () {
			$http.post(API_HOST + '/games/toggleGameStatus/', { gameID: $scope.gameID }).success(function (data) {
				if (data.success) 
					$scope.details.status = !$scope.details.status;
			});
		};
		$scope.toggleForum = function () {
			$http.post(API_HOST + '/games/toggleForum/', { gameID: $scope.gameID }).success(function (data) {
				if (data.success) 
					$scope.details.readPermissions = !$scope.details.readPermissions;
			});
		};

		$scope.displayRetireConfirm = false;
		$scope.toggleRetireConfirm = function () {
			$scope.displayRetireConfirm = !$scope.displayRetireConfirm;
		}
		$scope.confirmRetire = function () {
			$http.post(API_HOST + '/games/retire/', { 'gameID': $scope.details.gameID }).success(function (data) {
				if (data.success) 
					window.location.href = '/games/?gameRetired=' + $scope.details.gameID;
			});
		}


		$scope.applyToGame = function () {
			$http.post(API_HOST + '/games/apply/', { gameID: $scope.gameID }).success(function (data) {
				if (data.success == true) 
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
					for (key in $scope.players) {
						if ($scope.players[key].userID == newVal.playerID) {
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
						if ($scope.players[key].userID == newVal.playerID) {
							$scope.players.splice(key, 1);
							$scope.inGame = false;
							$scope.pendingInvite = false;
							break;
						}
					}
					break;
				case 'playerLeft':
					for (key in $scope.players) {
						if ($scope.players[key].userID == newVal.playerID) {
							$scope.players.splice(key, 1);
							break;
						}
					}
					$scope.inGame = false;
					$scope.pendingInvite = false;
					break;
				case 'toggleGM':
					for (key in $scope.players) {
						if ($scope.players[key].userID == newVal.playerID) {
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
				for (key in data) 
					data[key] = {
						'value': data[key].userID,
						'display': data[key].username
					};
				return data;
			});
		}
		$scope.inviteUser = function () {
			if ($scope.invites.user.length == 0) 
				return;
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
					for (key in $scope.availChars) {
						if ($scope.availChars[key].id == $scope.submitChar.character.id) 
							$scope.availChars.splice(key, 1);
					}
					for (pKey in $scope.players) {
						if ($scope.players[pKey].userID == CurrentUser.userID) {
							if ($scope.isGM) 
								data.character.approved = true;
							$scope.players[pKey].characters.push(data.character);
							break;
						}
					}
				}
			});
		};
		$scope.removeCharacter = function (character) {
			$http.post(API_HOST + '/games/characters/remove/', { 'gameID': $scope.gameID, 'characterID': character.characterID }).success(function (data) {
				if (data.success) {
					for (pKey in $scope.players) {
						if ($scope.players[pKey].userID == character.userID) {
							for (cKey in $scope.players[pKey].characters) {
								if ($scope.players[pKey].characters[cKey].characterID == character.characterID) 
									character = $scope.players[pKey].characters.splice(cKey, 1);
							}
							$scope.availChars.push({ 'value': character[0].characterID, 'display': character[0].label });
							$scope.availChars = $filter('orderBy')($scope.availChars, 'value');
							break;
						}
					}
				}
			});
		};
		$scope.approveCharacter = function (character) {
			$http.post(API_HOST + '/games/characters/approve/', { 'gameID': $scope.gameID, 'characterID': character.characterID }).success(function (data) {
				if (data.success) {
					for (pKey in $scope.players) {
						if ($scope.players[pKey].userID == character.userID) {
							for (cKey in $scope.players[pKey].characters) {
								if ($scope.players[pKey].characters[cKey].characterID == character.characterID) 
									$scope.players[pKey].characters[cKey].approved = true;
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
	}
}]);