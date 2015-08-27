$(function () {
	var $mainColumn = $('div.mainColumn');

	if ($('#page_acp_autocomplete').length) {
		$('#newItems').on('click', '.actions a', function (e) {
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
		});
	}

	if ($('#page_acp_faqs').length) {
		$('div.faq').on('click', '.display a, .inputs a', function (e) {
			e.preventDefault();

			$link = $(this);
			$faq = $link.closest('.faq');

			if ($link.hasClass('edit')) $link.closest('.faq').addClass('editing');
			else if ($link.hasClass('save')) {
				$.post('/acp/process/editFAQ/', { mongoID: $faq.data('questionId'), question: $faq.find('input').val(), answer: $faq.find('textarea').val() }, function (data) {
					$link.closest('.faq').removeClass('editing').find('.display .answer').html(data);
				});
			} else if ($link.hasClass('cancel')) $link.closest('.faq').removeClass('editing');
			else if ($link.hasClass('delete')) {
				$.post('/acp/process/deleteFAQ/', { mongoID: $faq.data('questionId') }, function (data) {
					$faq.remove();
				});
			}
		}).on('click', '.controls a', function (e) {
			e.preventDefault();

			$current = $(this).closest('.faq');
			if ($(this).hasClass('upArrow')) {
				$swap = $current.prev();
				if ($swap.length) $current.insertBefore($swap);
			} else {
				$swap = $current.next();
				if ($swap.length) $current.insertAfter($swap);
			}
			$.post('/acp/process/swapFAQ/', { mongoID1: $current.data('questionId'), mongoID2: $swap.data('questionId') }, function () {
				;
			});
		});
	}

	if ($('#page_acp_users').length) {
		var currentTab = 'active';
		$('#controls a').click(function (e) {
			e.preventDefault();

			currentTab = this.id.substring(9, this.id.length);
			$.post('/acp/ajax/listUsers/', { show: currentTab }, function (data) {
				$('div.mainColumn ul').html(data);
			});
		});

		$suspendDate = $('#suspendDate');
		$suspendDate.ajaxForm({
			beforeSubmit: function (arr, $form) {
				var error = false;
				$form.find('input[type="text"]').each(function () {
					if ((this.name == 'hour' && ($(this).val() < 0 || $(this).val() > 23)) || (this.name == 'minutes' && ($(this).val() < 0 || $(this).val() > 60))) error = true;
				});

				if (error) return false;
			},
			success: function (data) {
				if (data == 'suspended' && currentTab == 'active') {
					$li = $suspendDate.closest('li');
					$suspendDate.appendTo($mainColumn);
					$li.remove();
				}
//				document.location.reload();
			}
		});
		$('ul.prettyList').on('click', 'a.suspend', function (e) {
			e.preventDefault();

			$li = $(this).closest('li');

			if ($(this).text() == 'Suspend' && $li.find('form').length == 0) {
				$li.append($suspendDate);
				$suspendDate.find('#userID').val($li.data('id'));
			} else if ($(this).text() == 'Suspend' && $li.find('form').length == 1) {
				$suspendDate.appendTo($mainColumn);
			}
		});
	}
});

controllers.controller('acp_systems', ['$scope', '$http', '$sce', '$timeout', 'systems', function ($scope, $http, $sce, $timeout, systems) {
	$scope.selectSystem = {
		'data': [],
		'value': {},
		'search': ''
	};
	systems.get({ getAll: true }).then(function (data) {
		var systems = data.systems;
		$scope.selectSystem.data = [];
		for (key in systems) {
			if (systems[key].shortName != 'custom') 
				$scope.selectSystem.data.push({
					'value': systems[key].shortName, 
					'display': systems[key].fullName
				});
		}
		$scope.selectSystem.data.push({ 'value': 'custom', 'display': 'Custom' });
	});
	$scope.newGenre = {
		'data': [],
		'value': {},
		'search': ''
	};
	systems.getGenres().then(function (data) {
		$scope.allGenres = [];
		$scope.newGenre.data = [];
		for (key in data) {
			$scope.allGenres.push(data[key]);
			$scope.newGenre.data.push(data[key]);
		}
	});
	$scope.edit = {};
	$scope.allGenres = [];
	$scope.saveSuccess = false;

	$scope.saveStatusBtn = 'cancel';
	$scope.setEditBtn = function (type) {
		$scope.saveStatusBtn = type;
	}

	function updateGenres() {
		$scope.newGenre.data = [];
		for (key in $scope.allGenres) 
			if ($scope.edit.genres.indexOf($scope.allGenres[key]) == -1)
				$scope.newGenre.data.push({
					'value': $scope.allGenres[key],
					'display': $scope.allGenres[key]
				});
	}
	$scope.addGenre = function () {
		if (typeof $scope.edit.genres == 'undefined') 
			$scope.edit.genres = [];
		if ($scope.newGenre.value.display.length == 0) 
			return;
		$scope.edit.genres.push($scope.newGenre.value.display);
		updateGenres();
	}
	$scope.removeGenre = function (genre) {
		index = $scope.edit.genres.indexOf(genre);
		if (index >= 0) 
			$scope.edit.genres.splice(index, 1);
		updateGenres();
	}
	$scope.addBasic = function () {
		if (typeof $scope.edit.basics == 'undefined') 
			$scope.edit.basics = [];
		if (typeof $scope.edit.newBasic == 'undefined' || $scope.edit.newBasic.text.length == 0 || $scope.edit.newBasic.site.length == 0) 
			return false;
		$scope.edit.basics.push($scope.edit.newBasic);
		$scope.edit.newBasic = { 'text': '', 'site': '' };
	}
	$scope.removeBasic = function (basic) {
		index = $scope.edit.basics.indexOf(basic);
		if (index >= 0) 
			$scope.edit.basics.splice(basic, 1);
	}
	$scope.saveSystem = function () {
		if ($scope.saveStatusBtn != 'save') 
			return;

		$http.post(API_HOST + '/systems/save/', { system: $scope.edit }).success(function (data) {
			$scope.saveSuccess = true;
			getGeneres();
			$timeout(function () { $scope.saveSuccess = false; }, 1500);
		});
	}

	$scope.loadSystem = function () {
		if ($scope.selectSystem.value.value == null) 
			return;
		systems.get({ 'shortName': $scope.selectSystem.value.value }).then(function (data) {
			$scope.edit = data.systems[0];
//			$scope.selectSystem.search = '';
			$scope.newGenre.search = '';
			updateGenres();
		});
	}
}]).controller('acp_links', function ($scope, $http, $sce) {
	function getLinks() {
		$scope.links = [];
		$http.post(API_HOST + '/links/list/', { 'page': $scope.pagination.current }).success(function (data) {
			$(data.links).each(function (key, value) {
				value.level = { 'value': value.level, 'display': value.level }
				$scope.links.push(value);
			})

			$scope.pagination.numItems = data.totalCount;
		});
	}
	$scope.pagination = { numItems: 0, itemsPerPage: 10 };
	if ($.urlParam('page')) 
		$scope.pagination.current = parseInt($.urlParam('page'));
	else 
		$scope.pagination.current = 1;

	$scope.links = {};
	$scope.newLink = {};
	getLinks();
}).directive('linksEdit', ['$filter', '$http', 'Upload', function ($filter, $http, Upload) {
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
			} else 
				scope.new = false;

			scope.toggleEditing = function () {
				scope.showEdit = !scope.showEdit;
				scope.editing = !scope.editing;
			}
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
					if (scope.new) 
						document.location.reload();
					else {
						if (data.image) 
							scope.data.image = data.image;
						scope.toggleEditing();
					}
				});
			}
			scope.deleteImage = function () {
				$http.post(API_HOST + '/links/deleteImage/', { '_id': scope.data._id }).success(function (data) {
					delete scope.data.image;
				})
			}
			scope.deleteLink = function () {
				$http.post(API_HOST + '/links/deleteLink/', { '_id': scope.data._id }).success(function (data) {
					document.location.reload();
				})
			}
		}
	}
}]).controller('acp_music', function ($scope, $http, $sce) {
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
		if ($scope.showEdit != null) 
			$scope.$broadcast('resetSongForm', id);
	};
	$scope.toggleApproval = function (song) {
		$http.post(API_HOST + '/music/toggleApproval/', { 'id': song._id, approved: song.approved }).success(function (data) {
			if (data.success) 
				song.approved = !song.approved;
		})
	};
	$scope.$on('closeSongEdit', function (event) {
		$scope.showEdit = null;
	});
	$scope.$on('addNew', function (event) {
		loadMusic();
		$scope.newSong = { 'url': '', 'title': '', 'lyrics': false, 'battlebards': false, 'genres': [], 'notes': '' };
	});
});