app.factory('DeletePM', ['$http', function ($http) {
	return function (pmID) {
		return $http.post('http://api.gamersplane.local/pms/delete/', { pmID: pmID });
	}
}]);