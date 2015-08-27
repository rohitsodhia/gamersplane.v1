app.service('character', ['$http', '$q', function ($http, $q) {
	this.load = function (characterID, blanks) {
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
	this.loadBlanks = function (character, blanks) {
		if (typeof blanks == 'undefined' || Object.keys(blanks).length == 0) 
			return;
		for (key in blanks) {
			if (key.indexOf('.') < 0) 
				bArray = character[key];
			else 
				bArray = character[key.split('.')[0]][key.split('.')[1]];
			if (typeof bArray != 'undefined' && Object.keys(bArray).length == 0) 
				bArray.push(copyObject(bArray));
		}
	}
}]);