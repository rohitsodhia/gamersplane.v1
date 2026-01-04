controllers.controller('pmView', function ($scope, $cookies, $http, $sce, DeletePM) {
	pathElements = getPathElements();

	$scope.allowDelete = true;
	$scope.hasHistory = false;
	$http.get(APIV2_HOST + '/legacy/pms/' + pathElements[2]).success(function (data) {
		data = data.pm
		data.datestamp = convertTZ(data.datestamp, 'YYYY-MM-DD HH:mm:ss', 'MMMM D, YYYY h:mm a');
		for (key in data)
			$scope[key] = data[key];

		if (data.history != null) {
			$scope.hasHistory = true;
			for (key in $scope.history)
				$scope.history[key].datestamp = convertTZ($scope.history[key].datestamp, 'YYYY-MM-DD HH:mm:ss', 'MMMM D, YYYY h:mm a');
		}
	}).error(() => {
		window.location.href = '/pms/';
	});

	$scope.delete = function () {
		DeletePM(pathElements[2]).success(function (data) {
			window.location.href = '/pms/';
		});
	}
});
