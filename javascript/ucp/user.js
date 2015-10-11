controllers.controller('user', ['$scope', '$http', 'currentUser', 'Users', function ($scope, $http, currentUser, Users) {
	$scope.$emit('pageLoading');
	currentUser.then(function (currentUser) {
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
				age = moment() - moment($scope.user.birthday.date);
				$scope.user.age = Math.floor(age / (1000 * 60 * 60 * 24 * 365));
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