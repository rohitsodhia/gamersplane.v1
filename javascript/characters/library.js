controllers.controller('charLibrary', ['$scope', 'CurrentUser', 'CharactersService', 'SystemsService', function ($scope, CurrentUser, CharactersService, SystemsService) {
	$scope.characters = [];
	$scope.systems = [];
	$scope.search = { 'systems': [] };
	$scope.$emit('pageLoading');
	$scope.testVar = false;
	var loadingFinished = 0;
	CurrentUser.load().then(function () {
		var waitingLoadingFinish = $scope.$watch(function () { return loadingFinished; }, function () {
			if (loadingFinished == 2) {
				$scope.$emit('pageLoading');
				waitingLoadingFinish();
			}
		});
		SystemsService.get({ 'getAll': true, 'basic': true }).then(function (data) {
			for (key in data.systems) 
				$scope.systems.push({ 'slug': data.systems[key].shortName, 'name': data.systems[key].fullName });
			loadingFinished++;
		});
		CharactersService.getLibrary().then(function (data) {
			if (data.success) 
				$scope.characters = data.characters;
			loadingFinished++;
		});
	});
	$scope.filterLibrary = function () {
		filter = {};
		if ($scope.search.systems.length) 
			filter.search = $scope.search.systems;
		CharactersService.getLibrary(filter).then(function (data) {
			if (data.success) 
				$scope.characters = data.characters;
		});
	}
}]);