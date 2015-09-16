controllers.controller('games_details', function ($scope, $http, $sce, $filter, $timeout, currentUser) {
	pathElements = getPathElements();
	currentUser.then(function (currentUser) {
		currentUser = currentUser.data;
		$scope.ngInterface = '';
		$scope.skewedOut = {};
		$scope.loggedIn = currentUser.loggedOut?false:true;
		$scope.currentUser = currentUser;
		$scope.gameID = pathElements[1];
		$scope.details = {};
		$scope.players = [];
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
			$http.post(API_HOST + '/games/details/', { gameID: $scope.gameID }).success(function (data) {
				if (data.success) {
					$scope.details = data.details;
					$scope.players = data.players;
					$scope.invites.waiting = data.invites;
					$scope.decks = data.decks;
					$scope.playersAwaitingApproval = $filter('filter')($scope.players, { approved: false }).length > 0?true:false;
					$scope.details.playersInGame = $scope.players.length - 1;
					$scope.pendingInvite = $filter('filter')($scope.invites.waiting, { userID: currentUser.userID }, true).length  == 1?true:false;
					for (key in $scope.players) {
						if (currentUser && $scope.players[key].userID == currentUser.userID) {
							$scope.inGame = true;
							$scope.curPlayer = $scope.players[key];
							$scope.approved = $scope.curPlayer.approved?true:false;
							if ($scope.curPlayer.isGM) 
								$scope.isGM = true;

							if ($scope.approved && ($scope.isGM || $scope.curPlayer.characters.length < $scope.details.charsPerPlayer)) {
								$http.post(API_HOST + '/characters/my/', { 'system': $scope.details.system['_id'], 'noGame': true }).success(function (data) {
									$scope.characters = data.characters;
									for (key in $scope.characters) 
										$scope.availChars.push({ 'value': $scope.characters[key].characterID, 'display': $scope.characters[key].label });
									$scope.availChars = $filter('orderBy')($scope.availChars, 'display');
								});
							}
							break;
						}
					}
					if (currentUser && $scope.details.gm.userID == currentUser.userID && $scope.details.retired == null) 
						$scope.isPrimaryGM = true;
				} //else 
//					document.location = '/games/';
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
					document.location = '/games/?gameRetired=' + $scope.details.gameID;
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
					if (newVal.playerID == currentUser.userID) 
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

		$scope.invites = { user: '', errorMsg: null, waiting: [] };
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
					$scope.invites.waiting.push(data.user);
				}
			});
		};
		$scope.withdrawInvite = function (invite) {
			$http.post(API_HOST + '/games/invite/withdraw/', { 'gameID': $scope.gameID, 'userID': invite.userID }).success(function (data) {
				if (data.success) {
					index = $scope.invites.waiting.indexOf(invite);
					$scope.invites.waiting.splice(index, 1);
				}
			});
		};
		$scope.rejectInvite = function (invite) {
			$http.post(API_HOST + '/games/invite/reject/', { 'gameID': $scope.gameID, 'userID': currentUser.userID }).success(function (data) {
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

		$scope.submitChar = { characterID: null };
		$scope.submitCharacter = function () {
			$http.post(API_HOST + '/games/characters/submit/', { 'gameID': $scope.gameID, 'characterID': $scope.submitChar.character.value }).success(function (data) {
				if (data.success) {
					$scope.combobox.search.characters = '';
					for(key in $scope.combobox.characters) 
						if ($scope.combobox.characters[key].id == $scope.submitChar.character.id) 
							delete $scope.combobox.characters[key]
					for (pKey in $scope.players) {
						if ($scope.players[pKey].userID == currentUser.userID) {
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
							$scope.combobox.characters.push({ 'id': character[0].characterID, 'value': character[0].label });
							$scope.combobox.characters = $filter('orderBy')($scope.combobox.characters, 'value');
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
});

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