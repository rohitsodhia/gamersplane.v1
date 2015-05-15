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
});
/*controllers.controller('games_details', function ($scope, $http, $sce, $filter, currentUser) {
	pathElements = getPathElements();
	currentUser.getUser();
	$http.post(API_HOST + '/games/details/', { gameID: pathElements[1] }).success(function (data) {
		$scope.details = data.details;
		$scope.players = data.players;
		$scope.characters = data.characters;
		for (key in $scope.players) {
			if ($scope.players[key].userID == currentUser.user.userID && $scope.players[key].isGM) 
				$scope.isGM = true;
		}
	});
	$scope.isGM = false;
});*/