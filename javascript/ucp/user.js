controllers.controller('user', ['$scope', '$http', 'CurrentUser', 'Users', function ($scope, $http, CurrentUser, Users) {
	$scope.$emit('pageLoading');
	CurrentUser.load().then(function () {
		$scope.user = null;
		$scope.profileFields = { 'location': 'Location', 'aim': 'AIM', 'yahoo': 'Yahoo!', 'msn': 'MSN', 'games': 'Games' };

		pathElements = getPathElements();
		userID = null;
		if (!isUndefined(pathElements[1])) 
			userID = parseInt(pathElements[1]);
		Users.get(userID).then(function (data) {
			if (data) {
				$scope.user = data;
				$scope.user.lastActivity = Users.inactive($scope.user.lastActivity, false);
				$http.post(API_HOST + '/users/stats/', { userID: userID }).then(function (response) {
					$scope.characters = response.data.characters.list;
					$scope.charCount = response.data.characters.numChars;
					$scope.characters.forEach(function (ele) {
						ele.percentage = Math.round(ele.numChars / $scope.charCount * 100);
					});
					$scope.games = response.data.games.list;
					$scope.gameCount = response.data.games.numGames;
					$scope.games.forEach(function (ele) {
						ele.percentage = Math.round(ele.numGames / $scope.gameCount * 100);
					});
				});
				$scope.$emit('pageLoading');
			}
		});
	});
}]);