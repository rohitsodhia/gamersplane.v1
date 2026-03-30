controllers.controller('pmSend', function ($scope, $cookies, $http, $compile) {
	pathElements = getPathElements();

	$scope.headerTitle = pathElements[1] == 'reply' ? 'Reply' : 'Send Private Message';

	var userID = null;
	if ($.urlParam('userID'))
		userID = $.urlParam('userID');
	$scope.username = '';
	if (userID != null) {
		$http.get(API_HOST + '/users/search/', { params: { search: userID, searchBy: 'userID', exact: true } }).success(function (data) {
			if (isNaN(data.noUsers))
				$scope.username = data.users[0].username;
		});
	}
	$scope.replyTo = null;
	$scope.hasHistory = false;
	if (pathElements[1] == 'reply') {
		$http.get(APIV2_HOST + '/legacy/pms/' + pathElements[2], { params: { includeSelfHistory: true } }).success(function (data) {
			$scope.replyTo = data.pm.id;
			$scope.username = data.pm.sender.username;
			$scope.title = (data.pm.title.substring(0, 3) == 'Re:' ? '' : 'Re: ') + data.pm.title;
			$scope.hasHistory = true;
			$scope.history = data.pm.history;
		});
	}
	$scope.title = '';
	$scope.message = '';
	$scope.formError = { 'validUser': true, 'validTitle': true, 'validMessage': true };
	$scope.checkUser = function () {
		if ($scope.username.length >= 3)
			$http.get(API_HOST + '/users/search/', { params: { search: $scope.username, exact: true } }).success(function (data) {
				if (!isNaN(data.noUsers))
					$scope.formError.validUser = false;
				else
					$scope.formError.validUser = true;
			});
		else
			$scope.formError.validUser = false;
	};
	$scope.checkTitle = function () {
		if ($scope.title.length > 0)
			$scope.formError.validTitle = true;
		else
			$scope.formError.validTitle = false;
	}
	var sendingPM = false;
	$scope.sendPM = function () {
		if (sendingPM) return;

		sendingPM = true;
		$scope.checkUser();
		$scope.checkTitle();
		if ($scope.message.length > 0)
			$scope.formError.validMessage = true;
		else
			$scope.formError.validMessage = false;
		if ($scope.formError.validUser && $scope.formError.validTitle && $scope.formError.validMessage) {
			$http.post(APIV2_HOST + '/legacy/pms', { username: $scope.username, title: $scope.title, message: $scope.message, reply_to_id: $scope.replyTo }).then(function (response) {
				if ("sent" in response.data)
					window.location.href = '/pms/?sent=1';
				sendingPM = false;
			}, function (response) {
				data = response.data;
				if ("errors" in data && "messagingSelf" in data.errors)
					$scope.formError.validUser = false;
				sendingPM = false;
			});
		}
	}

	$('#messageTextArea').markItUp(mySettings);
	$compile($('#messageTextArea'))($scope);
});
