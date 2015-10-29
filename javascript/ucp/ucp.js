controllers.controller('ucp', ['$scope', '$http', 'currentUser', 'Users', function ($scope, $http, currentUser, Users) {
	$scope.$emit('pageLoading');
	currentUser.then(function (currentUser) {
		$scope.currentUser = currentUser.data;
		userID = null;
		if (!isUndefined(pathElements[1])) 
			userID = parseInt(pathElements[1]);
		if ($scope.currentUser.userID != userID && (isUndefined($scope.currentUser.acpPermissions) || ($scope.currentUser.acpPermissions.indexOf('users') == -1 && $scope.currentUser.acpPermissions.indexOf('all') == -1))) 
			window.location.href = '/user/' + userID + '/';
		$scope.admin = !isUndefined($scope.currentUser.acpPermissions) && $scope.currentUser.acpPermissions != null && ($scope.currentUser.acpPermissions.indexOf('users') || $scope.currentUser.acpPermissions.indexOf('all'))?true:false;
		$scope.user = null;
		$scope.newPass = { 'oldPassword': '', 'password1': '', 'password2': '' }
		$scope.newAvatar = null;
		$scope.avatarTime = new Date().getTime();
		pathElements = getPathElements();

		Users.get(userID).then(function (data) {
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
			if ($scope.newPass.password1.length >= 6 && $scope.newPass.password1.length <= 32) 
				$scope.passMismatch = $scope.newPass.password1 != $scope.newPass.password2;
			else if ($scope.newPass.password1.length < 6 && $scope.newPass.password1.length > 32) 
				$scope.passMismatch = false;
		};

		$scope.save = function () {
			Users.save({ 'details': $scope.user, 'newPass': $scope.newPass }, $scope.newAvatar).then(function (data) {
				data = data.data;
				if (data.avatarUploaded) 
					$scope.avatarTime = new Date().getTime();
			});
		};
	});
}]);