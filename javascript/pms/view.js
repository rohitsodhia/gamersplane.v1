controllers.controller('pmView', function ($scope, $cookies, $http) {
	pathElements = getPathElements();

	$scope.allowDelete = true;
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
	});
});
