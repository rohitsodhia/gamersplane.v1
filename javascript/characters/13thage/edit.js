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

	itemizationFunctions['talents'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#talentList').find('input').placeholder().focus();
		},
		init: function ($list) {
			$list.find('input').placeholder();
		}
	}
	setupItemized($('#talents'));
	$('#talents').on('click', '.notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});
	$('.talent_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'talent', characterID: characterID, system: system });
});