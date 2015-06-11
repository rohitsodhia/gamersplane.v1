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
	$scope.permissionTypes = [
		{ 'key': 'read', 'label': 'Read' },
		{ 'key': 'write', 'label': 'Write' },
		{ 'key': 'editPost', 'label': 'Edit Post' },
		{ 'key': 'deletePost', 'label': 'Delete Post' },
		{ 'key': 'createThread', 'label': 'Create Thread' },
		{ 'key': 'deleteThread', 'label': 'Delete Thread' },
		{ 'key': 'addRolls', 'label': 'Add Rolls' },
		{ 'key': 'addDraws', 'label': 'Add Draws' },
		{ 'key': 'moderate', 'label': 'Moderate' }
	];
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
	};
	$scope.editingPermission = null;
	$scope.togglePermissionsEdit = function (permission) {
		if (permission.type == 'general') 
			curKey = 'general';
		else 
			curKey = permission.type + '_' + permission.id;
		if ($scope.editingPermission == null || $scope.editingPermission != curKey) 
			$scope.editingPermission = curKey;
		else 
			$scope.editingPermission = null;
	};
	$scope.changePermission = function (ref, pType, value) {
		type = ref.indexOf('_') >= 0?ref.split('_')[0]:'general';
		if (type == 'general') 
			$scope.permissions.general[pType] = value;
		for (key in $scope.permissions[type])
			if ($scope.permissions[type][key].ref == ref)
				$scope.permissions[type][key][pType] = value;
	};
	$scope.savePermission = function (permission) {
		$http.post(API_HOST + '/forums/acp/savePermission/', { forumID: $scope.forumID, permission: permission }).success(function (data) {
			if (data.success) 
				$scope.editingPermission = null;
		});
	}
});