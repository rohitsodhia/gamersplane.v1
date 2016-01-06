$(function () {
	$('a.sprite.cross, a.newPermission, a.permission_delete').colorbox();
});

controllers.controller('forums_acp', function ($scope, $http, $sce, $filter, $timeout, CurrentUser) {
	CurrentUser.load().then(function () {
		pathElements = getPathElements();
		$scope.forumID = pathElements[2]?parseInt(pathElements[2]):0;
		$scope.currentSection = 'details';
		$scope.list = {};
		$scope.details = {};
		$scope.editDetails = {};
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
		$scope.newGroupPermission = {
			'data': [],
			'value': {}
		};
		if (pathElements[3] != null && ['details', 'subforums', 'permissions'].indexOf(pathElements[3]) != -1) 
			$scope.currentSection = pathElements[3];
		else if (pathElements[3] == 'groups') 
			$scope.currentSection = null;
		$scope.setSection = function (section) {
			if (['details', 'subforums', 'permissions'].indexOf(section) != -1) 
				$scope.currentSection = section;
			else if ($scope.details.isGameForum && section == 'groups') 
				$scope.currentSection = 'groups';
		};
		$scope.getForumDetails = function (forumID, newSection) {
			$http.post(API_HOST + '/forums/acp/details/', { forumID: forumID }).success(function (data) {
				if (data.failed && $scope.details == {}) 
					window.location.href = '/forums/';
				else if (data.success) {
					if (typeof newSection == 'undefined' || ['details', 'subforums', 'groups', 'permissions'].indexOf(newSection) == -1) 
						$scope.currentSection = 'details';
					else 
						$scope.currentSection = newSection;
					if (data.details.parentID == 2 && $scope.currentSection == 'details') 
						$scope.currentSection = 'subforums';
					$scope.forumID = forumID;
					$scope.list = data.list;
					$scope.details = data.details;
					$scope.editDetails = { 'title': data.details.title, 'description': data.details.description };
					$scope.permissions = data.permissions;
				}
			});
		}
		$scope.getForumDetails($scope.forumID, $scope.currentSection);
		$scope.$watch(function () { return $scope.details.gameDetails; }, function (gameDetails) {
			$scope.newGroupPermission.data = [];
			if (gameDetails) {
				for (key in gameDetails.groups) 
					if (!gameDetails.groups[key].permissionSet) 
						$scope.newGroupPermission.data.push({
							'value': gameDetails.groups[key].groupID,
							'display': gameDetails.groups[key].name
						});
			}
		}, true);

		$scope.saveError = false;
		$scope.saveDetails = function () {
			if ($scope.editDetails.title.length >= 3 && ($scope.details.title != $scope.editDetails.title || $scope.details.description != $scope.editDetails.description)) {
				$http.post(API_HOST + '/forums/acp/updateForum/', { 'forumID': $scope.forumID, 'title': $scope.editDetails.title, 'desc': $scope.editDetails.description }).success(function (data) {
					if (data.success) {
						$scope.details.title = $scope.editDetails.title;
						$scope.details.description = $scope.editDetails.description;
						$scope.saveError = false;
					} else 
						$scope.saveError = true;
				});
			}
		};

		$scope.showForumDelete = null;
		$scope.changeOrder = function (direction, forum) {
			if ((direction == 'up' && forum.order == 1) || (direction == 'down' && forum.order == $scope.details.children.length) || ['up', 'down'].indexOf(direction) == -1) 
				return;

			$http.post(API_HOST + '/forums/acp/changeOrder/', { 'forumID': forum.forumID, 'direction': direction }).success(function (data) {
				if (data.success) {
					curPos = newPos = forum.order;
					newPos += direction == 'up'?-1:1;
					switchForum = $filter('filter')($scope.details.children, { 'order': newPos }, true)[0];
					forum.order = newPos;
					switchForum.order = curPos;
				}
			});
		};
		$scope.toggleForumDelete = function (forumID) {
			$scope.showForumDelete = $scope.showForumDelete != forumID?forumID:null;
		};
		$scope.cancelForumDelete = function () {
			$scope.showForumDelete = null;
		};
		$scope.confirmForumDelete = function (forum, key) {
			$http.post(API_HOST + '/forums/acp/deleteForum/', { 'forumID': forum.forumID }).success(function (data) {
				if (data.success) 
					$scope.details.children.splice(key, 1);
			})
		};
		$scope.newForum = { 'name': '' };
		$scope.createForum = function () {
			if ($scope.newForum.name.length < 3) 
				return;
			$http.post(API_HOST + '/forums/acp/createForum/', { 'parentID': $scope.forumID, 'name': $scope.newForum.name }).success(function (data) {
				if (data.success) 
					$scope.details.children.push(data.forum);
			});
		};

		$scope.cb_groups = '';
		$scope.renderedDirectives = { 'groups': false };
		$scope.newGroup = { 'name': '' };
		$scope.editingGroup = null;
		$scope.confirmGroupDelete = null;
		groupNameHold = '';
		$scope.createGroup = function () {
			if ($scope.newGroup.name.length < 3) 
				return;

			$http.post(API_HOST + '/forums/acp/createGroup/', { 'forumID': $scope.forumID, 'name': $scope.newGroup.name }).success(function (data) {
				if (data.success) {
					$scope.details.gameDetails.groups.push({ 'groupID': data.groupID, 'name': $scope.newGroup.name, 'permissionSet': false });
					$scope.newGroup.name = '';
				}
			});
		};
		$scope.editGroup = function (groupID, key) {
			$scope.editingGroup = groupID;
			groupNameHold = $scope.details.gameDetails.groups[key].name;
		};
		$scope.cancelEditing = function () {
			$scope.details.gameDetails.groups[key].name = groupNameHold;
			$scope.editingGroup = null;
		};
		$scope.saveGroup = function (groupID, key) {
			$http.post(API_HOST + '/forums/acp/editGroup/', { 'groupID': groupID, 'name': $scope.details.gameDetails.groups[key].name }).success(function (data) {
				if (data.success) {
					$scope.details.gameDetails.groups[key].name = data.name;
					groupNameHold = '';
					$scope.editingGroup = null;
				} else 
					$scope.details.gameDetails.groups[key].name = groupNameHold;
			});
		};
		$scope.deleteGroup = function (groupID) {
			$scope.confirmGroupDelete = groupID;
		};
		$scope.cancelDelete = function () {
			$scope.confirmGroupDelete = null;
		};
		$scope.confirmDelete = function (groupID, key) {
			$http.post(API_HOST + '/forums/acp/deleteGroup/', { 'groupID': groupID }).success(function (data) {
				if (data.success) 
					$scope.details.gameDetails.groups.splice(key, 1);
				$scope.confirmGroupDelete = null;
			});
		}

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
		};
		$scope.deletePermission = function (permission) {
			$http.post(API_HOST + '/forums/acp/deletePermission/', { 'type': permission.type, 'forumID': $scope.forumID, 'typeID': permission.id }).success(function (data) {
				if (data.success) {
					permissions = $scope.permissions[permission.type];
					type = permission.type;
					if (type == 'user') 
						type = 'player';
					for (key in permissions) {
						if (permissions[key].id == permission.id) {
							permissions.splice(key, 1);
							if (permission.type == 'group') {
								for (gKey in $scope.details.gameDetails.groups) {
									if ($scope.details.gameDetails.groups[gKey].groupID == permission.id) {
										$scope.details.gameDetails.groups[gKey].permissionSet = true;
										break;
									}
								}
							}
							break;
						}
					}
					if (permission.type != 'general') {
						details = $scope.details.gameDetails[type + 's'];
						for (key in details) {
							if (permission.userID == details[key].id) {
								details[key].permissionSet = false;
								break;
							}
						}
					}
				}
			});
		};
		$scope.addGroupPermission = function () {
			if ($scope.newGroupPermission.value.value != null) {
				$http.post(API_HOST + '/forums/acp/addPermission/', { 'type': 'group', 'forumID': $scope.forumID, 'typeID': $scope.newGroupPermission.value.value }).success(function (data) {
					if (data.success) {
						groupID = $scope.newGroupPermission.value.value;
						for (key in $scope.details.gameDetails.groups) {
							if ($scope.details.gameDetails.groups[key].groupID == groupID) {
								$scope.details.gameDetails.groups[key].permissionSet = true;
								break;
							}
						}
						data.newPermission.name = $scope.newGroupPermission.value.display;
						data.newPermission.ref = 'group_' + $scope.newGroupPermission.value.value;
						$scope.permissions.group.push(data.newPermission);
					}
				});
			}
		};
		$scope.addUserPermission = function (user) {
			if (user.userID) {
				$http.post(API_HOST + '/forums/acp/addPermission/', { 'type': 'user', 'forumID': $scope.forumID, 'typeID': user.userID }).success(function (data) {
					if (data.success) {
						details = $scope.details.gameDetails.players;
						for (key in details) {
							if (details[key].userID == user.userID) {
								details[key].permissionSet = true;
								break;
							}
						}
						data.newPermission.name = user.username;
						data.newPermission.ref = 'user_' + user.userID;
						$scope.permissions.user.push(data.newPermission);
					}
				})
			}
		}
	});
});