var characterID = parseInt($('#characterID').val()), system = $('#system').val();
var itemizationFunctions = [], itemizedCount = [];

function setupItemized($list) {
	itemizationFunctions[$list.attr('id')].count = 0;
	$list.on('click', '.remove', function (e) {
		e.preventDefault();

		$(this).parent().remove();
		if ($list.find('.item').length === 0)
			$list.find('.addItem').click();
	}).on('click', 'a.addItem', function (e) {
		e.preventDefault();
		$link = $(this);

		itemizationFunctions[$list.attr('id')].count += 1;
		$.post('/characters/ajax/addItemized/', { system: system, 'type': $list.data('type'), key: 'n' + itemizationFunctions[$list.attr('id')].count }, function (data) {
			$newItem = $(data);
			itemizationFunctions[$list.attr('id')].newItem($newItem, $link);
		});
	});

	itemizationFunctions[$list.attr('id')].init($list);
}

$(function () {
	$('#charAvatar a').colorbox();

	$('#charDetails').on('blur', '.sumRow input', sumRow);

	if ($('#classWrapper')) {
		$('#classWrapper a').click(function (e) {
			e.preventDefault();
			$classSet = $(this).parent().find('.classSet').eq(0).clone();
			$classSet.find('input').val('');
			$classSet.appendTo($(this).parent());
		});
	}

	$('#content form').on('change', '.abilitySelect', function () {
		$abilitySelect = $(this);
		$statMod = $(this).parent().siblings('.abilitySelectMod');
		$total = $('#' + $abilitySelect.data('totalEle'));
		oldStat = $abilitySelect.data('statHold');
		newStat = $abilitySelect.val();
		totalVal = parseInt($total.html());
		if (oldStat != 'n/a') {
			$statMod.removeClass('statBonus_' + oldStat);
			$total.removeClass('addStat_' + oldStat);
			totalVal -= statBonus[oldStat];
		}
		if (newStat != 'n/a') {
			$statMod.html(showSign(statBonus[newStat])).addClass('statBonus_' + newStat);
			$total.addClass('addStat_' + newStat);
			totalVal += statBonus[newStat];
		} else $statMod.html('+0');
		$abilitySelect.data('statHold', newStat);
		$total.html(showSign(totalVal));
	});

	if ($('#skills').length && !$('#skills').hasClass('nonDefault')) {
		itemizationFunctions.skills = {
			newItem: function ($newItem) {
				$newItem.appendTo('#skillList').prettify().find('.abilitySelect').trigger('change').closest('.skill').find('.skill_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system }).find('input').focus();
			},
			init: function ($list) {
				$list.find('input').placeholder();
			}
		};
		setupItemized($('#skills'));
		$('.skill_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system });

		addCSSRule('.skill_stat', 'width: ' + ($('.skill .skill_stat').eq(0).outerWidth(true)) + 'px; text-align: center;');
	}

	if ($('#feats').length) {
		itemizationFunctions.feats = {
			newItem: function ($newItem) {
				$newItem.appendTo('#featList').find('.feat_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'feat', characterID: characterID, system: system }).find('input').focus();
			},
			init: function ($list) {
				$list.find('input').placeholder();
			}
		};
		setupItemized($('#feats'));

		$('#feats').on('click', '.notesLink', function(e) {
			e.preventDefault();

			$(this).siblings('textarea').slideToggle();
		}).find('.feat_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'feat', characterID: characterID, system: system });
	}

	if ($('#addWeapon').length) {
		var weaponCount = $('div.weapon').length;
		$('#addWeapon').click(function (e) {
			e.preventDefault();

			$.post('/characters/ajax/addWeapon/', { system: system, weaponNum: ++weaponCount }, function (data) { $(data).hide().appendTo('#weapons > div').slideDown(); } );
		});
	}
	if ($('#addArmor').length) {
		var armorCount = $('div.armor').length;
		$('#addArmor').click(function (e) {
			e.preventDefault();

			$.post('/characters/ajax/addArmor/', { system: system, armorNum: ++armorCount }, function (data) { $(data).hide().appendTo('#armor > div').slideDown(); } );
		});
	}

	$('#weapons, #armor').on('click', '.remove', function (e) {
		$(this).parent().parent().remove();

		e.preventDefault();
	});

	$('#submitDiv button').click(function (e) {
		$('.placeholder').each(function () {
			if ($(this).val() == $(this).data('placeholder')) $(this).val('');
		});
	});
});

controllers.controller('editCharacter', ['$scope', 'CharactersService', function ($scope, CharactersService) {
	pathElements = getPathElements();
	$scope.loadChar = function () {
		return CharactersService.load(pathElements[2]).then(function (data) {
			$scope.character = data;
			if (typeof blanks != 'undefined')
				CharactersService.loadBlanks($scope.character, blanks);
		});
	};
	$scope.addItem = function (key) {
		keyParts = key.split('.');
		if (keyParts.length == 2)
			$scope.character[keyParts[0]][keyParts[1]].push(copyObject(blanks[key]));
		else
			$scope.character[key].push(copyObject(blanks[key]));
	};
	$scope.toggleNotes = function ($event) {
		$($event.target).siblings('textarea').slideToggle();
	};
	$scope.save = function () {
		CharactersService.save($scope.character.characterID, $scope.character).then(function (data) {
			if (data.saved)
				window.location = '/characters/' + pathElements[1] + '/' + pathElements[2] + '/';
		});
	};

	$('.hasNotesLinks').on('click', '.notesLink', function (e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});
}]).controller('editCharacter_custom', ['$scope', 'CurrentUser', function ($scope, CurrentUser) {
	CurrentUser.load().then(function () {
		$scope.loadChar();
	});
}]);
