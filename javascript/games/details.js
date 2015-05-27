$(function () {
	$('#withdrawFromGame, .actionLinks a, #newMap, .mapActions a, #newDeck, .deckActions a').colorbox();
	$('#toggleForumVisibility').click(function (e) {
		e.preventDefault();

		$forumVis = $(this);

		$.post('/games/process/toggleForumVisibility/', { gameID: $('#gameID').val() }, function () {
			status = $forumVis.siblings('span').text();
			$forumVis.siblings('span').text(status == 'Public'?'Private':'Public');
			$forumVis.text('[ Make game ' + (status == 'Public'?'Public':'Private') + ' ]');
		});
	});

/*	$('#invite').submit(function (e) {
		e.preventDefault();

		gameID = $(this).find('input[name=gameID]').val();
		user = $(this).find('input[name=user]').val();

		$.ajax({
			method: 'POST',
			url: API_HOST + '/games/invite/',
			data: { gameID: gameID, user: user },
			xhrFields: {
				withCredentials: true
			},
			success: function (data) {
				console.log(data);
			}
		});
	});*/
});
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
		$scope.inGame = false;
		$scope.withdrawEarly = false;
		$scope.approved = false;
		$scope.isGM = false;
		$scope.isPrimaryGM = false;
		$scope.combobox = {};
		$scope.combobox.search = { 'characters': '' };
		$scope.pendingInvite = false;

		setGameData = function () {
			$http.post(API_HOST + '/games/details/', { gameID: $scope.gameID }).success(function (data) {
				$scope.details = data.details;
				$scope.players = data.players;
				$scope.invites.waiting = data.invites;
				$scope.playersAwaitingApproval = $filter('filter')($scope.players, { approved: false }).length > 0?true:false;
				$scope.details.playersInGame = $scope.players.length - 1;
				$scope.pendingInvite = $filter('filter')($scope.invites.waiting, { userID: currentUser.userID }).length  == 1?true:false;
				for (key in $scope.players) {
					if (currentUser && $scope.players[key].userID == currentUser.userID) {
						$scope.inGame = true;
						$scope.curPlayer = $scope.players[key];
						$scope.approved = $scope.curPlayer.approved?true:false;
						if ($scope.curPlayer.isGM) 
							$scope.isGM = true;

						if ($scope.approved && ($scope.isGM || $scope.curPlayer.characters.length < $scope.details.charPerPlayer)) {
							$http.post(API_HOST + '/characters/my/', { 'system': $scope.details.system['_id'] }).success(function (data) {
								$scope.characters = data.characters;
								$scope.combobox.characters = [];
								for (key in $scope.characters) 
									$scope.combobox.characters.push({ 'id': $scope.characters[key].characterID, 'value': $scope.characters[key].label });
							});
						}
					}
				}
				if (currentUser && $scope.details.gm.userID == currentUser.userID) 
					$scope.isPrimaryGM = true;
			});
		};
		setGameData();

		$scope.applyToGame = function () {
			$http.post(API_HOST + '/games/apply/', { gameID: $scope.gameID }).success(function (data) {
				if (data.success == true) 
					$scope.inGame = true;
			});
		};

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
	});
});
