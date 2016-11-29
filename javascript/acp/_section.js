controllers.controller('acp_users', ['$scope', '$timeout', 'UsersService', 'Range', function ($scope, $timeout, UsersService, Range) {
	$scope.loadUsers = function (loadType) {
		if (['all', 'active', 'inactive', 'suspended'].indexOf(loadType) == -1)
			$scope.loadType = 'all';
		else
			$scope.loadType = loadType;
		$scope.$emit('pageLoading');
		UsersService.search({
			'search': $scope.search,
			'loadType': $scope.loadType,
			'fields': 'userID, username, activatedOn, suspendedUntil, banned',
			'limit': $scope.pagination.itemsPerPage,
			'page': $scope.pagination.current,
			'md5': true
		}).then(function (data) {
			$scope.users = data.users;
			$scope.pagination.numItems = data.numUsers;
			for (var key in $scope.users) {
				if ($scope.users[key].suspendedUntil)
					$scope.users[key].suspendedUntil *= 1000;
				$scope.users[key].showForm = null;
			}
			$scope.$emit('pageLoading');
		});
	};
	$scope.suspend = function (user) {
		if (user.showForm != 'suspend') {
			$scope.suspendUntil = {
				'month': curDate.getMonth() + 1,
				'day': curDate.getDate(),
				'year': curDate.getFullYear(),
				'hour': curDate.getHours(),
				'minutes': curDate.getMinutes()
			};
			user.showForm = 'suspend';
		} else {
			$user.showForm = null;
		}
	};
	$scope.setDatePart = function(suspendUntil, part, value) {
		suspendUntil[part] = value;
	};
	$scope.confirmSuspend = function (user) {
		if (user.suspendedUntil === null) {
			suspendDate = new Date($scope.suspendUntil.year, $scope.suspendUntil.month - 1, $scope.suspendUntil.day, $scope.suspendUntil.hour, $scope.suspendUntil.minutes);
		} else {
			suspendDate = null;
		}
		UsersService.suspend(user.userID, moment(suspendDate).utc().unix()).then(function (data) {
			if (data.suspended !== null) {
				user.suspendedUntil = data.suspended * 1000;
			} else {
				user.suspendedUntil = null;
			}
			user.showForm = null;
		});
	};
	$scope.getActivation = function (user) {
		if (user.showForm != 'activationLink') {
			user.showForm = 'activationLink';
		} else
			user.showForm = null;
	};
	$timeout(function () {
		$('#userList').on('click', '.activationLink input', function () {
			if (document.selection) {
		        document.selection.empty();
		    } else if (window.getSelection) {
		        window.getSelection().removeAllRanges();
		    }
			$(this).select();
		}).on('keydown keypress', '.activationLink input', function ($event) {
			$event.preventDefault();
		});
	});
	var searchTimeout = null;
	$scope.searchChange = function () {
		$timeout.cancel(searchTimeout);
		searchTimeout = $timeout(function () {
			$scope.loadUsers($scope.loadType);
		}, 500);
	};

	$scope.users = [];
	$scope.range = Range.get;
	$scope.pagination = {
		'numItems': 0,
		'itemsPerPage': 25,
		'current': 1
	};
	$scope.loadType = 'all';
	$scope.search = '';
	var curDate = new Date();
	$scope.suspendUntil = {
		'month': 1,
		'day': 1,
		'year': 1,
		'hour': 0,
		'minutes': 0
	};
	$scope.combobox = {
		'values': {
			'month': Range.get(1, 12),
			'day': Range.get(1, 31),
			'year': Range.get(curDate.getFullYear(), curDate.getFullYear() + 2)
		}
	};
	$scope.loadUsers();
}]).controller('acp_autocomplete', ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {
	$scope.$emit('pageLoading');
	$scope.newItems = [];
	$scope.addToSystem = [];
	$http.post(API_HOST + '/characters/getUAI/').then(function (data) {
		data = data.data;
		$scope.$emit('pageLoading');
		$scope.newItems = data.newItems;
		addToSystem = {};
		data.addToSystem.forEach(function (item) {
			if (typeof addToSystem[item.type] == 'undefined')
				addToSystem[item.type] = [];
			addToSystem[item.type].push(item);
		});
		angular.forEach(addToSystem, function (items, key) {
			$scope.addToSystem.push({ 'type': key, 'items': items });
		});
	});

	$scope.processUAI = function (item, action) {
		item.action = action;
		$http.post(API_HOST + '/characters/processUAI/', { 'item': item, 'action': action }).then(function (data) {
			data = data.data;
			if (data.success) {
				if (item.itemID) {
					var sub = null;
					$scope.addToSystem.forEach(function (set) {
						if (set.type == item.type)
							sub = set.items;
					});
					removeEle(sub, item);
				} else
					removeEle($scope.newItems, item);
			}
		});
	};
/*		$('#newItems').on('click', '.actions a', function (e) {
			e.preventDefault();

			var $itemRow = $(this).closest('.newItem'), postData = { uItemID: $itemRow.attr('id').split('_')[1], name: $itemRow.children('input').val() };
			if ($(this).hasClass('check')) postData['action'] = 'add';
			else if ($(this).hasClass('cross')) postData['action'] = 'reject';
			$.post('/acp/process/newItem/', postData, function (data) {
				$itemRow.remove();
			});
		});
		$('#addToSystem').on('click', '.actions a', function (e) {
			e.preventDefault();

			var $itemRow = $(this).closest('.item'), postData = { uItemID: $itemRow.attr('id').split('_')[1], name: $itemRow.children('input').val() };
			if ($(this).hasClass('check')) postData['action'] = 'add';
			else if ($(this).hasClass('cross')) postData['action'] = 'reject';
			$.post('/acp/process/addToSystem/', postData, function (data) {
				$itemRow.remove();
			});
		});*/
}]).controller('acp_systems', ['$scope', '$http', '$sce', '$timeout', 'SystemsService', function ($scope, $http, $sce, $timeout, SystemsService) {
	$scope.selectSystem = {
		'data': [],
		'value': ''
	};
	$scope.setSelectSystem = function (system) {
		$scope.selectSystem.value = system;
	};
	function loadSystems() {
		SystemsService.get({ 'getAll': true }).then(function (data) {
			systems = data.systems;
			$scope.selectSystem.data = [];
			for (var key in systems) {
				if (systems[key].shortName != 'custom')
					$scope.selectSystem.data.push({
						'value': systems[key].shortName,
						'display': systems[key].fullName
					});
			}
			$scope.selectSystem.data.push({ 'value': 'custom', 'display': 'Custom' });
		});
	}
	loadSystems();
	$scope.newGenre = {
		'data': [],
		'value': {}
	};
	function getGenres() {
		SystemsService.getGenres().then(function (data) {
			$scope.allGenres = [];
			$scope.newGenre.data = [];
			for (var key in data) {
				$scope.allGenres.push(data[key]);
				$scope.newGenre.data.push(data[key]);
			}
		});
	}
	getGenres();
	$scope.newSystem = true;
	$scope.edit = {};
	$scope.allGenres = [];
	$scope.saveSuccess = false;

	$scope.loadSystem = function () {
		if ($scope.selectSystem.value === null) {
			return;
		}
		SystemsService.get({ 'shortName': $scope.selectSystem.value }).then(function (data) {
			$scope.newSystem = false;
			$scope.edit = data.systems[0];
//			$scope.selectSystem.search = '';
			$scope.newGenre.search = '';
			updateGenres();
		});
	};
	$scope.setNewSystem = function () {
		$scope.newSystem = true;
		$scope.edit = {};
	};

	$scope.saveStatusBtn = 'cancel';
	$scope.setEditBtn = function (type) {
		$scope.saveStatusBtn = type;
	};

	function updateGenres() {
		$scope.newGenre.data = [];
		for (var key in $scope.allGenres) {
			if ($scope.edit.genres.indexOf($scope.allGenres[key]) == -1) {
				$scope.newGenre.data.push($scope.allGenres[key]);
			}
		}
	}
	$scope.setNewGenre = function (value) {
		$scope.newGenre.value = value;
	};
	$scope.addGenre = function () {
		if (typeof $scope.edit.genres == 'undefined') {
			$scope.edit.genres = [];
		}
		if ($scope.newGenre.value.length === 0) {
			return;
		}
		$scope.edit.genres.push($scope.newGenre.value);
		updateGenres();
	};
	$scope.removeGenre = function (genre) {
		index = $scope.edit.genres.indexOf(genre);
		if (index >= 0)
			$scope.edit.genres.splice(index, 1);
		updateGenres();
	};
	$scope.addBasic = function () {
		if (typeof $scope.edit.basics == 'undefined')
			$scope.edit.basics = [];
		if (typeof $scope.edit.newBasic == 'undefined' || $scope.edit.newBasic.text.length === 0 || $scope.edit.newBasic.site.length === 0)
			return false;
		$scope.edit.basics.push($scope.edit.newBasic);
		$scope.edit.newBasic = { 'text': '', 'site': '' };
	};
	$scope.removeBasic = function (basic) {
		index = $scope.edit.basics.indexOf(basic);
		if (index >= 0)
			$scope.edit.basics.splice(basic, 1);
	};
	$scope.saveSystem = function () {
		if ($scope.saveStatusBtn != 'save')
			return;

		SystemsService.save($scope.edit).then(function (data) {
			$scope.edit = {};
			$scope.saveSuccess = true;
			$scope.newSystem = true;
			getGenres();
			loadSystems();
			$timeout(function () { $scope.saveSuccess = false; }, 1500);
		});
	};
}]).controller('acp_links', ['$scope', '$http', '$sce', '$filter', 'Links', function ($scope, $http, $sce, $filter, Links) {
	$scope.links = [];
	$scope.newLink = {};
	$scope.search = '';
	Links.get().then(function (data) {
		$scope.links = data.data.links;
		$scope.links.forEach(function (ele) {
			ele.level = { 'value': ele.level, 'display': ele.level };
		});
		$scope.pagination.numItems = data.data.totalCount;
	});
	$scope.pagination = { numItems: 0, itemsPerPage: 20 };
	if ($.urlParam('page')) {
		$scope.pagination.current = parseInt($.urlParam('page'));
	} else {
		$scope.pagination.current = 1;
	}

	$scope.$watch(function () { return $scope.search; }, function () {
		$scope.pagination.numItems = $filter('filter')($scope.links, { 'title': $scope.search }).length;
	});
}]).directive('linksEdit', ['$filter', '$http', 'Upload', function ($filter, $http, Upload) {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/acp/links.php',
		scope: {
			'data': '=data',
		},
		link: function (scope, element, attrs) {
			scope.editing = false;
			scope.showEdit = false;
			scope.showDelete = false;
			scope.levels = ['Link', 'Affiliate', 'Partner'];
			scope.categories = ['Blog', 'Podcast', 'Videocast', 'Liveplay', 'Devs', 'Accessories'];
			if (!isUndefined(attrs.new)) {
				scope.new = true;
				scope.editing = true;
				scope.data = {
					'title': '',
					'url': '',
					'level': { 'id': 'link', 'display': 'Link' },
					'networks': [],
					'categories': []
				};
			} else {
				scope.new = false;
			}

			scope.setLevel = function (value) {
				scope.data.level = value;
			};

			scope.toggleEditing = function () {
				scope.showEdit = !scope.showEdit;
				scope.editing = !scope.editing;
			};
			scope.saveLink = function () {
				data = copyObject(scope.data);
				delete data.image;
				data.level = data.level.display;
				Upload.upload({
					'url': API_HOST + '/links/save/',
					'file': scope.data.newImage,
					'fields': data,
					'sendFieldsAs': 'form'
				}).success(function (data) {
					if (scope.new) {
						window.location.reload();
					} else {
						if (data.image) {
							scope.data.image = data.image;
						}
						scope.toggleEditing();
					}
				});
			};
			scope.deleteImage = function () {
				$http.post(API_HOST + '/links/deleteImage/', { '_id': scope.data._id }).success(function (data) {
					delete scope.data.image;
				});
			};
			scope.deleteLink = function () {
				$http.post(API_HOST + '/links/deleteLink/', { '_id': scope.data._id }).success(function (data) {
					window.location.reload();
				});
			};
		}
	};
}]).controller('acp_music', ['$scope', '$http', '$sce', function ($scope, $http, $sce) {
	$scope.music = [];
	$scope.newSong = { 'url': '', 'title': '', 'lyrics': false, 'battlebards': false, 'genres': [], 'notes': '' };
	$scope.pagination = { numItems: 0, itemsPerPage: 10 };
	if ($.urlParam('page'))
		$scope.pagination.current = parseInt($.urlParam('page'));
	else
		$scope.pagination.current = 1;
	$scope.showPagination = true;
	function loadMusic() {
		$http.post(API_HOST + '/music/get/', { 'page': $scope.pagination.current }).success(function (data) {
			if (data.success) {
				$scope.music = data.music;
				$scope.pagination.numItems = data.count;
			}
		});
	}
	loadMusic();

	$scope.showEdit = null;
	$scope.addSong = function () {
		$scope.showEdit = 'new';
		$scope.$broadcast('resetSongForm', 'new');
	};
	$scope.editSong = function (id) {
		$scope.showEdit = $scope.showEdit != id?id:null;
		if ($scope.showEdit !== null)
			$scope.$broadcast('resetSongForm', id);
	};
	$scope.toggleApproval = function (song) {
		$http.post(API_HOST + '/music/toggleApproval/', { 'id': song._id, approved: song.approved }).success(function (data) {
			if (data.success)
				song.approved = !song.approved;
		});
	};
	$scope.$on('closeSongEdit', function (event) {
		$scope.showEdit = null;
	});
	$scope.$on('addNew', function (event) {
		loadMusic();
		$scope.newSong = { 'url': '', 'title': '', 'lyrics': false, 'battlebards': false, 'genres': [], 'notes': '' };
	});
}]).controller('acp_faqs', ['$scope', '$http', '$filter', 'faqs', function ($scope, $http, $filter, faqs) {
	$scope.categories = [];
	$scope.catMap = {};
	for (var key in faqs.categories) {
		$scope.categories.push({ 'value': key, 'display': faqs.categories[key] });
		$scope.catMap[key] = faqs.categories[key];
	}
	$scope.aFAQs = [];
	faqs.get().then(function (data) {
		if (data.faqs) {
			$scope.aFAQs = data.faqs;
		}
	});
	$scope.editing = null;
	$scope.editHold = null;
	$scope.editFAQ = function(faq) {
		$scope.editing = faq._id;
		$scope.editHold = faq;
	};
	$scope.moveUp = function (faq, cFAQs) {
		faqs.changeOrder(faq._id, 'up').then(function (data) {
			order = faq.order;
			sFAQ = $filter('filter')(cFAQs, { 'order': order - 1 });
			faq.order = faq.order - 1;
			sFAQ[0].order = sFAQ[0].order + 1;
		});
	};
	$scope.moveDown = function (faq, cFAQs) {
		faqs.changeOrder(faq._id, 'down').then(function (data) {
			order = faq.order;
			sFAQ = $filter('filter')(cFAQs, { 'order': order + 1 });
			faq.order = faq.order + 1;
			sFAQ[0].order = sFAQ[0].order - 1;
		});
	};
	$scope.saveFAQ = function (faq) {
		faqs.update(faq).then(function (data) {
			if (data.success) {
				faq = data.faq;
				$scope.editing = null;
				$scope.editHold = null;
			}
		});
	};
	$scope.cancelSave = function () {
		$scope.editing = null;
		$scope.editHold = null;
	};
	$scope.deleteFAQ = function (id, cFAQs, index) {
		faqs.delete(id).then(function (data) {
			if (data.success)
				cFAQs.splice(index, 1);
		});
	};

	$scope.newFAQ = {
		'category': '',
		'question': '',
		'answer': ''
	};
	$scope.setCategory = function (value) {
		$scope.newFAQ.category = value;
	};
	$scope.createFAQ = function () {
		if ($scope.newFAQ.question.length === 0 || $scope.newFAQ.answer.length === 0) {
			return false;
		}
		faqs.create($scope.newFAQ).then(function (data) {
			$scope.aFAQs[data.faq.category].push(data.faq);
		});
	};
}]);
