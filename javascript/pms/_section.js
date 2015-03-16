app.factory('DeletePM', ['$http', function ($http) {
	return function (pmID) {
		return $http.post(API_HOST + '/pms/delete/', { pmID: pmID });
	}
}]);