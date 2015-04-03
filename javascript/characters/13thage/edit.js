function trigger_levelUpdate(oldLevel) {
	$('.addHL').each(function () {
		$(this).text(showSign(parseInt($(this).text()) - Math.floor(oldLevel / 2) + Math.floor(level / 2)));
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


});