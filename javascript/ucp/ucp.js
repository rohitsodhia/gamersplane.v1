controllers.controller('ucp', ['$scope', '$http', 'CurrentUser', 'UsersService', function ($scope, $http, CurrentUser, UsersService) {
	$scope.$emit('pageLoading');
	CurrentUser.load().then(function () {
		$scope.CurrentUser = CurrentUser.get();
		$scope.admin = !isUndefined($scope.CurrentUser.acpPermissions) && $scope.CurrentUser.acpPermissions !== null && ($scope.CurrentUser.acpPermissions.indexOf('users') || $scope.CurrentUser.acpPermissions.indexOf('all'))?true:false;
		var userID = null;
		var pathElements = getPathElements();
		if (!isUndefined(pathElements[1])) {
			userID = parseInt(pathElements[1]);
		} else {
			userID = $scope.CurrentUser.userID;
		}
		if (
			$scope.CurrentUser.userID != userID &&
			(
				typeof $scope.CurrentUser.acpPermissions == 'undefined' ||
				$scope.CurrentUser.acpPermissions === null ||
				(
					$scope.CurrentUser.acpPermissions.indexOf('users') == -1 &&
					$scope.CurrentUser.acpPermissions.indexOf('all') == -1
				)
			)
		) {
			window.location.href = '/user/' + (userID !== null?userID + '/':'');
		}
		$scope.user = null;
		$scope.newPass = { 'oldPassword': '', 'password1': '', 'password2': '' };
		$scope.newAvatar = null;
		$scope.avatarTime = new Date().getTime();

		UsersService.get(userID).then(function (data) {
			if (data) {
				$scope.user = data;
				if ($scope.user.birthday.date) {
					birthday = $scope.user.birthday.date.split('-');
					$scope.user.birthday.date = {
						'month': birthday[1],
						'day': birthday[2],
						'year': birthday[0]
					};
				}
				$scope.$emit('pageLoading');
			}
		});

		$scope.passMismatch = false;
		$scope.samePass = function () {
			if ($scope.newPass.password1.length >= 6 && $scope.newPass.password1.length <= 32) {
				$scope.passMismatch = $scope.newPass.password1 != $scope.newPass.password2;
			} else if ($scope.newPass.password1.length < 6 && $scope.newPass.password1.length > 32) {
				$scope.passMismatch = false;
			}
		};

		$scope.save = function () {
			UsersService.save({
				'details': $scope.user,
				'newPass': $scope.newPass
			}, $scope.newAvatar).then(function (data) {
				data = data.data;
				if (data.avatarUploaded) {
					$scope.avatarTime = new Date().getTime();
				}
			});
		};
	});
}]);
