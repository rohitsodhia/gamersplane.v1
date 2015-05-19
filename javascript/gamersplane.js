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

var app = angular.module('gamersplane', ['controllers', 'ngCookies', 'ngSanitize', 'angularFileUpload']);
app.config(function ($httpProvider) {
	$httpProvider.defaults.withCredentials = true;
}).factory('currentUser', function ($http) {
	return $http.post(API_HOST + '/users/getCurrentUser/').success(function (data) {
		if (data.loggedOut) 
			return null;
		else 
			return data;
	});
}).directive('skewElement', function () {
	return {
		restrict: 'A',
		link: function (scope, element, attrs) {
			$element = $(element);
			var skewDeg = 0;
			if (attrs.skewElement != '') 
				skewDeg = parseInt(attrs.skewElement);
			if (skewDeg == 0)
				skewDeg = -30;
			$skewDiv = $element.wrapInner('<div></div>').children('div');
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
}).directive('hbMargined', function () {
	return {
		restrict: 'A',
		link: function (scope, element, attrs) {
			$element = $(element);
			$headerbar = $(element).siblings('.headerbar');
			skewedOut = parseFloat($headerbar.data('skewedOut')) * 2;
			$element.css({ 'margin-left': skewedOut, 'margin-right': skewedOut });
		}
	};
}).directive('paginate', function () {
	return {
		restrict: 'E',
		replace: true,
		templateUrl: '/angular/directives/paginate.php'
	}
}).directive('combobox', ['$filter', '$timeout', function ($filter, $timeout) {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/combobox.php',
		scope: {
			'data': '=data',
			'search': '=search',
			'value': '=value',
		},
		link: function (scope, element, attrs) {
			scope.strict = typeof attrs.strict != 'undefined'?true:false;
			scope.bypassFilter = true;
			scope.value = '';
			var setupFinished = scope.$watch('data', function (newVal, oldVal) {
				if (scope.search != '') {
					$(scope.data).each(function (key, value) {
						if (value.value == scope.search) 
							scope.value = value.id;
					});
					if (scope.value == '') 
						scope.search = '';
				} else
					scope.search = '';
				if (typeof attrs.placeholder != 'undefined') 
					element.find('input').attr('placeholder', attrs.placeholder);
				scope.showDropdown = false;
				scope.hasFocus = false;
				$combobox = element.children('.combobox');
				$combobox.children('.results').css({ 'top': $combobox.outerHeight(), 'width': $combobox.outerWidth() });
				$combobox.children('.dropdown').css('height', $combobox.outerHeight());
				$input = $combobox.children('input');
				if (typeof scope.data == 'undefined') 
					scope.data = [];
				var oldIndex = currentIndex = -1;

				setupFinished();
			});

			scope.toggleDropdown = function ($event) {
				oldIndex = currentIndex = -1;
				$event.stopPropagation();
				if ((isNaN(scope.search) || scope.search.length == 0) && $filter('filter')(scope.data, (!scope.bypassFilter || '') && scope.search).length) {
					scope.showDropdown = scope.showDropdown?false:true;
					scope.hasFocus = scope.showDropdown?true:false;
				}
			};
			scope.revealDropdown = function () {
				scope.hasFocus = true;
				scope.value = '';
				for (key in scope.data) 
					if (scope.search == scope.data[key].value) 
						scope.value = scope.data[key].id;
				if (typeof scope.value != 'undefined' && scope.value.length == 0) 
					scope.value = scope.search;
				if ((isNaN(scope.search) || scope.search.length == 0) && $filter('filter')(scope.data, (!scope.bypassFilter || '') && scope.search).length) {
					oldIndex = currentIndex = -1;
					scope.showDropdown = true;
				} else {
					oldIndex = currentIndex = -1;
					element.find('.selected').removeClass('selected');
					scope.showDropdown = false;
				}
			};
			scope.hideDropdown = function () {
				element.find('.selected').removeClass('selected');
				scope.showDropdown = false;
				scope.bypassFilter = true;
			};
			$('html').click(function () {
				scope.hideDropdown();
				scope.hasFocus = false;
				scope.$apply();
			});

			scope.$watch('hasFocus', function (newVal, oldVal) {
				if (!newVal) {
					if (scope.strict && scope.search.length > 0) {
						bypassHold = scope.bypassFilter;
						scope.bypassFilter = false;
						filterResults = $filter('filter')(scope.data, (!scope.bypassFilter || '') && scope.search);
						if (filterResults.length == 1 && filterResults[0].value.toLowerCase() == scope.search.toLowerCase()) {
							scope.search = filterResults[0].value;
							scope.value = filterResults[0].id;
						} else {
							noResults = true;
							for (key in filterResults) {
								if (filterResults[key].value == scope.search) {
									noResults = false;
									scope.search = filterResults[key].value;
									scope.value = filterResults[key].id;
									break
								}
							}
							if (noResults) {
								scope.search = '';
								scope.value = '';
							}
						}
						scope.bypassFilter = bypassHold;
					}
				}
			});

			scope.navigateResults = function ($event) {
				if ($event.keyCode == 13) {
					$selected = element.find('.results').find('.selected');
					if ($selected.length == 0) {
						$(scope.data).each(function (key, value) {
							if (value.value == scope.search) 
								scope.setBox(value);
						});
					} else 
						scope.setBox($selected.data('$scope')['set']);
				} else if ($event.keyCode == 38 || $event.keyCode == 40) {
					$resultsWrapper = element.find('.results');
					$results = $($resultsWrapper).children();
					resultsHeight = $resultsWrapper.height();
					$results.each(function (key, value) {
						if ($(this).hasClass('selected')) {
							oldIndex = currentIndex = key;
						}
					});

					if ($event.keyCode == 40) 
						currentIndex += 1;
					else if ($event.keyCode == 38) 
						currentIndex -= 1;
					else 
						return;

					if (currentIndex < 0) 
						currentIndex = $results.length - 1;
					else if (currentIndex >= $results.length) 
						currentIndex = 0;

					if ($results[currentIndex].offsetTop + $($results[currentIndex]).outerHeight() > $resultsWrapper.scrollTop() + resultsHeight) 
						$resultsWrapper.scrollTop($results[currentIndex].offsetTop + $($results[currentIndex]).outerHeight() - resultsHeight);
					else if ($results[currentIndex].offsetTop < $resultsWrapper.scrollTop()) 
						$resultsWrapper.scrollTop($results[currentIndex].offsetTop);

					$($results[oldIndex]).removeClass('selected');
					$($results[currentIndex]).addClass('selected');
				} else 
					scope.bypassFilter = false;
			};

			scope.setBox = function (set) {
				scope.value = set.id;
				scope.search = set.value;
				scope.hasFocus = false;
				scope.bypassFilter = true;
				scope.hideDropdown();
			};
			scope.setSelected = function (set, $event) {
				element.find('.results .selected').removeClass('selected');
				$($event.currentTarget).addClass('selected');
			}
		}
	}
}]).directive('prettyCheckbox', [function () {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/prettyCheckbox.php',
		scope: {
			'checkbox': '=checkbox'
		},
		link: function (scope, element, attrs) {
			scope.checkbox = scope.checkbox?true:false;
			if (typeof attrs['eleid'] == 'string') 
				scope.inputID = attrs['eleid'];
			else 
				scope.inputID = '';

			scope.toggleCB = function ($event) {
				$event.stopPropagation();
				scope.checkbox = !scope.checkbox;
			}
		}
	}
}]).filter('trustHTML', ['$sce', function($sce){
	return function(text) {
		if (typeof text != 'string') 
			text = '';
		return $sce.trustAsHtml(text);
	}
}]).filter('paginateItems', function () {
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
}).filter('intersect', function () {
	return function (input, field, compareTo) {
		output = [];
		for (key in input) {
			for (iKey in compareTo) {
				if (compareTo[iKey] && input[key][field].indexOf(iKey) >= 0) {
					output.push(input[key]);
					break;
				}
			}
		}
		return output;
	}
}).filter('convertTZ', function () {
	return function (dtString, parseString, displayString) {
		parseString = typeof parseString !== 'undefined'?parseString:'MMM D, YYYY h:mm a';
		displayString = typeof displayString !== 'undefined'?displayString:'MMM D, YYYY h:mm a';

		utcDT = moment.utc(dtString, parseString);
		return utcDT.local().format(displayString);
	}
});
var controllers = angular.module('controllers', []);