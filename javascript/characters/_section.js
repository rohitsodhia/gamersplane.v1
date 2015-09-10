app.factory('character', ['$http', '$q', function ($http, $q) {
	return {
		'load': function (characterID, pr) {
			if (typeof pr != 'boolean') 
				pr = false;
			var deferred = $q.defer();
			$http.post(API_HOST + '/characters/load/', { 'characterID': characterID, 'printReady': pr }).success(function (data) { deferred.resolve(data); });
			return deferred.promise;
		},
		'save': function (characterID, character) {
			var deferred = $q.defer();
			$http.post(API_HOST + '/characters/save/', { 'characterID': characterID, 'character': character }).success(function (data) { deferred.resolve(data); });
			return deferred.promise;
		},
		'loadBlanks': function (character, blanks) {
			if (typeof blanks == 'undefined' || Object.keys(blanks).length == 0) 
				return;
			console.log(character);
			for (key in blanks) {
				if (key.indexOf('.') < 0) 
					bArray = character[key];
				else 
					bArray = character[key.split('.')[0]][key.split('.')[1]];
				if (typeof bArray != 'undefined' && Object.keys(bArray).length == 0) 
					bArray.push(copyObject(blanks[key]));
			}
		}
	}
}]);