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
				$scope.user.lastInactivity = UsersService.inactive($scope.user.lastActivity, false);
				$scope.user.lastActivity = lastActiveText($scope.user.lastActivity);
				$http.post(API_HOST + '/users/stats/', { userID: userID }).then(function (response) {
					$scope.characters = response.data.characters.list;
					$scope.posts = {
						postCount: response.data.posts.count,
						communityPostCount: response.data.posts.communityCount,
						gamePostCount: response.data.posts.gameCount
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
					$scope.activeGames = response.data.activeGames;
				});
				$scope.$emit('pageLoading');
			}
		});
	});

	var lastActiveText = function(lastActivity){
		if (typeof lastActivity == 'number')
			lastActivity *= 1000;
		lastActivity = moment(lastActivity);
		var now = moment();
		var diff = now - lastActivity;
		var diffSeconds = Math.floor(diff / 1000);
		diff = Math.floor(diffSeconds / (60 * 60 * 24));
		if (diff < 14)
		{
			if(diffSeconds<=86400){
				return "Less than 1 day ago";
			} else if(diffSeconds<=(86400*2)){
				return "1 day ago";
			} else {
				return diff + " days ago";
			}
		}

		return null;
	};

}]);