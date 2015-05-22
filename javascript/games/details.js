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
controllers.controller('games_details', function ($scope, $http, $sce, $filter, currentUser) {
	pathElements = getPathElements();
	currentUser.then(function (currentUser) {
		currentUser = currentUser.data;
		$scope.skewedOut = {};
		$scope.loggedIn = currentUser.loggedOut?false:true;
		$scope.currentUser = currentUser;
		$scope.gameID = pathElements[1];
		$scope.details = {};
		$scope.players = [];
		$scope.curPlayer = {};
		$scope.characters = [];
		$scope.inGame = false;
		$scope.withdrawEarly = false;
		$scope.approved = false;
		$scope.isGM = false;
		$scope.isPrimaryGM = false;
		$scope.combobox = {};
		$scope.combobox.search = { 'characters': '' };
		$http.post(API_HOST + '/games/details/', { gameID: $scope.gameID }).success(function (data) {
			$scope.details = data.details;
			$scope.players = data.players;
			$scope.details.playersInGame = $scope.players.length - 1;
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
		
		$scope.applyToGame = function () {
			$http.post(API_HOST + '/games/apply/', { gameID: $scope.gameID }).success(function (data) {
				if (data.success == true) 
					$scope.inGame = true;
			});
		};

		$scope.toggleEarlyWithdraw = function ($event) {
//			$('#withdrawEarly').slideToggle();
//			$scope.withdrawEarly = !$scope.withdrawEarly;
		};
	});
});

app.animation('#withdrawEarly', [function () {
	return {
/*		addClass: function (element, className, done) {
			$(element).slideToggle();
		},
		removeClass: function (element, className, done) {
			$(element).slideToggle();
		}*/
	}
}]);