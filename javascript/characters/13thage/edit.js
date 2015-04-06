function trigger_levelUpdate(oldLevel) {
	$('.addHL').each(function () {
		$(this).text(showSign(parseInt($(this).text()) - Math.floor(oldLevel / 2) + Math.floor(level / 2)));
	});
}

function updateStats() {
	$.each(['ac', 'pd', 'md'], function (key, value) {
		$statRow = $('#' + value + 'Row');
		total = parseInt($statRow.find('.saveStat').text()) + level;
		$statRow.find('input').each(function () {
			total += parseInt($(this).val());
		});
		$statRow.find('.total').text(total);
	});
}

$(function() {
	itemizationFunctions['backgrounds'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#backgroundList').find('input').placeholder().focus();
		},
		init: function ($list) {
			$list.find('input').placeholder();
		}
	}
	setupItemized($('#backgrounds'));
	$('#backgrounds').on('click', '.notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});
	$('.background_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'background', characterID: characterID, system: system });

	itemizationFunctions['classAbilities'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#classAbilityList').find('input').placeholder().focus();
		},
		init: function ($list) {
			$list.find('input').placeholder();
		}
	}
	setupItemized($('#classAbilities'));
	$('#classAbilities').on('click', '.notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});
	$('.classAbility_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'classAbility', characterID: characterID, system: system });

	itemizationFunctions['powers'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#powerList').find('input').placeholder().focus();
		},
		init: function ($list) {
			$list.find('input').placeholder();
		}
	}
	setupItemized($('#powers'));
	$('#powers').on('click', '.notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});
	$('.power_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'power', characterID: characterID, system: system });

	itemizationFunctions['attacks'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#attackList').find('input').placeholder().focus();
		},
		init: function ($list) {
			$list.find('input').placeholder();
		}
	}
	setupItemized($('#attacks'));

	$('.stat').blur(function () {
		$.each({
			'ac': ['dex', 'con', 'wis'],
			'pd': ['str', 'dex', 'con'],
			'md': ['int', 'wis', 'cha']
		}, function (index, stats){
			if (statBonus[stats[1]] > statBonus[stats[0]]) {
				hold = stats[0];
				stats[0] = stats[1];
				stats[1] = hold;
			}
			if (statBonus[stats[2]] > statBonus[stats[1]]) {
				hold = stats[1];
				stats[1] = stats[2];
				stats[2] = hold;
			}
			if (statBonus[stats[1]] > statBonus[stats[0]]) {
				hold = stats[0];
				stats[0] = stats[1];
				stats[1] = hold;
			}
			$('#' + index + 'Stat').text(showSign(statBonus[stats[1]]));
		});
		updateStats();
	});

	$('#saves').on('blur', 'input', updateStats);

	$basicAttacks = $('#basicAttacks');
	basicAttacks_misc = { 'melee': $basicAttacks.find('#ba_melee input').val(), 'ranged': $basicAttacks.find('#ba_ranged input').val() };
	$basicAttacks.on('change', 'input', function () {
		$(this).siblings('.total').text(showSign(parseInt($(this).siblings('.total').text()) - basicAttacks_misc[$(this).data('type')] + parseInt($(this).val())));
		basicAttacks_misc[$(this).data('type')] = parseInt($(this).val());
	});
});