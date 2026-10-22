app.factory('DeletePM', ['$http', function ($http) {
	return function (pmID) {
		return $http.delete(APIV2_HOST + '/legacy/pms/' + pmID);
	}
}]);
