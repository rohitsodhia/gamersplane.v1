controllers.controller('subscriptions', ['$scope', '$http', '$filter', 'CurrentUser', 'ForumsService', function ($scope, $http, $filter, CurrentUser, ForumsService) {
	$scope.$emit('pageLoading');
	CurrentUser.load().then(function () {
		$scope.CurrentUser = CurrentUser.get();
		ForumsService.getSubscriptions({ userID: $scope.CurrentUser.userID }).then(function (data) {
			$scope.$emit('pageLoading');
			$scope.forums = data.forums;
			$scope.threads = data.threads;
		});
	});

	$scope.unsubscribe = function (type, id, item) {
		ForumsService.unsubscribe($scope.CurrentUser.userID, type[0], id).then(function (data) {
			if (data.success) {
				if (type == 'forum') 
					removeForum(item);
				else {
					forumID = item.forumID;
					forum = $filter('filter')($scope.threads, { forumID: forumID })[0];
					key = $scope.threads.indexOf(forum);
					removeEle($scope.threads[key].threads, item);
					if ($scope.threads[key].threads.length == 0) 
						$scope.threads.splice(key, 1);
				}
			}
		});
	}

	function removeForum(item) {
		children = $filter('filter')($scope.forums, { parentID: item.forumID });
		if (children.length) 
			item.isSubbed = false;
		else {
			parentID = item.parentID;
			removeEle($scope.forums, item);
			children = $filter('filter')($scope.forums, { parentID: parentID });
			parent = $filter('filter')($scope.forums, { forumID: parentID })[0];
			if (children.length == 0 && !parent.isSubbed) {
				removeForum(parent);
			}
		}
	}
}]);