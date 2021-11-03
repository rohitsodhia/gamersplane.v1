controllers.controller('charLibrary', ['$scope','$filter', 'CurrentUser', 'CharactersService', 'SystemsService', function ($scope, $filter, CurrentUser, CharactersService, SystemsService) {
	$scope.characters = [];
	$scope.systems = [];
	$scope.search = { 'systems': [] };
	$scope.$emit('pageLoading');
	$scope.testVar = false;
	$scope.pagination = { numItems: 0, itemsPerPage: 25 };
	$scope.filter = { search: '' };

	var loadingFinished = 0;
	CurrentUser.load().then(function () {
		var waitingLoadingFinish = $scope.$watch(function () { return loadingFinished; }, function () {
			if (loadingFinished == 2) {
				$scope.$emit('pageLoading');
				waitingLoadingFinish();
			}
		});
		SystemsService.get({ 'getAll': true }).then(function (data) {
			for (key in data.systems)
				$scope.systems.push({ 'slug': data.systems[key].shortName, 'name': data.systems[key].fullName });
			loadingFinished++;
		});
		CharactersService.getLibrary().then(function (data) {
			if (data.success){
				$scope.characters = data.characters;
				$scope.pagination.numItems = $scope.characters.length;
			}
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
	};
	$scope.$watch(function () { return $scope.filter.search; }, function () {
		$scope.pagination.numItems = $filter('filter')($scope.characters, { $: $scope.filter.search }).length;
		$scope.pagination.current = 1;
	});

}]);