function hex(x) {
	return ("0" + parseInt(x).toString(16)).slice(-2);
}

$.cssHooks.backgroundColor = {
    get: function(elem) {
		var bg;
        if (elem.currentStyle) {
            bg = elem.currentStyle.backgroundColor;
		}
        else if (window.getComputedStyle) {
            bg = document.defaultView.getComputedStyle(elem, null).getPropertyValue("background-color");
		}
        if (bg.search("rgb") == -1) {
            return bg;
        } else {
            bg = bg.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            hexString = /*"#" + */hex(bg[1]) + hex(bg[2]) + hex(bg[3]);
            return hexString.toUpperCase();
        }
    }
};

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
				if (data.success === true) {
					parent.window.location.reload();
				}
			}
		});
		$('form.ajaxForm_closeCB').append('<input type="hidden" name="modal" value="1">').ajaxForm({
			dataType: 'json',
			success: function (data) {
				if (data.success === true) {
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

	if ($('#fixedMenu').size()) {
		var $fixedMenu = $('#fixedMenu_window');
		$('html').click(function () {
			$fixedMenu.find('.submenu, .subwindow').slideUp(250);
		});

		var fm_currentlyOpen = '';
		$fixedMenu.click(function (e) { e.stopPropagation(); });
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
			if (dice !== '') fm_rollDice(dice);

			e.preventDefault();
		});

		$('#fm_diceRoller input').keypress(function (e) {
			if (e.which == 13) {
				var dice = $(this).val();
				if (dice !== '') fm_rollDice(dice);

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
				if ($(this).val().length === 0) return false;
			});

			return true;
		},
		success: function (data) {
			if (data == '1') {
				parent.window.location.reload();
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
	var curPage;
	if (!$('body').hasClass('modal'))
		curPage = $('#content > div > div').attr('id').substring(5);
	else
		curPage = $('body > div').attr('id').substring(5);
});

var app = angular.module('gamersplane', ['controllers', 'ngCookies', 'ngSanitize', 'ngAnimate', 'ngFileUpload', 'angularMoment', 'rsCombobox']);
app.config(['$httpProvider', function ($httpProvider) {
	$httpProvider.defaults.withCredentials = true;
}]).factory('CurrentUser', ['$http', function ($http) {
	var factory = {};
	var userData = null;

	factory.load = function () {
		return $http.post(API_HOST + '/users/getCurrentUser/').then(function (data) {
			userData = data.data.loggedOut?null:data.data;
			return data.data.loggedOut?false:true;
		});
	};

	factory.get = function () {
		return userData;
	};

	factory.getLFG = function () {
		return $http.post(API_HOST + '/users/getLFG/').then(function (data) {
			return data.data.lfg;
		});
	};

	factory.saveLFG = function (lfg) {
		return $http.post(API_HOST + '/users/saveLFG/', { 'lfg': lfg }).then(function (data) {
			return data.data.lfg;
		});
	};

	return factory;
}]).service('UsersService', ['$http', 'Upload', function ($http, Upload) {
	this.getHeader = function () {
		return $http.post(API_HOST + '/users/getHeader/').then(function (data) {
			return data.data;
		});
	};
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
	this.search = function (params) {
		return $http.get(API_HOST + '/users/search/', { 'params': params }).then(function (data) {
			return data.data;
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
		if (typeof lastActivity == 'number')
			lastActivity *= 1000;
		lastActivity = moment(lastActivity);
		now = moment();
		diff = now - lastActivity;
		diff = Math.floor(diff / (1000 * 60 * 60 * 24));
		if (diff < 14)
			return null;
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
	this.suspend = function (userID, until) {
		return $http.post(API_HOST + '/users/suspend/', { 'userID': userID, 'until': until }).then(function (data) {
			return data.data;
		});
	};
}]).service('SystemsService', ['$http', function ($http) {
	this.systems = {};
	this.init = function () {
		var self = this;
		this.get({ 'getAll': true, 'basic': true }).then(function (data) {
			var systems = {};
			data.systems.forEach(function (val) {
				self.systems[val.shortName] = val.fullName;
			});
		});
	};
	this.get = function (params) {
		if (typeof params != 'object' || Array.isArray(params))
			params = {};
		return $http.post(API_HOST + '/systems/get/', params).then(function (data) { return data.data; });
	};
	this.getGenres = function () {
		return $http.post(API_HOST + '/systems/getGenres/').then(function (data) { return data.data; });
	};
	this.save = function (systemData) {
		return $http.post(API_HOST + '/systems/save/', { data: systemData }).then(function (data) { return data.data; });
	};
}]).service('ToolsService', ['$http', function ($http) {
	this.deckTypes = {};
	this.init = function () {
		var self = this;
		this.getDeckTypes().then(function (data) {
			data.types.forEach(function (val) {
				self.deckTypes[val._id] = val;
			});
		});
	};
	this.getDeckTypes = function () {
		return $http.post(API_HOST + '/tools/getDeckTypes/').then(function (data) { return data.data; });
	};
}]).service('LanguageService', [function () {
	this.userProfileLink = function (userID, username) {
		return '<a href="/user/' + userID + '/" class="username">' + username + '</a>';
	};
	this.characterLink = function (characterID, systemShort, label) {
		return '<a href="/characters/' + systemShort + '/' + characterID + '/">' + label + '</a>';
	};
	this.gameLink = function (gameID, title) {
		return '<a href="/games/' + gameID + '/">' + title + '</a>';
	};
}]).service('ContactService', ['$http', function ($http) {
	this.send = function (fields) {
		return $http.post(API_HOST + '/contact/send/', fields).then(function (data) {
			return data.data;
		});
	};
}]).service('ForumsService', ['$http', function ($http) {
	this.getSubscriptions = function (fields) {
		return $http.post(API_HOST + '/forums/getSubscriptions/', fields).then(function (data) {
			return data.data;
		});
	};
	this.unsubscribe = function (userID, type, id) {
		return $http.post(API_HOST + '/forums/unsubscribe/', {
			userID: userID,
			type: type,
			id: id
		}).then(function (data) {
			return data.data;
		});
	};
}]).service('Links', ['$http', function ($http) {
	this.categories = [ 'Blog', 'Podcast', 'Videocast', 'Liveplay', 'Devs', 'Accessories' ];
	this.get = function (params) {
		if (typeof params != 'object' || Array.isArray(params))
			params = {};
		return $http.post(API_HOST + '/links/get/', params).then(function (data) { return data; });
	};
}]).service('faqs', ['$http', '$q', function ($http, $q) {
	this.categories = { 'getting-started': 'Getting Started', 'characters': 'Characters', 'games': 'Games', 'tools': 'Tools' };
	this.get = function () {
		return $http.post(API_HOST + '/faqs/get/').then(function (data) { return data.data; });
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
		$http.post(API_HOST + '/faqs/save/', { 'category': faq.category, 'question': faq.question, 'answer': faq.answer }).success(function (data) { deferred.resolve(data); });
		return deferred.promise;
	};
	this.delete = function (id) {
		var deferred = $q.defer();
		$http.post(API_HOST + '/faqs/delete/', { 'id': id }).success(function (data) { deferred.resolve(data); });
		return deferred.promise;
	};
}]).service('GamesService', ['$http', function ($http) {
	this.getGames = function (params) {
		if (typeof params != 'undefined' && typeof params.systems != 'undefined' && Array.isArray(params.systems)) {
			params.systems = params.systems.join(',');
		}
		return $http.get(
			API_HOST + '/games/getGames/',
			{
				'params': params
			}
		).then(function (data) {
			if (data.data.success) {
				return data.data.games;
			}
		});
	};
	this.getDetails = function (gameID) {
		return $http.post(
			API_HOST + '/games/details/',
			{
				'gameID': gameID
			}
		).then(function (data) { return data.data; });
	};
	this.getLFG = function (lfgCount) {
		lfgCount = parseInt(lfgCount) >= 0?parseInt(lfgCount):10;
		return $http.post(API_HOST + '/games/getLFG/', { count: lfgCount }).then(function (data) { return data.data.lfgs; });
	};
	this.create = function (details) {
		return $http.post(API_HOST + '/games/create/', details).then(function (data) { return data.data; });
	};
	this.update = function (details) {
		return $http.post(API_HOST + '/games/update/', details).then(function (data) { return data.data; });
	};
	this.apply = function (gameID) {
		return $http.post(API_HOST + '/games/apply/', { 'gameID': gameID }).then(function (data) { return data.data; });
	};
	this.toggleGameStatus = function (gameID) {
		return $http.post(API_HOST + '/games/toggleGameStatus/', { 'gameID': gameID }).then(function (data) { return data.data; });
	};
	this.toggleForum = function (gameID) {
		return $http.post(API_HOST + '/games/toggleForum/', { 'gameID': gameID }).then(function (data) { return data.data; });
	};
	this.confirmRetire = function (gameID) {
		return $http.post(API_HOST + '/games/retire/', { 'gameID': gameID }).then(function (data) { return data.data; });
	};
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
	};
}]).service('initializeVars', [function () {
	this.setup = function (scope) {
		return scope;
	};
}]).service('CharactersService', ['$http', '$q', function ($http, $q) {
	this.getMy = function (params) {
		return $http.post(API_HOST + '/characters/my/', params).then(function (data) { return data.data; });
	};
	this.getLibrary = function (params) {
		if (typeof params == 'undefined')
			params = {};
		return $http.post(API_HOST + '/characters/library/', params).then(function (data) { return data.data; });
	};
	this.new = function (data) {
		return $http.post(API_HOST + '/characters/new/', {
			'label': data.label,
			'system': data.system,
			'charType': data.charType
		}).then(function (data) { return data.data; });
	};
	this.saveBasic = function (data) {
		return $http.post(API_HOST + '/characters/saveBasic/', {
			'characterID': data.characterID,
			'label': data.label,
			'charType': data.charType
		}).then(function (data) { return data.data; });
	};
	this.toggleLibrary = function (characterID) {
		return $http.post(API_HOST + '/characters/toggleLibrary/', { 'characterID': characterID }).then(function (data) { return data.data; });
	};
	this.delete = function (data) {
		return $http.post(API_HOST + '/characters/delete/', { 'characterID': data.characterID }).then(function (data) { return data.data; });
	};
	this.toggleFavorite = function (characterID) {
		return $http.post(API_HOST + '/characters/toggleFavorite/', { 'characterID': characterID }).then(function (data) { return data.data; });
	};
	this.load = function (characterID, printReady/*options*/) {
		if (typeof options != 'object') {
			options = {};
		}
		var validOptions = {
			'pr': {
				'type': 'boolean',
				'default': false
			}
		},
		postData = { 'characterID': parseInt(characterID) };
		for (var option in validOptions) {
			if (option in options && typeof options[option] == validOptions[option].type) {
				postData[option] = options[option];
			}
		}
		if (printReady === true) {
			postData.printReady = true;
		}
		return $http.post(API_HOST + '/characters/load/', postData).then(function (data) { return data.data; });
	};
    this.getBookData = function (system) {
        return $http.post(API_HOST + '/characters/getBookData/', { 'system': system }).then(function (data) { return data.data; });
    };
	this.save = function (characterID, character) {
		return $http.post(API_HOST + '/characters/save/', { 'characterID': characterID, 'character': character }).then(function (data) { return data.data; });
	};
	this.loadBlanks = function (character, blanks) {
		if (typeof blanks == 'undefined' || Object.keys(blanks).length === 0)
			return;
		for (var key in blanks) {
			if (key.indexOf('.') < 0)
				bArray = character[key];
			else
				bArray = character[key.split('.')[0]][key.split('.')[1]];
			if (!Array.isArray(bArray))
				bArray = [];
			if (typeof bArray != 'undefined' && Object.keys(bArray).length === 0)
				bArray.push(copyObject(blanks[key]));
			character[key] = bArray;
		}
	};
}]).service('Range', function () {
	this.get = function (from, to, incBy) {
		incBy = parseInt(incBy);
		if (Math.round(incBy) != incBy || incBy === 0)
			incBy = 1;
		range = [];
		for (count = from; count <= to; count += incBy)
			range.push(count);
		return range;
	};
}).directive('skewElement', function () {
	return {
		restrict: 'A',
		link: function (scope, element, attrs) {
			$element = $(element);
			if ($element.children('div.skewedDiv').length)
				return;
			var skewDeg = 0;
			if (attrs.skewElement !== '')
				skewDeg = parseInt(attrs.skewElement);
			if (skewDeg === 0)
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
	};
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
	};
}).directive('paginate', ['$timeout', function ($timeout) {
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
				if (typeof scope.changeFunc == 'function') {
					$timeout(scope.changeFunc);
				}
			};
		}
	};
}])
.directive('prettyCheckbox', ['$timeout', function ($timeout) {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/prettyCheckbox.php',
		scope: {
			'checkbox': '=checkbox',
			'cbValue': '=value'
		},
		link: function (scope, element, attrs) {
			scope.cbm = false;
			var eleID = null, label = null, wrapperLabel = false;
			$timeout(function () {
				if ((scope.checkbox instanceof Array && scope.checkbox.indexOf(scope.cbValue) != -1) || !(scope.checkbox instanceof Array) && scope.checkbox)
					scope.cbm = true;
				eleID = typeof attrs.eleid == 'string' && attrs.eleid?attrs.eleid:null;
				$label = $(element).closest('label');
				if (!$label.length && eleID)
					$label = $('label[for=' + eleID + ']');
				else if ($label.length)
					wrapperLabel = true;
				if ($label.length)
					$label.on('click', function ($event) {
						$event.preventDefault();
						if ($event.target.nodeName !== 'DIV') {
							scope.toggleCB();
							scope.$apply();
						}
					});
			});

			scope.toggleCB = function () {
				scope.cbm = !scope.cbm;
			};

			scope.$watch(function () { return scope.cbm; }, function (val, oldVal) {
				if (val == oldVal)
					return;

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

			scope.$watch(function () { return scope.checkbox; }, function (newVal, oldVal) {
				if (scope.checkbox instanceof Array)
					scope.cbm = scope.checkbox.indexOf(scope.cbValue) != -1?true:false;
				else
					scope.cbm = scope.checkbox?true:false;
			});
		}
	};
}]).directive('prettyRadio', [function () {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/prettyRadio.php',
		scope: {
			'radio': '=radio',
			'rValue': '=rValue'
		},
		link: function (scope, element, attrs) {
			scope.inputID = typeof attrs.eleid == 'string'?attrs.eleid:'';

			var label = null, wrapperLabel = false;
			label = $(element).closest('label');
			if (!label.length && typeof attrs.eleid == 'string' && attrs.eleid) {
//				element.attr('id', attrs['eleid']);
				label = $('label[for=' + attrs.eleid + ']');
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
			};
		}
	};
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
	};
}]).directive('ngPlaceholder', ['$timeout', function ($timeout) {
	return {
		restrict: 'A',
		require: 'ngModel',
		scope: {},
		link: function (scope, element, attrs, ngModel) {
			var placeholder = attrs.ngPlaceholder;
			if (typeof placeholder == 'string' && placeholder.length) {
				$timeout(function () {
					var $input = $(element);
					$input.addClass('placeholder');
					if ($input.val() === '' || $input.val() == placeholder)
						$input.addClass('default');
					$input.focus(function () {
						if ($input.val() == placeholder || $input.val() === '')
							$input.val('').removeClass('default');
					}).blur(function () {
						if ($input.val() === '')
							$input.val(placeholder).addClass('default');
					}).blur();
					scope.$watch(function () { return ngModel.$modelValue; }, function (val) {
						if (val != placeholder && typeof val != 'undefined' && val !== '')
							$input.removeClass('default');
						else if (val == placeholder || val === '')
							$input.addClass('default');
					});
				});
			}
		}
	};
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
			};
			scope.fadeOut = function () {
				$timeout(function () {
					if (!scope.pause) {
						running = true;
						foreground.fadeOut(fadeTime, scope.fadeIn);
					} else
						running = false;
				}, fadePauseT);
			};
			if (!scope.pause)
				scope.fadeIn();
			scope.$watch(function () { return scope.pause; }, function () {
				if (!scope.pause && !running)
					scope.fadeIn();
			});
		}
	};
}]).directive('userLink', [function () {
	return {
		restrict: 'E',
		template: '<a href="/user/{{user.userID}}/" class="username" ng-bind-html="user.username"></a>',
		scope: {
			'user': '='
		},
		link: function (scope, element, attrs) {
		}
	};
}]).filter('trustHTML', ['$sce', function($sce){
	return function(text) {
		if (typeof text != 'string')
			text = '';
		return $sce.trustAsHtml(text);
	};
}]).filter('paginateItems', [function () {
	return function (input, limit, skip) {
		output = [];
		count = -1;
		for (var key in input) {
			count++;
			if (count < skip)
				continue;
			else if (count >= limit + skip)
				break;
			output.push(input[key]);
		}
		return output;
	};
}]).filter('intersect', [function () {
	return function (input, field, compareTo) {
		if (compareTo.length === 0)
			return input;
		output = [];
		for (var key in input) {
			for (var iKey in compareTo) {
				if (input[key][field].indexOf(compareTo[iKey]) >= 0) {
					output.push(input[key]);
					break;
				}
			}
		}
		return output;
	};
}]).filter('convertTZ', [function () {
	return function (dtString, parseString, displayString) {
		parseString = !isUndefined(parseString)?parseString:'MMM D, YYYY h:mm a';
		displayString = !isUndefined(displayString)?displayString:'MMM D, YYYY h:mm a';

		utcDT = moment.utc(dtString, parseString);
		return utcDT.local().format(displayString);
	};
}]).filter('ceil', [function () {
	return function (input) {
		return Math.ceil(input);
	};
}]).controller('core', ['$scope', '$window', 'SystemsService', function ($scope, $window, SystemsService) {
	$scope.pageLoadingPause = true;
	$pageLoading = $('#pageLoading');

	$scope.$on('pageLoading', function (event) {
		$scope.pageLoadingPause = !$scope.pageLoadingPause;
		$pageLoading.toggle();
	});

	$scope.clearPageLoading = function(count) {
		count--;
		if (count === 0)
			$scope.$emit('pageLoading');
		return count;
	};
}]).controller('header', ['$scope', '$timeout', 'UsersService', function ($scope, $timeout, UsersService) {
	$scope.characters = [];
	$scope.games = [];
	$scope.avatar = '';
	$scope.pmCount = 0;
	UsersService.getHeader().then(function (data) {
		$scope.loggedIn = data.success?true:false;
		if ($scope.loggedIn) {
			$scope.characters = data.characters;
			$scope.games = data.games;
			$scope.avatar = data.avatar;
			$scope.pmCount = data.pmCount;
		}
	});

	var $header = $('#bodyHeader'),
		$headerEles = $('#bodyHeader, #bodyHeader > *'),
		$logo = $('#headerLogo img'),
		scrollPos = $(window).scrollTop(),
		headerHeight = $header.height(),
		scrollTimeout = null,
		ratio = 1,
		$mainMenu = $('#mainMenu');

	$mainMenu.on('click', 'li', function ($event) {
		$event.stopPropagation();
		if ($(this).parent()[0] == $mainMenu[0] && $(this).children('ul').length) {
			$event.preventDefault();
			$(this).children('ul').stop(true, true).slideDown();
		}
	});
	$('html').click(function ($event) {
		$mainMenu.find('li').children('ul').stop(true, true).slideUp();
	});
	$timeout(function () {
		$headerEles.height(scrollPos < 50?120 - scrollPos:70);
		ratio = (scrollPos < 50?scrollPos:50) / 50;
		$logo.height(100 - 47 * ratio);
	});
	$(window).scroll(function () {
		scrollPos = $(this).scrollTop();
		headerHeight = $header.height();
		// console.log(scrollPos);
		if (scrollPos >= 0 && scrollPos <= 50) {
//			scrollTimeout = setTimeout(function () {
				$headerEles.height(scrollPos < 50?120 - scrollPos:70);
				ratio = (scrollPos < 50?scrollPos:50) / 50;
				$logo.height(100 - 47 * ratio);
//			}, 100);
		} else if ($headerEles.height() > 70) {
			$headerEles.height(70);
			$logo.height(53);
		}
	});
}]).controller('landing', ['$scope', '$timeout', 'SystemsService', 'GamesService', function ($scope, $timeout, SystemsService, GamesService) {
	$scope.games = [];
	GamesService.getGames({
		'limit': 4,
		'sort': 'created',
		'sortOrder': -1
	}).then(function (data) {
		$scope.games = data;
	});
	$scope.systems = [{ 'value': 'all', 'display': 'All' }];
	SystemsService.get({ 'getAll': true, 'excludeCustom': true }).then(function (data) {
		for (var key in data.systems) {
			$scope.systems.push({
				'value': data.systems[key].shortName,
				'display': data.systems[key].fullName
			});
		}
	});
	$scope.setSystem = function (system) {
		if (system == 'all') {
			system = null;
		}
		GamesService.getGames({
			'systems': system,
			'limit': 3,
			'sort': 'created',
			'sortOrder': -1
		}).then(function (data) {
			$scope.games = data;
		});
	};

	$scope.signup = {
		'username': '',
		'password': ''
	};
	$scope.formFocus = '';
	$scope.setFormFocus = function (input) {
		if (input == $scope.formFocus) {
			return;
		}
		$scope.formFocus = input;
		if (input !== '') {
			$timeout(function () {
				$('#landing_signup_' + input + ' input').focus();
			});
		}
	};

	$scope.whatIsLogos = ['dnd5', 'thestrange', 'pathfinder', 'starwarsffg', '13thage', 'numenera', 'shadowrun5', 'fate', 'savageworlds'];
}]).controller('faqs', ['$scope', 'faqs', function ($scope, faqs) {
	$scope.$emit('pageLoading');
	$scope.catMap = {};
	$scope.aFAQs = {};
	for (var key in faqs.categories) {
		$scope.catMap[key] = faqs.categories[key];
	}
	faqs.get().then(function (data) {
		if (data.faqs) {
			$scope.$emit('pageLoading');
			$scope.aFAQs = data.faqs;
		}
	});
}]).controller('about', ['$scope', '$filter', 'Links', function ($scope, $filter, Links) {
	$scope.$emit('pageLoading');
	$scope.links = [];
	Links.get({
		'level': ['Affiliate', 'Partner'],
		'networks': 'rpga',
		'or': true
	}).then(function (data) {
		data = data.data;
		$scope.links.partners = $filter('filter')(data.links, { 'level': 'Partner' });
		$scope.links.rpgan = $filter('filter')(data.links, { 'networks': 'rpga' });
		$scope.links.affiliates = $filter('filter')(data.links, { 'level': 'Affiliate' });
		$scope.$emit('pageLoading');
	});
}]);
var controllers = angular.module('controllers', []);
