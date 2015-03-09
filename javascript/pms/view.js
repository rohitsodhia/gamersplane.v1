controllers.controller('pmView', function ($scope, $cookies, $http) {
	function getPMs(box) {
		$http.post('http://api.gamersplane.local/pms/view/', { loginHash: $cookies.loginHash, box: box }).success(function (data) {
			data.pms.forEach(function (value, key) {
				data.pms[key].datestamp = convertTZ(value.datestamp, 'YYYY-MM-DD HH:mm:ss', 'MMMM D, YYYY h:mm a')
			});
			$scope.pms = data.pms;
		});
	}

	pathElements = getPathElements();

	
});
