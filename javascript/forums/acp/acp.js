$(function () {
	$('a.sprite.cross, a.newPermission, a.permission_delete').colorbox();
	$('a.permission_edit').click(function (e) {
		e.preventDefault();

		$permissions = $(this).parent().parent().children('.permissions');
		$('#permissions .permissions').not($permissions).hide();
		$permissions.toggle();
	});
});

controllers.controller('forums_acp', function ($scope, $http, $sce, $filter, $timeout, currentUser) {
	pathElements = getPathElements();
	$scope.forumID = pathElements[2];
	$scope.currentSection = 'details';
	$scope.list = {};
	$scope.details = {};
	$scope.permissions = {};
	if (pathElements[3] != null && ['details', 'subforums', 'permissions'].indexOf(pathElements[3]) != -1) 
		$scope.currentSection = pathElements[3];
	$scope.setSection = function (section) {
		if (['details', 'subforums', 'permissions'].indexOf(section) != -1) 
			$scope.currentSection = section;
	}
	currentUser.then(function (currentUser) {
		$http.post(API_HOST + '/forums/acp/details/', { forumID: $scope.forumID }).success(function (data) {
			if (data.failed) {
				document.location = '/forums/';
			} else {
				$scope.list = data.list;
				$scope.details = data.details;
				$scope.permissions = data.permissions;
			}
		});
	});
	$scope.saveDetails = function () {
		console.log($scope.details);
	}
});