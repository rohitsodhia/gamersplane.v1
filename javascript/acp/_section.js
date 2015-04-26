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

/*	if ($('#page_acp_links').length) {
		$('li > form').ajaxForm({
			beforeSubmit: function (arr, $form) {
				var error = false;
				$form.find('input[type="text"]').each(function () {
					if ($(this).val().length == 0) error = true;
				});
				if (error) return false;
			},
			success: function (data, status, xhr, $form) {
				if (data['status'] == 'updated' || data['status'] == 'imageDeleted') {
					mongoID = $form.find('input[name="mongoID"]').val();
					$form.find('.action_edit').show();
					$form.find('.confirmEdit').hide();
					$form.find('.level .display').text($form.find('option[value="' + $form.find('select').val() + '"]').text());
					if (data['status'] == 'imageDeleted') {
						$form.find('img').attr('src', '/images/spacer.gif');
						$form.find('.preview button').hide();
					} else if (data['image']) {
						$form.find('img').attr('src', '/images/links/' + mongoID + '.' + data['image'] + '?u=' + new Date().getTime());
					}

					$form.removeClass('editing').find('input').prop('disabled', true);
				} else if (data['status'] == 'deleted') {
					$form.parent().remove();
				}
			}
		});

		var origVals = {};
		$mainColumn.on('click', 'button.action_edit', function (e) {
			e.preventDefault();

			$form = $(this).closest('form');
			mongoID = $form.find('input[name="mongoID"]').val();
			origVals[mongoID] = {};
			$form.find('input[type="text"]').each(function () {
				origVals[mongoID][$(this).attr('name')] = $(this).val();
			});
			origVals[mongoID]['level'] = $form.find('select').val();

			$(this).siblings('.confirmEdit').show();
			$(this).hide();

			$(this).closest('form').addClass('editing').find('input').prop('disabled', false);
		}).on('click', 'button.action_edit_cancel', function (e) {
			e.preventDefault();

			$form = $(this).closest('form');
			mongoID = $form.find('input[name="mongoID"]').val();
			$form.find('input[type="text"]').each(function () {
				$(this).val(origVals[mongoID][$(this).attr('name')]);
			});
			$form.find('select').val(origVals[mongoID]['level']).change();

			$(this).parent().siblings('button').show();
			$(this).parent().hide();

			$form.removeClass('editing').find('input').prop('disabled', true);
		}).on('click', 'button.action_delete', function (e) {
			e.preventDefault();

			$(this).siblings('.confirmDelete').show();
			$(this).hide();
		}).on('click', 'button.action_delete_cancel', function (e) {
			e.preventDefault();

			$(this).parent().siblings('button').show();
			$(this).parent().hide();
		});
	}*/
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
		if ($scope.newGenre.length == 0) 
			return;
		$scope.edit.genres.push($scope.newGenre);
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
		$scope.showEdit($scope.systemSearch);
		$scope.combobox.search = { 'systems': '', 'genres': '' };
		updateGenres();
	}
}).controller('acp_links', function ($scope, $http, $sce) {
	function getLinks() {
		$http.post(API_HOST + '/links/list/', {}).success(function (data) {
			$scope.links = [];
			$(data.links).each(function (key, value) {
				networks = value.networks;
				value.networks = {};
				for (nKey in networks) 
					value.networks[networks[nKey]] = true
				categories = value.categories;
				value.categories = {};
				for (nKey in categories) 
					value.categories[categories[nKey]] = true
				$scope.links.push(value);
			})
		});
	}

	$scope.links = {};
	$scope.newLink = {};
	getLinks();
}).directive('linksEdit', ['$filter', '$http', '$upload', function ($filter, $http, $upload) {
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
			if (typeof attrs.new != 'undefined') {
				scope.new = true;
				scope.editing = true;
				scope.data.level = 'Link';
				scope.data.networks = { 'rpga': false };
				scope.data.categories = { 'blog': false, 'podcast': false };
			} else {
				scope.new = false;
			}
			scope.cb_value = '';

			scope.toggleEditing = function () {
				scope.showEdit = !scope.showEdit;
				scope.editing = !scope.editing;
			}
			scope.saveLink = function () {
				console.log(scope.data);
				data = JSON.parse(JSON.stringify(scope.data));
				delete data.image;
				$upload.upload({
					'url': API_HOST + '/links/save/',
					'file': scope.data.newImage,
					'fields': data
				}).success(function (data) {
					if (data.image) 
						scope.data.image = data.image;
					scope.toggleEditing();
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
}]);