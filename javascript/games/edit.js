controllers.controller('games_cu', ['$scope', '$http', '$filter', 'CurrentUser', 'GamesService', 'SystemsService', function ($scope, $http, $filter, CurrentUser, GamesService, SystemsService) {
	pathElements = getPathElements();
	CurrentUser.load().then(function () {
		CurrentUser = CurrentUser.get();
		$scope.CurrentUser = CurrentUser;
		$scope.state = pathElements[2] == 'edit' ? 'edit' : 'new';
		$scope.allSystems = {};
		$scope.systemsWCharSheets = {};
		$scope.lfg = [];
		$scope.game = {
			'title': '',
			'system': 'custom',
			'allowedCharSheets': [],
			'postFrequency': {
				'timesPer': 1,
				'perPeriod': 'd',
			},
			'numPlayers': 2,
			'charsPerPlayer': 1,
			'description': '',
			'charGenInfo': ''
		};
		$scope.charSheet = null;
		$scope.combobox = {
			'periods': [
				{ 'value': 'd', 'display': 'day' },
				{ 'value': 'w', 'display': 'week' }
			]
		};
		$scope.errors = [];

		SystemsService.get(
			{
				'getAll': true
			}
		).then(function (data) {
			$scope.allSystems = {};
			$scope.systemsWCharSheets = {};
			data.systems.forEach(function (val) {
				$scope.allSystems[val.shortName] = val.fullName;
				if (val.hasCharSheet) {
					$scope.systemsWCharSheets[val.shortName] = val.fullName;
				}
			});
			if ($scope.state == 'new') {
				GamesService.getLFG(10).then(function (data) {
					$scope.lfg = [];
					data.forEach(function (val) {
						$scope.lfg.push(val);
					});
				});
			} else {
				$scope.game.gameID = parseInt(pathElements[1]);
				GamesService.getDetails($scope.game.gameID).then(function (data) {
					$scope.game = data.details;
					$scope.game.allowedCharSheets.forEach(function (val) {
						delete $scope.systemsWCharSheets[val];
					});
					if ($scope.game.description == 'None Provided') {
						$scope.game.description = '';
					}
					if ($scope.game.charGenInfo == 'None Provided') {
						$scope.game.charGenInfo = '';
					}
					$('#gameOptions').updateFields($scope.game.gameOptions);
				});
			}


		});

		$scope.setSystem = function (system) {
			$scope.game.system = system;
		};

		$scope.setCharSheet = function (system) {
			$scope.charSheet = system;
		};
		$scope.addCharSheet = function () {
			if ($scope.errors.indexOf('noCharSheets') >= 0) {
				removeEle($scope.errors, 'noCharSheets');
			}
			$scope.game.allowedCharSheets.push($scope.charSheet);
			delete $scope.systemsWCharSheets[$scope.charSheet];
		};
		$scope.removeCharSheet = function (system) {
			key = $scope.game.allowedCharSheets.indexOf(system);
			if (key > -1) {
				$scope.game.allowedCharSheets.splice(key, 1);
				var hold = {},
					inserted = false,
					compSystemName = $scope.allSystems[system].toLowerCase();
				angular.forEach($scope.systemsWCharSheets, function (fullName, shortName) {
					if (!inserted && fullName.toLowerCase() > compSystemName) {
						this[system] = $scope.allSystems[system];
					}
					this[shortName] = fullName;
				}, hold);
				$scope.systemsWCharSheets = hold;
			}
		};

		$scope.validateTitle = function () {
			if ($scope.game.title.length > 0 && $scope.errors.indexOf('invalidTitle') >= 0) {
				removeEle($scope.errors, 'invalidTitle');
			} else if ($scope.game.title.length === 0) {
				$scope.errors.push('invalidTitle');
			}
		};

		$scope.setPeriod = function (period) {
			$scope.game.postFrequency.perPeriod = period;
		};

		$scope.save = function () {
			$scope.validateTitle();
			if ($scope.state == 'new' && $scope.game.allowedCharSheets.length === 0) {
				$scope.errors.push('noCharSheets');
			}
			if ($scope.errors.length) {
				return;
			}
			gameDetails = copyObject($scope.game);
			$scope.$emit('pageLoading');
			if ($scope.state == 'new') {
				GamesService.create(gameDetails).then(function (data) {
					if (data.success) {
						document.location.href = '/games/' + data.gameID + '/';
					} else if (data.failed) {
						$scope.errors = data.errors;
					}
					$scope.$emit('pageLoading');
				});
			} else {
				GamesService.update(gameDetails).then(function (data) {
					if (data.success) {
						document.location.href = '/games/' + data.gameID + '/';
					} else if (data.failed) {
						$scope.errors = data.errors;
					}
					$scope.$emit('pageLoading');
				});
			}
		};
	});
}]);

$(function () {

	var isValidJson = function (str) {
		try {
			str = str.replace(/[‘’]/g, "'").replace(/[“”]/g, '"');
			JSON.parse(str);
		} catch (e) {
			return false;
		}
		return true;
	}

	$('#gameOptions').on('change keyup blur', function () {
		$('#gameOptionsError').hide();
		var testJson = $.trim($('#gameOptions').val());
		if (testJson != '' && !isValidJson(testJson)) {
			$('#gameOptionsError').show();
		}
	});

	$('#gameOptions').on('blur', function () {
		$('#gameOptions').updateFields();
	});


	$('.markItUp').markItUp(mySettings);

	$.get('/forums/thread/22053/?pageSize=10000', function (data) {
		var diceRuleSection = $('#diceRules');
		var diceRegex = /[\"\']diceRules[\"\'][\s]*:[\s]*(\[.*?\])/gms
		$('.post .spoiler', $(data)).each(function () {
			var spoiler = $(this);
			var ruleTitle = $('.tag', spoiler).text();
			var ruleText = $('.hidden', spoiler).text();
			var matchRules = ruleText.match(diceRegex);
			if (matchRules && matchRules.length == 1) {

				ruleTitle = ruleTitle.substring(6).trim();
				var ruleJson = '{' + matchRules[0] + '}';
				if (isValidJson(ruleJson)) {
					var ruleObj = JSON.parse(ruleJson);
					if (ruleObj && ruleObj.diceRules && Array.isArray(ruleObj.diceRules)) {
						var shortCutDiv = $('<li class="diceRule"></li>').appendTo(diceRuleSection);
						shortCutDiv.text(ruleTitle);
						shortCutDiv.data('rulejson', JSON.stringify(ruleObj));
					}
				}
			}
			$('#gameOptions').updateFields();
		});
	});

	var getGmSheetLocation = function (obj) {
		var sheetPropNames = Object.getOwnPropertyNames(obj);
		if (sheetPropNames.length == 1) {
			return obj[sheetPropNames[0]].toLowerCase();
		} else {
			return null;
		}
	}

	$.get('/forums/thread/23143/?pageSize=10000', function (data) {
		var gmSheetSection = $('#customSheets');
		var gmSheetRegex = /{[\s]*[\"\'](.+?)[\"\'][\s]*:[\s]*[\"\'](.+?)\/([0-9]+)[\s]*[\"\']}/gm
		var discoveredLocations = [];
		$('.post', $(data)).each(function () {
			var postText = $(this).text();
			var matchRules = postText.match(gmSheetRegex);
			if (matchRules) {
				for (var i = 0; i < matchRules.length; i++) {
					var gmSheet = matchRules[i];
					if (isValidJson(gmSheet)) {
						var gmSheetObject = JSON.parse(gmSheet);
						var location = getGmSheetLocation(gmSheetObject);
						if (!discoveredLocations.includes(location)) {
							var shortCutDiv = $('<li class="gmSheet"></li>').appendTo(gmSheetSection);
							shortCutDiv.text(Object.getOwnPropertyNames(gmSheetObject)[0]);
							shortCutDiv.data('rulejson', JSON.stringify(gmSheetObject));
							discoveredLocations.push(location);
						}
					}
				}
			}
			$('#gameOptions').updateFields();
		});
	});

	var getCurrentAdr = function () {
		var curJson = $.trim($('#gameOptions').val());
		if (!curJson || isValidJson(curJson)) {
			if (!curJson) {
				curObject = {};
			} else {
				curObject = JSON.parse(curJson);
			}

			$.extend(true, curObject, { background: {}, diceRules: [], characterSheetIntegration: { gmSheets: [] }, diceDefaults: {} });

			if (!curObject.background.image) {
				curObject.background.image = '';
			}

			return curObject;
		}

		return null;
	};

	//helper js
	var areEqualObjects = function (o1, o2) {
		var k1 = Object.getOwnPropertyNames(o1);
		var k2 = Object.getOwnPropertyNames(o2);
		for (var i = 0; i < k1.length; i++) {
			if ((!o2.hasOwnProperty(k1[i])) || (o1[k1[i]] != o2[k1[i]]))
				return false;
		}
		for (var i = 0; i < k2.length; i++) {
			if ((!o1.hasOwnProperty(k2[i])) || (o2[k2[i]] != o1[k2[i]]))
				return false;
		}

		return true;
	};

	var indexOfObject = function (arr, ob) {
		for (var i = 0; i < arr.length; i++) {
			if (areEqualObjects(arr[i], ob)) {
				return i;
			}
		}
		return -1;
	}

	var arrayHasSubset = function (arr, containsArr) {
		for (var i = 0; i < containsArr.length; i++) {
			if (indexOfObject(arr, containsArr[i]) == -1) {
				return false;
			}
		}
		return true;
	}

	$('#adrBackground').on('blur change keyup', function () {
		var curObject = getCurrentAdr();
		if (curObject) {
			curObject.background.image = $('#adrBackground').val();
			setAdrText(curObject);
		}
	});

	$('#diceRules').on('click', '.diceRule', function () {
		var curObject = getCurrentAdr();
		if (curObject) {
			var diceJson = $(this).data('rulejson');
			var selectedDiceRules = JSON.parse(diceJson).diceRules;
			if ($(this).hasClass('jsonRuleSel')) {
				//remove dice rules
				for (var i = 0; i < selectedDiceRules.length; i++) {
					var removeIndex = indexOfObject(curObject.diceRules, selectedDiceRules[i]);
					if (removeIndex != -1) {
						curObject.diceRules.splice(removeIndex, 1);
					}
				}
			} else {
				for (var i = 0; i < selectedDiceRules.length; i++) {
					if (indexOfObject(curObject.diceRules, selectedDiceRules[i]) == -1) {
						curObject.diceRules.push(selectedDiceRules[i]);
					}
				}
			}
			setAdrText(curObject);
			$('#gameOptions').updateFields();
		}
	});

	$('#customSheets').on('click', '.gmSheet', function () {
		var curObject = getCurrentAdr();
		if (curObject) {
			var pThis = $(this);
			var sheetJson = $(this).data('rulejson');
			var jsonObj = JSON.parse(sheetJson);
			if (pThis.hasClass('jsonRuleSel')) {
				var removeLocation = getGmSheetLocation(jsonObj);
				for (var i = 0; i < curObject.characterSheetIntegration.gmSheets.length; i++) {
					if (getGmSheetLocation(curObject.characterSheetIntegration.gmSheets[i]) == removeLocation) {
						curObject.characterSheetIntegration.gmSheets.splice(i, 1);
					}
				}
			}
			else {
				curObject.characterSheetIntegration.gmSheets.push(jsonObj);
			}

			setAdrText(curObject);
			$('#gameOptions').updateFields();
		}
	});

	$('input#gmExcludeNpcs').on('click', function () {
		var curObject = getCurrentAdr();
		if (curObject) {
			curObject.characterSheetIntegration.gmExcludeNpcs = $(this).prop('checked');
			setAdrText(curObject);
			$('#gameOptions').updateFields();
		}
	});

	$('input#gmExcludePcs').on('click', function () {
		var curObject = getCurrentAdr();
		if (curObject) {
			curObject.characterSheetIntegration.gmExcludePcs = $(this).prop('checked');
			setAdrText(curObject);
			$('#gameOptions').updateFields();
		}
	});

	$('input#rerollAcesDefault').on('click', function () {
		var curObject = getCurrentAdr();
		if (curObject) {
			curObject.diceDefaults.rerollAces = $(this).prop('checked');
			setAdrText(curObject);
			$('#gameOptions').updateFields();
		}
	});


	var setAdrText = function (obj) {
		var val = JSON.stringify(obj, null, 2);
		val = val.replace(/{\s*/gms, "{");
		val = val.replace(/\s*}/gms, "}");
		val = val.replace(/([\S^}^\]]),\s*"/gms, '\$1,"');
		val = val.replace(/},"/gms, '},\n"');
		val = val.replace(/],"/gms, '],\n"');
		val = val.replace(/}}/gms, '}\n}');

		$('#gameOptions').val(val).change();
	}


	jQuery.fn.updateFields = function (gameOptionJson) {
		$('#customSheets .gmSheet').removeClass('jsonRuleSel');
		$('#diceRules .diceRule').removeClass('jsonRuleSel');
		$('#adrBackground').val('');
		$('input#gmExcludeNpcs').prop('checked', false);
		$('input#gmExcludePcs').prop('checked', false);

		var curJson = gameOptionJson || $.trim($('#gameOptions').val());
		if (curJson && isValidJson(curJson)) {
			var gameOptions = JSON.parse(curJson);
			if (gameOptions && gameOptions.background && gameOptions.background.image) {
				$('#adrBackground').val(gameOptions.background.image);
			}

			if (gameOptions && gameOptions.diceRules) {
				$('#diceRules .diceRule').each(function () {
					var pThis = $(this);
					var diceRulesArray = JSON.parse(pThis.data('rulejson'));
					if (diceRulesArray && diceRulesArray.diceRules && arrayHasSubset(gameOptions.diceRules, diceRulesArray.diceRules)) {
						pThis.addClass('jsonRuleSel');
					} else {
						pThis.removeClass('jsonRuleSel');
					}
				});
			}

			if (gameOptions && gameOptions.characterSheetIntegration && gameOptions.characterSheetIntegration.gmExcludeNpcs) {
				$('input#gmExcludeNpcs').prop('checked', true);
			}

			if (gameOptions && gameOptions.characterSheetIntegration && gameOptions.characterSheetIntegration.gmExcludePcs) {
				$('input#gmExcludePcs').prop('checked', true);
			}

			if (gameOptions && gameOptions.diceDefaults && gameOptions.diceDefaults.rerollAces) {
				$('input#rerollAcesDefault').prop('checked', true);
			}

			if (gameOptions && gameOptions.characterSheetIntegration && gameOptions.characterSheetIntegration.gmSheets && Array.isArray(gameOptions.characterSheetIntegration.gmSheets)) {
				for (var i = 0; i < gameOptions.characterSheetIntegration.gmSheets.length; i++) {
					var sheetPropName = getGmSheetLocation(gameOptions.characterSheetIntegration.gmSheets[i]);
					if (sheetPropName) {
						$('#customSheets .gmSheet').each(function () {
							var pThis = $(this);
							var communitySheet = JSON.parse(pThis.data('rulejson'));
							if (getGmSheetLocation(communitySheet) == sheetPropName) {
								pThis.addClass('jsonRuleSel');
							}
						});
					}
				}
			}
		}
	};
});
