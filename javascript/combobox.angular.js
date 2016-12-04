angular.module('rsCombobox', ['rx'])
.directive('combobox', ['$filter', '$timeout', 'rx', function ($filter, $timeout, rx) {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/combobox.html',
		scope: {
			'data': '<',
			'search': '<?',
			'change': '&'
		},
		link: function (scope, element, attrs) {
			var select = !isUndefined(attrs.select)?true:false;
			scope.bypassFilter = true;
			scope.value = '';
			if (!isUndefined(attrs.placeholder)) {
				element.find('input').attr('placeholder', attrs.placeholder);
			}
			if (!isUndefined(attrs.inputid)) {
				element.find('input').attr('id', attrs.inputid);
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

			scope.filterData = function () {
				return $filter('filter')(scope.options, (!scope.bypassFilter || '') && { 'display': scope.search.toString() });
			};
			scope.$watch(function () { return scope.data; }, function (newVal, oldVal) {
				scope.options = [];
				if (isUndefined(scope.data) || (scope.data instanceof Array && scope.data.length === 0)) {
					return;
				}
				var optsIsArray = Array.isArray(scope.data);
				for (var key in scope.data) {
					val = scope.data[key];
					if (typeof val != 'object') {
						val = { 'display': val };
						val.value = optsIsArray?val.display:key;
					} else if (!isUndefined(val.display) && val.display.length && (isUndefined(val.value) || val.value.length === 0)) {
						val.value = val.display;
					} else if (isUndefined(val.display) || val.display.length === 0) {
						continue;
					}

					val = {
						'value': decodeHTML(val.value),
						'display': decodeHTML(val.display),
						'class': !isUndefined(val.class)?val.class:[]
					};
					scope.options.push(val);
				}
				filterResults = $filter('filter')(scope.options, { 'value': scope.value }, true);
				if (select && scope.options.length && (isUndefined(scope.value) || scope.value === null || scope.value === '' || filterResults.length === 0) && !scope.hasFocus) {
					scope.value = scope.options[0].value;
					scope.search = scope.options[0].display;
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
			scope.inputChanged = function () {
				scope.showDropdown = true;
			};

			scope.toggleDropdown = function ($event) {
				$event.stopPropagation();
				if (scope.filterData().length)
					scope.showDropdown  = !scope.showDropdown;
			};
			scope.$watch(function () { return scope.showDropdown; }, function (newVal, oldVal) {
				if (scope.showDropdown && scope.filterData().length) {
					scope.curSelected = -1;
				} else {
					element.find('.selected').removeClass('selected');
					scope.bypassFilter = true;
				}
			});
			$('html').click(function () {
				scope.showDropdown = false;
				scope.$apply();
			});

			scope.$watch(function () { return scope.search; }, function (newVal, oldVal) {
				if (isUndefined(scope.search)) {
					scope.search = '';
				}
				filterResults = $filter('filter')(scope.options, { 'display': scope.search.toString() }, select?true:false);
				if (filterResults.length == 1) {
					scope.value = filterResults[0].value;
				}
				scope.change({ search: scope.search, value: scope.value });
			});
			scope.$watch(function () { return scope.value; }, function (newVal, oldVal) {
				scope.change({ search: scope.search, value: scope.value });
			});

			scope.$watch(function () { return scope.hasFocus; }, function (newVal, oldVal) {
				if (!newVal) {
					if (!isUndefined(scope.search) && scope.search.length !== 0) {
						filterResults = $filter('filter')(scope.options, { 'display': scope.search.toString() });
						if (filterResults.length == 1 && filterResults[0].display.toLowerCase() == scope.search.toLowerCase()) {
							scope.search = filterResults[0].display;
							scope.value = filterResults[0].value;
						} else if (filterResults.length >= 1) {
							noResults = true;
							for (var key in filterResults) {
								if (filterResults[key].display.toLowerCase() == scope.search.toLowerCase()) {
									noResults = false;
									scope.search = filterResults[key].display;
									scope.value = filterResults[key].value;
									break;
								}
							}
							if (noResults) {
								if (select) {
									scope.value = scope.options[0].value;
									scope.search = scope.options[0].display;
								} else {
									scope.search = '';
									scope.value = '';
								}
							}
						} else if (!select) {
							scope.value = scope.search;
						} else {
							scope.value = '';
						}
					}
				}
			});

			var $resultsWrapper = element.find('.results');
			scope.navigateResults = function ($event) {
				if ($event.keyCode == 13) {
					if (scope.showDropdown) {
						$event.preventDefault();
					}
					scope.value = '';
					if (scope.curSelected == -1) {
						filterResults = $filter('filter')(scope.options, (!scope.bypassFilter || '') && { 'display': scope.search.toString() }, true);
						if (filterResults.length == 1) {
							scope.setBox(filterResults);
						}
					} else {
						filterResults = $filter('filter')(scope.options, (!scope.bypassFilter || '') && { 'display': scope.search.toString() });
						scope.setBox(filterResults[scope.curSelected]);
					}
				} else if ($event.keyCode == 38 || $event.keyCode == 40) {
					$event.preventDefault();
					if (!scope.showDropdown) {
						scope.showDropdown = true;
					}
					$results = $($resultsWrapper).children();
					resultsHeight = $resultsWrapper.height();

					if ($event.keyCode == 40) {
						scope.curSelected += 1;
						if (scope.curSelected >= $results.length) {
							scope.curSelected = 0;
						}
					} else if ($event.keyCode == 38) {
						scope.curSelected -= 1;
						if (scope.curSelected < 0) {
							scope.curSelected = $results.length - 1;
						}
					}

					if ($results[scope.curSelected].offsetTop + $($results[scope.curSelected]).outerHeight() > $resultsWrapper.scrollTop() + resultsHeight) {
						$resultsWrapper.scrollTop($results[scope.curSelected].offsetTop + $($results[scope.curSelected]).outerHeight() - resultsHeight);
					} else if ($results[scope.curSelected].offsetTop < $resultsWrapper.scrollTop()) {
						$resultsWrapper.scrollTop($results[scope.curSelected].offsetTop);
					}
				} else if ($event.keyCode == 27) {
					scope.showDropdown = false;
				} else {
					scope.bypassFilter = false;
				}
			};

			scope.setBox = function (set) {
				scope.value = set.value;
				scope.search = set.display;
				scope.hasFocus = false;
				scope.bypassFilter = true;
				scope.showDropdown = false;
			};
			scope.setSelected = function (index) {
				scope.curSelected = index;
			};
		}
	};
}]);
