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
		$scope.loggedIn = currentUser?true:false;
		$scope.currentUser = currentUser;
		$scope.gameID = pathElements[1];
		$scope.inGame = false;
		$scope.approved = false;
		$scope.isGM = false;
		$scope.isPrimaryGM = false;
		$http.post(API_HOST + '/games/details/', { gameID: $scope.gameID }).success(function (data) {
			$scope.details = data.details;
			$scope.players = data.players;
			$scope.details.playersInGame = $($scope.players).size() - 1;
			for (key in $scope.players) {
				if (currentUser && $scope.players[key].userID == currentUser.userID) {
					$scope.inGame = true;
					$scope.approved = $scope.players[currentUser.userID].approved?true:false;
					if ($scope.players[key].isGM) 
						$scope.isGM = true;
				}
			}
			if (currentUser && $scope.details.gm.userID == currentUser.userID) 
				$scope.isPrimaryGM = true;

			if ($scope.inGame && $scope.approved && ($scope.isGM || $scope.players[currentUser.userID].characters.length < $scope.details.charPerPlayer)) {
				$http.post(API_HOST + '/characters/my/', { 'system': $scope.details.system['_id'] }).success(function (data) {
					$scope.characters = data.characters;
				});
			}
		});
	});
});