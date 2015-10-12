$.cssHooks.backgroundColor = {
    get: function(elem) {
        if (elem.currentStyle)
            var bg = elem.currentStyle["backgroundColor"];
        else if (window.getComputedStyle)
            var bg = document.defaultView.getComputedStyle(elem,
                null).getPropertyValue("background-color");
        if (bg.search("rgb") == -1)
            return bg;
        else {
            bg = bg.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            function hex(x) {
                return ("0" + parseInt(x).toString(16)).slice(-2);
            }
            hexString = /*"#" + */hex(bg[1]) + hex(bg[2]) + hex(bg[3]);
            return hexString.toUpperCase();
        }
    }
}

$(function() {
	$('select').prettySelect();
	$('input[type="checkbox"]').prettyCheckbox();
	$('input[type="radio"]').prettyRadio();

	$('.loginLink').colorbox();

	$('.placeholder').placeholder();

	if ($('body').hasClass('modal')) {
		$('a').not('.inFrame').attr('target', '_parent');
		parent.$.colorbox.resize({ 'innerWidth': $('body').data('modalWidth') } );
		parent.$.colorbox.resize({ 'innerHeight': $('body').height() } );

		$('form.ajaxForm_refreshParent').append('<input type="hidden" name="modal" value="1">').ajaxForm({
			dataType: 'json',
			success: function (data) {
				if (data.success == true) {
					parent.document.location.reload();
				}
			}
		});
		$('form.ajaxForm_closeCB').append('<input type="hidden" name="modal" value="1">').ajaxForm({
			dataType: 'json',
			success: function (data) {
				if (data.success == true) {
					parent.$.colorbox.close();
				}
			}
		});
	}

	$('.headerbar, .fancyButton').each(skewElement);
	hbMargin = parseFloat($('.headerbar').data('skewedOut')) * 2;
	$('.hbMargined:not(textarea)').css({ 'margin-left': Math.ceil(hbMargin) + 'px', 'margin-right': Math.ceil(hbMargin) + 'px' });
	$('.hbTopper').css({ 'marginLeft': Math.round(hbMargin) + 'px' });
	$('textarea.hbMargined').each(function () {
		tWidth = $(this).parent().width();
		$(this).css({ 'margin-left': Math.ceil(hbMargin) + 'px', 'margin-right': Math.ceil(hbMargin) + 'px', 'width': Math.ceil(tWidth - 2 * hbMargin) + 'px' });
	});

	hbdMargin = parseFloat($('.hbDark').data('skewedOut')) * 2;
	$('.hbdMargined:not(textarea)').css({ 'margin-left': Math.ceil(hbdMargin) + 'px', 'margin-right': Math.ceil(hbdMargin) + 'px' });
	$('.hbdTopper').css({ 'marginLeft': Math.round(hbdMargin) + 'px' });
	$('textarea.hbdMargined').each(function () {
		tWidth = $(this).parent().width();
		$(this).css({ 'margin-left': Math.ceil(hbdMargin) + 'px', 'margin-right': Math.ceil(hbdMargin) + 'px', 'width': Math.ceil(tWidth - 2 * hbdMargin) + 'px' });
	});

	$('.trapezoid').each(trapezoidify);

	$('#mainMenu li').mouseenter(function () {
		$(this).children('ul').stop(true, true).slideDown();
	}).mouseleave(function () {
		$(this).children('ul').stop(true, true).slideUp();
	}).find('ul').each(function () {
		$(this).css('minWidth', $(this).parent().width());
	});

	if ($('#fixedMenu').size()) {
		var $fixedMenu = $('#fixedMenu_window');
		$('html').click(function () {
			$fixedMenu.find('.submenu, .subwindow').slideUp(250);
		});
		
		var fm_currentlyOpen = '';
		$fixedMenu.click(function (e) { e.stopPropagation(); })
		$fixedMenu.find('li > a').filter(function () {
			return $(this).siblings('.submenu, .subwindow').length;
		}).click(function (e) {
			e.stopPropagation();

			currentID = $(this).parent().attr('id');
			$parentMenu = $(this).parent().parent();
			$subwindow = $(this).siblings('.submenu, .subwindow');

			$parentMenu.find('.fm_smOpen').not($subwindow).slideUp(250).removeClass('fm_smOpen');
			$subwindow.slideToggle(250).toggleClass('fm_smOpen');
			
			e.preventDefault();
		});
		
		
		$('#fm_roll').click(function (e) {
			e.stopPropagation();
			var dice = $('#fm_customDiceRoll input').val();
			if (dice != '') fm_rollDice(dice);
			
			e.preventDefault();
		});
		
		$('#fm_diceRoller input').keypress(function (e) {
			if (e.which == 13) {
				var dice = $(this).val();
				if (dice != '') fm_rollDice(dice);
				
				e.preventDefault();
			}
		}).click(function (e) { e.stopPropagation(); });
		
		$('#fm_diceRoller .diceBtn').click(function (e) {
			e.stopPropagation();
			var dice = '1' + $(this).attr('name');
			if (dice != '1') fm_rollDice(dice);

			e.preventDefault();
		});
	}

	$('.cbf_basic').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			$('.cbf_basic .required').each(function () {
				if ($(this).val().length == 0) return false;
			});

			return true;
		},
		success: function (data) {
			if (data == '1') {
				parent.document.location.reload();
			}
		}
	});

	$('.convertTZ').each(function () {
		var parseFormat = 'MMMM D, YYYY h:mm a', displayFormat = 'MMMM D, YYYY h:mm a';
		if ($(this).data('parseFormat')) parseFormat = $(this).data('parseFormat');
		if ($(this).data('displayFormat')) displayFormat = $(this).data('displayFormat');
		$(this).text(convertTZ($(this).text(), parseFormat, displayFormat));
	});


	/* Individual Pages */
	if (!$('body').hasClass('modal')) var curPage = $('#content > div > div').attr('id').substring(5);
	else var curPage = $('body > div').attr('id').substring(5);
});

var app = angular.module('gamersplane', ['controllers', 'ngCookies', 'ngSanitize', 'ngAnimate', 'ngFileUpload', 'angularMoment']);
app.config(function ($httpProvider) {
	$httpProvider.defaults.withCredentials = true;
}).factory('currentUser', function ($http) {
	return $http.post(API_HOST + '/users/getCurrentUser/').success(function (data) {
		if (data.loggedOut) 
			return null;
		else 
			return data;
	});
}).service('Users', ['$http', 'Upload', function ($http, Upload) {
	this.get = function (userID) {
		params = {};
		if (userID && parseInt(userID) > 0) 
			params.userID = userID;
		return $http.post(API_HOST + '/users/get/', params).then(function (data) {
			data = data.data;
			if (data.success) 
				return data.details;
			else 
				return false;
		});
	};
	this.save = function (params, newAvatar) {
		return Upload.upload({
			'url': API_HOST + '/users/save/',
			'file': newAvatar,
			'data': params
//			'fields': params,
//			'sendFieldsAs': 'form'
		});
	};
	this.inactive = function (lastActivity, returnImg) {
		if (isUndefined(returnImg) || typeof returnImg != 'boolean') 
			returnImg = true;
		lastActivity = moment(lastActivity);
		now = moment();
		diff = now - lastActivity;
		diff = Math.floor(diff / (1000 * 60 * 60 * 24));
		if (diff < 14) 
			return false;
		diffStr = 'Inactive for';
		if (diff <= 30) 
			diffStr += ' ' + (diff - 1) + ' day' + (diff > 1?'s':'');
		else {
			diff = Math.floor(diff / 30);
			if (diff < 12) 
				diffStr += ' ' + diff + ' month' + (diff > 1?'s':'');
			else 
				diffStr += 'ever!';
		}
		return returnImg?"<img src=\"/images/sleeping.png\" title=\"" + diffStr + "\" alt=\"" + diffStr + "\">":diffStr;
	};
}]).service('systems', ['$http', '$q', function ($http, $q) {
	this.get = function (params) {
		if (typeof params != 'object' || Array.isArray(params)) 
			params = {};
		var deferred = $q.defer();
		$http.post(API_HOST + '/systems/get/', params).success(function (data) { deferred.resolve(data) });
		return deferred.promise;
	};
	this.getGenres = function () {
		var deferred = $q.defer();
		$http.post(API_HOST + '/systems/getGenres/').success(function (data) { deferred.resolve(data) });
		return deferred.promise;
	};
}]).service('Links', ['$http', function ($http, $q) {
	this.categories = [ 'Blog', 'Podcast', 'Videocast', 'Liveplay', 'Devs', 'Accessories' ];
	this.get = function (params) {
		if (typeof params != 'object' || Array.isArray(params)) 
			params = {};
		return $http.post(API_HOST + '/links/get/', params).then(function (data) { return data; });
	};
}]).service('faqs', ['$http', '$q', function ($http, $q) {
	this.categories = { 'Getting Started': 'getting-started', 'Characters': 'characters', 'Games': 'games', 'Tools': 'tools' };
	this.get = function () {
		var deferred = $q.defer();
		$http.post(API_HOST + '/faqs/get/').success(function (data) { deferred.resolve(data); });
		return deferred.promise;
	};
	this.changeOrder = function (id, direction) {
		if (direction != 'up' && direction != 'down') 
			return false;
		var deferred = $q.defer();
		$http.post(API_HOST + '/faqs/changeOrder/', { 'id': id, 'direction': direction }).success(function (data) { deferred.resolve(data); });
		return deferred.promise;
	};
	this.update = function (faq) {
		var deferred = $q.defer();
		$http.post(API_HOST + '/faqs/save/', { 'id': faq._id, 'question': faq.question, 'answer': faq.answer.raw }).success(function (data) { deferred.resolve(data); });
		return deferred.promise;
	};
	this.create = function (faq) {
		var deferred = $q.defer();
		$http.post(API_HOST + '/faqs/save/', { 'category': faq.category.value, 'question': faq.question, 'answer': faq.answer }).success(function (data) { deferred.resolve(data); });
		return deferred.promise;
	};
	this.delete = function (id) {
		var deferred = $q.defer();
		$http.post(API_HOST + '/faqs/delete/', { 'id': id }).success(function (data) { deferred.resolve(data); });
		return deferred.promise;
	}
}]).service('ACSearch', ['$http', function ($http) {
	this.cil = function (type, search, system, systemOnly) {
		if (isUndefined(systemOnly) || typeof systemOnly != 'boolean') 
			systemOnly = false;
		return $http.post(API_HOST + '/characters/cilSearch/', { 'type': type, 'search': search, 'system': system, 'systemOnly': systemOnly }).then(function (data) {
			data = data.data;
			if (data.items.length) 
				return data.items;
			else 
				return [];
		});
	};
	this.users = function (search, notSelf) {
		if (isUndefined(notSelf) || typeof notSelf != 'boolean') 
			notSelf = false;
		return $http.get(API_HOST + '/users/search/', { 'params': { 'search': search, 'notSelf': notSelf } }).then(function (data) {
			if (data.data.users) 
				return data.data.users;
			else 
				return [];
		});
	}
}]).service('initializeVars', [function () {
	this.setup = function (scope) {
		return scope;
	}
}]).factory('characters', ['$http', '$q', function ($http, $q) {
	return {
		'getMy': function (library) {
			var deferred = $q.defer();
			$http.post(API_HOST + '/characters/my/', { 'library': library }).success(function (data) { deferred.resolve(data); });
			return deferred.promise;
		},
		'new': function (data) {
			var deferred = $q.defer();
			$http.post(API_HOST + '/characters/new/', {
				'label': data.label,
				'system': data.system,
				'charType': data.charType
			}).success(function (data) { deferred.resolve(data) });
			return deferred.promise;
		},
		'saveBasic': function (data) {
			var deferred = $q.defer();
			$http.post(API_HOST + '/characters/saveBasic/', {
				'characterID': data.characterID,
				'label': data.label,
				'charType': data.charType
			}).success(function (data) { deferred.resolve(data); });
			return deferred.promise;
		},
		'toggleLibrary': function (characterID) {
			var deferred = $q.defer();
			$http.post(API_HOST + '/characters/toggleLibrary/', { 'characterID': characterID }).success(function (data) { deferred.resolve(data); });
			return deferred.promise;
		},
		'delete': function (data) {
			var deferred = $q.defer();
			$http.post(API_HOST + '/characters/delete/', { 'characterID': data.characterID }).success(function (data) { deferred.resolve(data); });
			return deferred.promise;
		},
		'toggleFavorite': function (characterID) {
			var deferred = $q.defer();
			$http.post(API_HOST + '/characters/toggleFavorite/', { 'characterID': characterID }).success(function (data) { deferred.resolve(data); });
			return deferred.promise;
		}
	}
}]).service('Range', function () {
	this.get = function (from, to, incBy) {
		incBy = parseInt(incBy);
		if (Math.round(incBy) != incBy || incBy == 0) 
			incBy = 1;
		range = [];
		for (count = from; count <= to; count += incBy) 
			range.push(count);
		return range;
	}
}).directive('skewElement', function () {
	return {
		restrict: 'A',
		link: function (scope, element, attrs) {
			$element = $(element);
			if ($element.children('div.skewedDiv').length) 
				return;
			var skewDeg = 0;
			if (attrs.skewElement != '') 
				skewDeg = parseInt(attrs.skewElement);
			if (skewDeg == 0)
				skewDeg = -30;
			$skewDiv = $element.wrapInner('<div class="skewedDiv"></div>').children('div');
			skewedOut = Math.tan(Math.abs(skewDeg) * Math.PI / 180) * $element.outerHeight() / 2;
			scope.skewedOut = skewedOut;
			$element.css({
				'-webkit-transform' : 'skew(' + skewDeg + 'deg)',
				'-moz-transform'    : 'skew(' + skewDeg + 'deg)',
				'-ms-transform'     : 'skew(' + skewDeg + 'deg)',
				'-o-transform'      : 'skew(' + skewDeg + 'deg)',
				'transform'         : 'skew(' + skewDeg + 'deg)',
			}).data('skewedOut', skewedOut);
			if (parseInt($element.css('margin-left').slice(0, -2)) < Math.ceil(skewedOut)) 
				$element.css('margin-left', Math.ceil(skewedOut) + 'px');
			if (parseInt($element.css('margin-right').slice(0, -2)) < Math.ceil(skewedOut)) 
				$element.css('margin-right', Math.ceil(skewedOut) + 'px');
			$skewDiv.css({
				'-webkit-transform' : 'skew(' + (skewDeg * -1) + 'deg)',
				'-moz-transform'    : 'skew(' + (skewDeg * -1) + 'deg)',
				'-ms-transform'     : 'skew(' + (skewDeg * -1) + 'deg)',
				'-o-transform'      : 'skew(' + (skewDeg * -1) + 'deg)',
				'transform'         : 'skew(' + (skewDeg * -1) + 'deg)',
				'margin-left'       : Math.ceil(skewedOut) + 'px',
				'margin-right'      : Math.ceil(skewedOut) + 'px'
			});

			if ($element.hasClass('headerbar')) {
				hbdMargin = skewedOut * 2;
				$element.siblings('.hbMargined:not(textarea)').add($element.find('.hbMargined:not(textarea)')).css({ 'margin-left': Math.ceil(hbdMargin) + 'px', 'margin-right': Math.ceil(hbdMargin) + 'px' });
				$element.siblings('.hbdTopper').add($element.find('.hbdTopper')).css({ 'marginLeft': Math.round(hbdMargin) + 'px' });
				$element.siblings('textarea.hbdMargined').add($element.siblings('textarea.hbdMargined')).each(function () {
					tWidth = $(this).parent().width();
					$(this).css({ 'margin-left': Math.ceil(hbdMargin) + 'px', 'margin-right': Math.ceil(hbdMargin) + 'px', 'width': Math.ceil(tWidth - 2 * hbdMargin) + 'px' });
				});
			}


		}
	}
}).directive('hbMargined', ['$timeout', function ($timeout) {
	return {
		restrict: 'A',
		link: function (scope, element, attrs) {
			$timeout(function () {
				$element = $(element);
				if (attrs.hbMargined == 'dark') 
					$headerbar = $('.headerbar.hbDark');
				else 
					$headerbar = $element.siblings('.headerbar');
				skewedOut = parseFloat($headerbar.data('skewedOut')) * 2;
				$element.css('margin-left', skewedOut);
				if (!$element.hasClass('hbTopper')) 
					$element.css('margin-right', skewedOut);
			});
		}
	};
}]).directive('hbTopper', function () {
	return {
		restrict: 'A',
		link: function (scope, element, attrs) {
			$element = $(element);
			$headerbar = $(element).siblings('.headerbar');
			skewedOut = parseFloat($headerbar.data('skewedOut')) * 2;
			$element.css({ 'margin-left': skewedOut });
		}
	};
}).directive('colorbox', function () {
	return {
		restrict: 'A',
		link: function (scope, element, attrs) {
			element = element[0];
			if (element.localName != 'a') 
				return;
			$(element).click(function (e) {
				e.preventDefault();
				$.colorbox({ href: attrs.href + '?modal=1' });
			});
		}
	}
}).directive('paginate', function () {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/paginate.php',
		scope: {
			'numItems': '=',
			'itemsPerPage': '=',
			'current': '=',
			'changeFunc': '='
		},
		link: function (scope, element, attrs) {
			if (!isUndefined(attrs.class) && attrs.class.length) 
				element.attr('class', (element.attr('class').length?element.attr('.class') + ' ':'') + attrs.class);
			scope.numPages = 0;
			scope.$watch(function () { return scope.numItems; }, function (val) {
				scope.numPages = Math.ceil(scope.numItems / scope.itemsPerPage);
				if (scope.current > scope.numPages) 
					scope.current = 1;
				scope.pages = [];
				for (count = scope.current > 2?scope.current - 2:1; count <= scope.current + 2 && count <= scope.numPages; count++) 
					scope.pages.push(count);
			});

			scope.changePage = function (page) {
				page = parseInt(page);
				if (page < 0 && page > scope.numItems) 
					page = 1;
				scope.current = page;
				scope.pages = [];
				for (count = scope.current > 2?scope.current - 2:1; count <= scope.current + 2 && count <= scope.numPages; count++) 
					scope.pages.push(count);
				if (typeof scope.changeFunc == 'function') 
					scope.changeFunc();
			}
		}
	}
}).directive('combobox', ['$filter', '$timeout', function ($filter, $timeout) {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/combobox.php',
		scope: {
			'data': '=',
			'value': '=',
			'search': '=',
			'autocomplete': '='
		},
		link: function (scope, element, attrs) {
			scope.select = !isUndefined(attrs.select)?true:false;
			scope.bypassFilter = true;
			$timeout(function () {
				scope.search = typeof scope.search == 'string'?scope.search:'';
				scope.value = typeof scope.value == 'object' && !isUndefined(scope.value.value) && !isUndefined(scope.value.display)?scope.value:{ 'value': null, 'display': '' };
			});
			if (!isUndefined(attrs.placeholder)) 
				element.find('input').attr('placeholder', attrs.placeholder);
			scope.usingAutocomplete = false;
			if (!isUndefined(attrs.autocomplete)) {
				scope.usingAutocomplete = true;
				var skillSearchTimeout = null;
				scope.$watch(function () { return scope.search; }, function (newVal, oldVal) {
					if (newVal == oldVal) 
						return;
					$timeout.cancel(skillSearchTimeout);
					if (scope.search.length >= 3) 
						skillSearchTimeout = $timeout(function () {
							var data = scope.autocomplete(scope.search)
							if (isUndefined(scope.data)) 
								scope.data = [];
							if (data && typeof data.then == 'function') 
								data.then(function (data) {
									scope.data = copyObject(data);
								});
							else 
								scope.data = copyObject(data);
						}, 500);
				});
			}
			scope.options = [];
			scope.showDropdown = false;
			scope.hasFocus = false;
			scope.curSelected = -1;
			var $combobox = element.children('.combobox'),
				$input = element.children('input');
			if (!isUndefined(attrs.class)) {
				$combobox.addClass(attrs.class);
				element.attr('class', '');
			}

			function setValue() {
				for (key in scope.options) 
					if (scope.search.toLowerCase() == scope.options[key].display.toLowerCase()) {
						scope.value = scope.options[key];
						scope.search = scope.value.display;
					}
			}
			scope.filterData = function () {
				return $filter('filter')(scope.options, (!scope.bypassFilter || '') && { 'display': scope.search });
			}
			scope.$watch(function () { return scope.data; }, function (newVal, oldVal) {
				if (isUndefined(scope.data)) 
					return;
				scope.options = copyObject(scope.data);
				if (isUndefined(scope.options) || (scope.options instanceof Array && scope.options.length == 0)) {
					scope.options = [];
					return;
				}
				for (key in scope.options) {
					if (typeof scope.options[key] == 'string' && scope.options[key].length > 0) 
						scope.options[key] = { 'value': scope.options[key], 'display': scope.options[key] };
					else if (!isUndefined(scope.options[key].display) && scope.options[key].display.length && (isUndefined(scope.options[key].value) || scope.options[key].value.length == 0))
						scope.options[key].value = scope.options[key].display;
					else if (isUndefined(scope.options[key].display) || scope.options[key].display.length == 0) 
						scope.options.splice(key, 1);

					scope.options[key] = {
						'value': decodeHTML(scope.options[key].value),
						'display': decodeHTML(scope.options[key].display),
						'class': !isUndefined(scope.options[key].class)?scope.options[key].class:[]
					}
				}
				filterResults = $filter('filter')(scope.options, { 'value': scope.value.value }, true);
				if (filterResults.length == 1 && !scope.hasFocus) 
					scope.search = scope.value.display;
				else 
					scope.value = { 'value': null, 'display': '' };
				if (scope.select && (isUndefined(scope.value) || isUndefined(scope.value.value) || isUndefined(scope.value.display) || (scope.value.value == null && scope.value.display == '')) && !scope.hasFocus) {
					scope.value = copyObject(scope.options[0]);
					scope.search = scope.value.display;
				}
			}, true);

			scope.inputFocused = function () {
				scope.hasFocus = true;
				scope.showDropdown = true;
			};
			scope.inputBlurred = function () {
				scope.hasFocus = false;
				scope.showDropdown = false;
			};

			scope.toggleDropdown = function ($event) {
				$event.stopPropagation();
				if (scope.filterData().length) 
					scope.showDropdown = !scope.showDropdown;
			};
			scope.$watch(function () { return scope.showDropdown; }, function (val, oldVal) {
				if (val == oldVal) 
					return;
				if (scope.showDropdown && scope.filterData().length) 
					scope.curSelected = -1;
				else {
					element.find('.selected').removeClass('selected');
					scope.bypassFilter = true;
				}
			});
			$('html').click(function () {
				scope.showDropdown = false;
				scope.$apply();
			});

			scope.$watch(function () { return scope.hasFocus; }, function (newVal, oldVal) {
				if (!newVal) {
					if (!isUndefined(scope.search) && scope.search.length != '') {
						filterResults = $filter('filter')(scope.options, { 'display': scope.search });
						if (filterResults.length == 1 && filterResults[0].display.toLowerCase() == scope.search.toLowerCase()) {
							scope.search = filterResults[0].display;
							scope.value = filterResults[0];
						} else if (scope.select) {
							noResults = true;
							for (key in filterResults) {
								if (filterResults[key].display.toLowerCase() == scope.search.toLowerCase()) {
									noResults = false;
									scope.search = filterResults[key].display;
									scope.value = filterResults[key];
									break;
								}
							}
							if (noResults) {
								if (scope.select) {
									scope.value = copyObject(scope.options[0]);
									scope.search = scope.value.display;
								} else {
									scope.search = '';
									scope.value = { 'value': null, 'display': '' };
								}
							}
						} else 
							scope.value = { 'value': null, 'display': scope.search }
					}
				}
			});

			scope.navigateResults = function ($event) {
				if ($event.keyCode == 13) {
					if (scope.showDropdown) 
						$event.preventDefault();
					scope.value = { 'value': null, 'display': '' };
					if (scope.curSelected == -1) {
						filterResults = $filter('filter')(scope.options, { 'display': scope.search }, true);
						if (filterResults.length == 1) 
							scope.setBox(filterResults);
					} else {
						filterResults = $filter('filter')(scope.options, { 'display': scope.search });
						scope.setBox(filterResults[scope.curSelected]);
					}
				} else if ($event.keyCode == 38 || $event.keyCode == 40) {
					$event.preventDefault();
					if (!scope.showDropdown) 
						scope.showDropdown = true;
					$resultsWrapper = element.find('.results');
					$results = $($resultsWrapper).children();
					resultsHeight = $resultsWrapper.height();

					if ($event.keyCode == 40) 
						scope.curSelected += 1;
					else if ($event.keyCode == 38) 
						scope.curSelected -= 1;

					if (scope.curSelected < 0) 
						scope.curSelected = $results.length - 1;
					else if (scope.curSelected >= $results.length) 
						scope.curSelected = 0;

					if ($results[scope.curSelected].offsetTop + $($results[scope.curSelected]).outerHeight() > $resultsWrapper.scrollTop() + resultsHeight) 
						$resultsWrapper.scrollTop($results[scope.curSelected].offsetTop + $($results[scope.curSelected]).outerHeight() - resultsHeight);
					else if ($results[scope.curSelected].offsetTop < $resultsWrapper.scrollTop()) 
						$resultsWrapper.scrollTop($results[scope.curSelected].offsetTop);
				} else if ($event.keyCode == 27) 
					scope.showDropdown = false;
				else 
					scope.bypassFilter = false;
			};

			scope.setBox = function (set) {
				scope.value = copyObject(set);
				scope.search = set.display;
				scope.hasFocus = false;
				scope.bypassFilter = true;
				scope.showDropdown = false;
			};
			scope.setSelected = function (index) {
				scope.curSelected = index;
			}
		}
	}
}]).directive('prettyCheckbox', [function () {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/prettyCheckbox.php',
		scope: {
			'checkbox': '=checkbox',
			'cbValue': '=value'
		},
		link: function (scope, element, attrs) {
			scope.cbm = false;
			if ((scope.checkbox instanceof Array && scope.checkbox.indexOf(scope.cbValue) != -1) || !(scope.checkbox instanceof Array) && scope.checkbox) 
				scope.cbm = true;
			scope.eleid = typeof attrs['eleid'] == 'string' && attrs['eleid']?attrs['eleid']:'';
//			element.attr('id', '');
			var label = null, wrapperLabel = false;
			label = $(element).closest('label');
			if (!label.length && typeof attrs['eleid'] == 'string' && attrs['eleid']) {
//				element.attr('id', attrs['eleid']);
				label = $('label[for=' + attrs['eleid'] + ']');
			} else if (label.length) 
				wrapperLabel = true;
			if (label.length) 
				label.click(function (e) {
					if (wrapperLabel) 
						scope.toggleCB();
					else 
						e.preventDefault();
					scope.$apply();
				});

			scope.toggleCB = function ($event) {
				if (!isUndefined($event) && (wrapperLabel || $event.currentTarget == 'html')) 
					return;
				else if ($event && label.length == 0) 
					$event.stopPropagation();
				scope.cbm = !scope.cbm;
			};

			scope.$watch(function () { return scope.cbm; }, function (val) {
				val = val?true:false;
				if (scope.checkbox instanceof Array) {
					if (val && scope.checkbox.indexOf(scope.cbValue) == -1) 
						scope.checkbox.push(scope.cbValue);
					else if (!val) {
						key = scope.checkbox.indexOf(scope.cbValue);
						if (key > -1) 
							scope.checkbox.splice(key, 1);
					}
				} else 
					scope.checkbox = val;
			});
		}
	}
}]).directive('prettyRadio', [function () {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/prettyRadio.php',
		scope: {
			'radio': '=radio',
			'rValue': '=rValue'
		},
		link: function (scope, element, attrs) {
			scope.inputID = typeof attrs['eleid'] == 'string'?attrs['eleid']:'';

			var label = null, wrapperLabel = false;
			label = $(element).closest('label');
			if (!label.length && typeof attrs['eleid'] == 'string' && attrs['eleid']) {
//				element.attr('id', attrs['eleid']);
				label = $('label[for=' + attrs['eleid'] + ']');
			} else if (label.length) 
				wrapperLabel = true;
			if (label.length) 
				label.click(function (e) {
					if (wrapperLabel) 
						scope.setRadio();
					else 
						e.preventDefault();
					scope.$apply();
				});

			scope.setRadio = function () {
				scope.radio = scope.rValue;
			}
		}
	}
}]).directive('equalizeColumns', ['$timeout', function ($timeout) {
	return {
		restrict: 'A',
		link: function (scope, element, attrs) {
			$timeout(function () {
				var tallest = 0;
				element.children().each(function () {
					if ($(this).height() > tallest) 
						tallest = $(this).height();
				}).height(tallest);
			}, 1);
		}
	}
}]).directive('ngPlaceholder', [function () {
	return {
		restrict: 'A',
		scope: {},
		link: function (scope, element, attrs) {
			var placeholder = attrs['ngPlaceholder'];
			if (typeof placeholder == 'string' && placeholder.length) {
				element.blur(function () {
					var $input = $(this);
					if ($input.val() == '' || $input.val() == placeholder) 
						$input.addClass('default');
					$input.val(function () { return placeholder == ''?placeholder:$input.val(); }).focus(function () {
						if ($input.val() == placeholder || $input.val() == '') 
							$input.val('').removeClass('default');
					}).blur(function () {
						if ($input.val() == '') 
							$input.val(placeholder).addClass('default');
					}).change(function () {
						if ($input.val() != placeholder) 
							$input.removeClass('default');
						else if ($input.val() == placeholder) 
							$input.addClass('default');
					});
				}).blur();
			}
		}
	}
}]).directive('loadingSpinner', ['$timeout', function ($timeout) {
	return {
		restrict: 'E',
		template: '<div class="loadingSpinner"><img ng-src="/images/loading_back{{wb}}.png" class="background"><img src="/images/loading_fore{{wb}}.png" class="foreground"></div>',
		scope: {
			'pause': '='
		},
		link: function (scope, element, attrs) {
			if (!isUndefined(attrs.size)) 
				element.children().addClass(attrs.size);
			scope.wb = !isUndefined(attrs.wb)?'_wb':'';
			if (!isUndefined(attrs.overlay)) {
				parentHeight = element.parent().height();
				if (parentHeight > 200) 
					element.children().css('top', '90px');
			}
			var foreground = element.find('.foreground'),
				running = !scope.pause,
				fadeTime = 1500,
				fadePauseB = 150,
				fadePauseT = 450;
			scope.fadeIn = function () {
				$timeout(function () {
					if (!scope.pause) {
						running = true;
						foreground.fadeIn(fadeTime, scope.fadeOut);
					} else 
						running = false;
				}, fadePauseB);
			}
			scope.fadeOut = function () {
				$timeout(function () {
					if (!scope.pause) {
						running = true;
						foreground.fadeOut(fadeTime, scope.fadeIn);
					} else 
						running = false;
				}, fadePauseT);
			}
			if (!scope.pause)
				scope.fadeIn();
			scope.$watch(function () { return scope.pause; }, function () {
				if (!scope.pause && !running) 
					scope.fadeIn();
			});
		}
	}
}]).filter('trustHTML', ['$sce', function($sce){
	return function(text) {
		if (typeof text != 'string') 
			text = '';
		return $sce.trustAsHtml(text);
	}
}]).filter('paginateItems', [function () {
	return function (input, limit, skip) {
		output = [];
		count = -1;
		for (key in input) {
			count++;
			if (count < skip) 
				continue;
			else if (count >= limit + skip) 
				break;
			output.push(input[key]);
		}
		return output;
	}
}]).filter('intersect', [function () {
	return function (input, field, compareTo) {
		if (compareTo.length == 0) 
			return input;
		output = [];
		for (key in input) {
			for (iKey in compareTo) {
				if (input[key][field].indexOf(compareTo[iKey]) >= 0) {
					output.push(input[key]);
					break;
				}
			}
		}
		return output;
	}
}]).filter('convertTZ', [function () {
	return function (dtString, parseString, displayString) {
		parseString = !isUndefined(parseString)?parseString:'MMM D, YYYY h:mm a';
		displayString = !isUndefined(displayString)?displayString:'MMM D, YYYY h:mm a';

		utcDT = moment.utc(dtString, parseString);
		return utcDT.local().format(displayString);
	}
}]).filter('ceil', [function () {
	return function (input) {
		return Math.ceil(input);
	}
}]).controller('core', ['$scope', function ($scope) {
	$scope.pageLoadingPause = true;
	$pageLoading = $('#pageLoading');

	$scope.$on('pageLoading', function (event) {
		$scope.pageLoadingPause = !$scope.pageLoadingPause;
		$pageLoading.toggle();
	});
}]).controller('faqs', ['$scope', 'faqs', function ($scope, faqs) {
	$scope.$emit('pageLoading');
	$scope.catMap = {};
	$scope.aFAQs = {};
	for (key in faqs.categories) 
		$scope.catMap[faqs.categories[key]] = key;
	faqs.get().then(function (data) {
		if (data.faqs) {
			$scope.$emit('pageLoading');
			$scope.aFAQs = data.faqs;
		}
	});
}]).controller('about', ['$scope', '$filter', 'Links', function ($scope, $filter, Links) {
	$scope.$emit('pageLoading');
	$scope.links = [];
	Links.get({ 'level': ['Affiliate', 'Partner'], 'networks': 'rpga', 'or': true }).then(function (data) {
		data = data.data;
		$scope.links.partners = $filter('filter')(data.links, { 'level': 'Partner' });
		$scope.links.rpgan = $filter('filter')(data.links, { 'networks': 'rpga' });
		$scope.links.affiliates = $filter('filter')(data.links, { 'level': 'Affiliate' });
		$scope.$emit('pageLoading');
	});
}]);
var controllers = angular.module('controllers', []);