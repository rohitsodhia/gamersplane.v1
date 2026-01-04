controllers.controller('pmList', ['$scope', '$http', 'CurrentUser', 'DeletePM', function ($scope, $http, CurrentUser, DeletePM) {
	pathElements = getPathElements();
	$scope.pagination = { numItems: 0, itemsPerPage: PAGINATE_PER_PAGE };
	CurrentUser.load().then(function () {
		$scope.CurrentUser = CurrentUser.get();
		if (!$scope.CurrentUser)
			window.location.href = '/';

		if ($.urlParam('page'))
			$scope.pagination.current = parseInt($.urlParam('page'));
		else
			$scope.pagination.current = 1;
		$scope.box = pathElements[1] == 'Outbox' ? 'Outbox' : 'Inbox';

		$scope.spinnerPause = true;
		$scope.getPMs = function () {
			$scope.spinnerPause = false;
			$scope.$emit('pageLoading');
			$http.get(APIV2_HOST + '/legacy/pms', { params: { box: $scope.box.toLowerCase(), page: $scope.pagination.current } }).success(function (data) {
				data.pms.forEach(function (value, key) {
					data.pms[key].datestamp = convertTZ(value.datestamp, 'YYYY-MM-DD HH:mm:ss', 'MMMM D, YYYY h:mm a')
				});
				console.log(data.pms);
				$scope.pms = data.pms;
				$scope.pagination.numItems = data.totalCount;
				$scope.$emit('pageLoading');
				$scope.spinnerPause = true;
			});
		};
		$scope.getPMs();

		$scope.switchBox = function ($event, box) {
			$event.preventDefault();
			newBox = box.capitalizeFirstLetter();
			if ($scope.box != newBox) {
				$scope.box = newBox;
				$scope.getPMs();
			}
		};

		$scope.delete = function (pmID) {
			DeletePM(pmID).success(function (data) {
				$scope.getPMs();
			});
		};
	});
}]);
