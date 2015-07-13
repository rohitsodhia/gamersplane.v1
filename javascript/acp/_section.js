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

	if ($('#page_acp_music').length) {
		var $editForm = $('#editMusicMaster');
		$editForm.ajaxForm({
			beforeSubmit: function (arr, $form) {
				var error = false;
				$form.find('input[type="text"]').each(function () {
					if ($(this).val().length == 0) error = true;
				});
				if (error) return false;
				error = true;
				$form.find('input[type="checkbox"]').each(function () {
					if ($(this).val() != 0 && $(this).attr('name').length) error = false;
				});
				if (error) return false;
			},
			success: function (data) {
				document.location.reload();
			}
		});
		$('.manageSong a').click(function (e) {
			e.preventDefault();

			var $link = $(this), action = $link.text().toLowerCase(), $li = $link.closest('li');
			if (action == 'delete') $link.hide().siblings('.confirmDelete').show();
			else if (action == 'deny') $link.parent().hide().siblings('.delete').show();
			else if (action == 'edit') {
				$link.closest('.songDetails').after($editForm);
				$editForm.find('#mongoID').val($li.data('id'));
				$editForm.find('#url').val($li.find('.song').attr('href'));
				$editForm.find('#title').val($li.find('.song').text());
				$editForm.find('input[type=radio]').prop('checked', false);
				if ($li.find('.song img').length) $editForm.find('#hasLyrics').prop('checked', true);
				else $editForm.find('#noLyrics').prop('checked', true);
				$editForm.find('.prettyRadio').each(syncRadio);
				genres = $li.find('.genres').text().split(',');
				for (i in genres) genres[i] = genres[i].trim();
				$editForm.find('#genres label').each(function () {
					if ($.inArray($(this).text(), genres) != -1) $(this).siblings('.prettyCheckbox').find('input').prop('checked', true);
					else $(this).siblings('.prettyCheckbox').find('input').prop('checked', false);
				});
				$editForm.find('.prettyCheckbox').each(syncRadio);
				$editForm.find('textarea').val($li.find('.notes').html());
			} else {
				if (action == 'confirm') action = 'delete';
				$.post('/acp/process/manageMusic/', { modal: true, mongoID: $link.closest('li').data('id'), action: action }, function (data) {
					if (data == 'Approve' || data == 'Unapprove') $link.text(data).closest('li').toggleClass('unapproved');
					else if (data == 'deleted') $link.closest('li').remove();
				});
			}
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

controllers.controller('acp_systems', function ($scope, $http, $sce, $timeout) {
	function getSystems() {
		$http.post(API_HOST + '/systems/search/', { getAll: true }).success(function (data) {
			$scope.systems = data.systems;
			$scope.combobox.systems = new Array();
			for (key in $scope.systems) {
				if ($scope.systems[key].shortName != 'custom') 
					$scope.combobox.systems.push({ 'id': $scope.systems[key].shortName, 'value': $scope.systems[key].fullName });
			}
			$scope.combobox.systems.push({ 'id': 'custom', 'value': 'Custom' });
		});
	}

	function getGeneres() {
		$http.post(API_HOST + '/systems/search/', { for: 'genres' }).success(function (data) {
			$scope.allGenres = $scope.combobox.genres = [];
			for (key in data) {
				$scope.allGenres.push(data[key]);
				$scope.combobox.genres.push({ 'id': data[key], 'value': data[key] });
			}
		});
	}

	$scope.combobox = {};
	$scope.combobox.search = { 'systems': '', 'genres': '' };
	getSystems();
	getGeneres();
	$scope.edit = {};
	$scope.allGenres = [];
	$scope.saveSuccess = false;

	$scope.showEdit = function (shortName) {
		$scope.edit = {};
		for (key in $scope.systems) {
			if ($scope.systems[key].shortName == shortName) {
				$scope.edit = $scope.systems[key];
				break;
			}
		}
	}

	$scope.saveStatusBtn = 'cancel';
	$scope.setEditBtn = function (type) {
		$scope.saveStatusBtn = type;
	}

	function updateGenres() {
		$scope.combobox.genres = [];
		for (key in $scope.allGenres) 
			if ($scope.edit.genres.indexOf($scope.allGenres[key]) == -1)
				$scope.combobox.genres.push({ 'id': $scope.allGenres[key], 'value': $scope.allGenres[key]});
	}
	$scope.addGenre = function () {
		if (typeof $scope.edit.genres == 'undefined') 
			$scope.edit.genres = [];
		if ($scope.newGenre.value.length == 0) 
			return;
		$scope.edit.genres.push($scope.newGenre.value);
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
		if ($scope.saveStatusBtn != 'save') return;

		$http.post(API_HOST + '/systems/save/', { system: $scope.edit }).success(function (data) {
			for (key in $scope.systems) {
				if ($scope.systems[key].shortName == data.shortName) {
					$scope.systems[key] = data;
					break;
				}
			}
			$scope.saveSuccess = true;
			getGeneres();
			$timeout(function () { $scope.saveSuccess = false; }, 1500);
		});
	}

	$scope.loadSystem = function () {
		$scope.showEdit($scope.systemSearch.value);
		$scope.combobox.search = { 'systems': '', 'genres': '' };
		updateGenres();
	}
}).controller('acp_links', function ($scope, $http, $sce) {
	function getLinks() {
		$scope.links = [];
		$http.post(API_HOST + '/links/list/', { 'page': $scope.pagination.current }).success(function (data) {
			$(data.links).each(function (key, value) {
				value.level = { 'id': value.level.toLowerCase(), 'value': value.level }
				$scope.links.push(value);
			})

			$scope.pagination.numItems = Math.ceil(data.totalCount / 20);
			$scope.pagination.pages = [];
			for (count = $scope.pagination.numItems - 2 > 0?$scope.pagination.numItems - 2:1; count <= $scope.pagination.numItems + 2 && count <= $scope.pagination.numItems; count++) {
				$scope.pagination.pages.push(count);
			}
		});
	}
	$scope.pagination = {};
	if ($.urlParam('page')) 
		$scope.pagination.current = parseInt($.urlParam('page'));
	else 
		$scope.pagination.current = 1;
	$scope.showPagination = true;

	$scope.links = {};
	$scope.newLink = {};
	getLinks();

	$scope.changePage = function (page) {
		page = parseInt(page);
		if (page < 0 && page > $scope.pagination.numItems) 
			page = 1;
		$scope.pagination.current = page;
		getLinks(page);
	}
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
			scope.levels = [
				{ 'id': 'link', 'value': 'Link'},
				{ 'id': 'affiliate', 'value': 'Affiliate'},
				{ 'id': 'partner', 'value': 'Partner'},
			];
			scope.categories = [ 'Blog', 'Podcast', 'Videocast', 'Liveplay', 'Devs', 'Accessories' ];
			if (typeof attrs.new != 'undefined') {
				scope.new = true;
				scope.editing = true;
				scope.data = {
					'title': '',
					'url': '',
					'level': { id: 'link', value: 'Link' },
					'networks': [],
					'categories': []
				};
			} else 
				scope.new = false;
			scope.cb_value = {};

			scope.toggleEditing = function () {
				scope.showEdit = !scope.showEdit;
				scope.editing = !scope.editing;
			}
			scope.saveLink = function () {
				data = copyObject(scope.data);
				delete data.image;
				data.level = data.level.value;
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
	$scope.pagination = {};
	if ($.urlParam('page')) 
		$scope.pagination.current = parseInt($.urlParam('page'));
	else 
		$scope.pagination.current = 1;
	$scope.showPagination = true;
	function loadMusic() {
		$http.post(API_HOST + '/music/get/', { 'page': $scope.pagination.current }).success(function (data) {
			if (data.success) {
				$scope.music = data.music;

				$scope.pagination.numItems = Math.ceil(data.count / 10);
				$scope.pagination.pages = new Array();
				for (count = $scope.pagination.numItems - 2 > 0?$scope.pagination.numItems - 2:1; count <= $scope.pagination.numItems + 2 && count <= $scope.pagination.numItems; count++) {
					$scope.pagination.pages.push(count);
				}
			}
		});
	}
	loadMusic();
	$scope.changePage = function (page) {
		page = parseInt(page);
		if (page < 0 && page > $scope.pagination.numItems) 
			page = 1;
		$scope.pagination.current = page;
		loadMusic();
	}

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