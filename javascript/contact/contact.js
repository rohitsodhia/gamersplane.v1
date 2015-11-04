controllers.controller('contact', ['$scope', '$http', '$timeout', 'ContactService', 'CurrentUser', function ($scope, $http, $timeout, ContactService, CurrentUser) {
	$scope.dispForm = true;
	$scope.errors = {};
	$scope.$emit('pageLoading');
	CurrentUser.load().then(function () {
		$scope.$emit('pageLoading');
		$scope.CurrentUser = CurrentUser.get();
		$scope.loggedIn = $scope.CurrentUser?true:false;
		$timeout(function () { $('.animationFrame').height($('form').height() + 2); });

		$scope.send = function () {
			$scope.$emit('pageLoading');
			ContactService.send($scope.form).then(function (data) {
				$scope.$emit('pageLoading');
				if (data.success) 
					$scope.dispForm = false;
				else 
					$scope.errors = data.errors;
			});
		};
	});
}]);