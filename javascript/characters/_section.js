app.service('character', ['$http', '$q', function ($http, $q) {
	this.load = function (characterID) {
		var deferred = $q.defer();
		$http.post(API_HOST + '/characters/load/', { 'characterID': characterID }).success(function (data) {
			deferred.resolve(data);
		});
		return deferred.promise;
	};
	this.save = function (characterID, character) {
		var deferred = $q.defer();
		$http.post(API_HOST + '/characters/save/', { 'characterID': characterID, 'character': character }).success(function (data) {
			deferred.resolve(data);
		});
		return deferred.promise;
	};
}]);