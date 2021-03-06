controllers.controller('pmView', function ($scope, $cookies, $http, $sce, DeletePM) {
	pathElements = getPathElements();

	$scope.allowDelete = true;
	$scope.hasHistory = false;
	$http.post(API_HOST + '/pms/view/', { pmID: pathElements[2], markRead: true }).success(function (data) {
		if (data.failed || data.noPM) 
			window.location.href = '/pms/';

		data.datestamp = convertTZ(data.datestamp, 'YYYY-MM-DD HH:mm:ss', 'MMMM D, YYYY h:mm a');
		for (key in data) 
			$scope[key] = data[key];

		if (data.history != null) {
			$scope.hasHistory = true;
			for (key in $scope.history) 
				$scope.history[key].datestamp = convertTZ($scope.history[key].datestamp, 'YYYY-MM-DD HH:mm:ss', 'MMMM D, YYYY h:mm a');
		}
	});

	$scope.delete = function () {
		DeletePM(pathElements[2]).success(function (data) {
			if (!isNaN(data.deleted)) 
				window.location.href = '/pms/';
		});
	}
});
