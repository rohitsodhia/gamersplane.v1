controllers.controller('user', ['$scope', '$http', 'CurrentUser', 'UsersService', function ($scope, $http, CurrentUser, UsersService) {
	$scope.$emit('pageLoading');
	CurrentUser.load().then(function () {
		$scope.user = null;
		$scope.systems = {};
		$scope.profileFields = { 'location': 'Location', 'aim': 'AIM', 'yahoo': 'Yahoo!', 'msn': 'MSN', 'games': 'Games' };

		pathElements = getPathElements();
		userID = null;
		if (!isUndefined(pathElements[1])) 
			userID = parseInt(pathElements[1]);
		UsersService.get(userID).then(function (data) {
			if (data) {
				$scope.user = data;
				$scope.user.lastActivity = UsersService.inactive($scope.user.lastActivity, false);
				$http.post(API_HOST + '/users/stats/', { userID: userID }).then(function (response) {
					$scope.characters = response.data.characters.list;
					$scope.posts = {
						postCount: response.data.posts.count
					};
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