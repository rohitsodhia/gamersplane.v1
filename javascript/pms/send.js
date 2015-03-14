controllers.controller('pmSend', function ($scope, $cookies, $http, $location, $compile) {
	pathElements = getPathElements();

	$scope.headerTitle = pathElements[1] == 'reply'?'Reply':'Send Private Message';

	var userID = null;
	$scope.replyTo = 0
	if (typeof $location.search().userID != 'undefined') 
		userID = $location.search().userID;
	$scope.username = '';
	if (userID != null) {
		$http.get(API_HOST + '/users/search/', { params: { search: userID, searchBy: 'userID', exact: true } }).success(function (data) {
			if (!isNaN(data.noUsers)) 
				$scope.username = data.users[0].username;
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
			$http.post(API_HOST + '/pms/send/', { username: $scope.username, title: $scope.title, message: $scope.message }).success(function (data) {
				sendingPM = false;
			});
		}
	}

	$('#messageTextArea').markItUp(mySettings);
	$compile($('#messageTextArea'))($scope);
/*	$scope.allowDelete = true;
	$http.post('http://api.gamersplane.local/pms/view/', { loginHash: $cookies.loginHash, pmID: pathElements[2] }).success(function (data) {
		if (data.failed || data.noPM) 
			document.location = '/pms/';

		data.datestamp = convertTZ(data.datestamp, 'YYYY-MM-DD HH:mm:ss', 'MMMM D, YYYY h:mm a');
		for (key in data) 
			$scope[key] = data[key];

		replyTo = parseInt(data.replyTo);
		if (!isNaN(replyTo)) {
			$scope.history = new Array();
			$scope.hasHistory = true;
			$http.post('http://api.gamersplane.local/pms/view/', { loginHash: $cookies.loginHash, pmID: replyTo }).success(function (historyPM) {
				historyPM.datestamp = convertTZ(historyPM.datestamp, 'YYYY-MM-DD HH:mm:ss', 'MMMM D, YYYY h:mm a');
				replyTo = historyPM.replyTo;
				$scope.history.push(historyPM);
			});
		}
	});*/
});