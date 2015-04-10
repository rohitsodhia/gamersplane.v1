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

	$('.headerbar, .fancyButton, .wingDiv').each(setupWingContainer);
	$('.wing').each(setupWings);
	if ($('.headerbar .wing').length) {
		leftMargin = $('.headerbar .wing').css('border-right-width');
		$('.hbMargined:not(textarea)').css({ 'margin-left': leftMargin, 'margin-right': leftMargin });
		$('.hbTopper').css({ 'marginLeft': leftMargin });

		leftMargin = leftMargin.slice(0, -2);
		$('textarea.hbMargined').each(function () {
			tWidth = $(this).parent().width();
			$(this).css({ 'margin-left': leftMargin + 'px', 'margin-right': leftMargin + 'px', 'width': (tWidth - 2 * leftMargin) + 'px' });
		});
	}
	if ($('.hbDark .wing').length) {
		leftMargin = $('.hbDark .wing').css('border-right-width');
		$('.hbdMargined:not(textarea)').css({ 'margin-left': leftMargin, 'margin-right': leftMargin });
		$('.hbdTopper').css({ 'marginLeft': leftMargin });

		leftMargin = leftMargin.slice(0, -2);
		$('textarea.hbdMargined').each(function () {
			tWidth = $(this).parent().width();
			$(this).css({ 'margin-left': leftMargin + 'px', 'margin-right': leftMargin + 'px', 'width': (tWidth - 2 * leftMargin) + 'px' });
		});
	}

/*	$('.ofToggle').not('.disable').click(function (e) {
		e.preventDefault();

		$(this).toggleClass('on');
	});*/

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

var app = angular.module('gamersplane', ['controllers', 'ngCookies', 'ngSanitize']);
app.config(function ($httpProvider) {
	$httpProvider.defaults.withCredentials = true;
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
			'value': '=value'
		},
		link: function (scope, element, attrs) {
			scope.value = '';
			scope.search = '';
			if (typeof attrs.placeholder != 'undefined') 
				element.find('input').attr('placeholder', attrs.placeholder);
			scope.showDropdown = false;
			$combobox = element.children('.combobox');
			$combobox.children('.results').css({ 'top': $combobox.outerHeight(), 'width': $combobox.outerWidth() });
			$combobox.children('.dropdown').css('height', $combobox.outerHeight());
			if (typeof scope.data == 'undefined') 
				scope.data = [];
			var oldIndex = currentIndex = -1;

			scope.toggleDropdown = function ($event) {
				oldIndex = currentIndex = -1;
				$event.stopPropagation();
				if ((isNaN(scope.search) || scope.search.length == 0) && $filter('filter')(scope.data, scope.search).length) 
					scope.showDropdown = scope.showDropdown?false:true;
			};
			scope.revealDropdown = function () {
				scope.value = '';
				for (key in scope.data) 
					if (scope.search == scope.data[key].value) 
						scope.value = scope.data[key].id;
				if (scope.value.length == 0) 
					scope.value = scope.search;
				if ((isNaN(scope.search) || scope.search.length == 0) && $filter('filter')(scope.data, scope.search).length) {
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
			};
			$('html').click(function () {
				scope.hideDropdown();
				scope.$apply();
			});

			scope.navigateResults = function ($event) {
				if ($event.keyCode == 13) {
					var set = $($resultsWrapper).find('.selected').data('$scope')['set'];
					scope.setBox(set);
				} else {
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
				}
			};

			scope.setBox = function (set) {
				scope.value = set.id;
				scope.search = set.value;
			};
			scope.setSelected = function (set, $event) {
				element.find('.results .selected').removeClass('selected');
				$($event.currentTarget).addClass('selected');
			}
		}
	}
}]).directive('prettySelect', [function () {
	return {
		restrict: 'E',
		transclude: true,
		templateUrl: '/angular/directives/select.php',
		link: function (scope, element, attrs) {
			$select = $(element).find('select');
			console.log($select.children());
			console.log(element.find('option'));
		}
	};
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
});
var controllers = angular.module('controllers', []);