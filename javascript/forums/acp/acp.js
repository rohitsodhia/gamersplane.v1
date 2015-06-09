$(function () {
	$('#controls a').click(function (e) {
		e.preventDefault();

		oldOpen = $('#controls .current').removeClass('current').attr('class');
		newOpen = $(this).attr('class');
		$(this).addClass('current');

		$('span.' + oldOpen + ', div.' + oldOpen + ', form.' + oldOpen).hide();
		$('span.' + newOpen + ', div.' + newOpen + ', form.' + newOpen).show();

	});

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
	if (pathElements[3] != null && ['details', 'subforums', 'permissions'].indexOf(pathElements[3]) != -1) 
		$scope.currentSection = pathElements[3];
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
});