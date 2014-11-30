$(function() {
	itemizationFunctions['aspects'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#aspectList').find('input').placeholder().focus();
		},
		init: function ($list) {
			$list.find('input').placeholder();
		}
	}
	setupItemized($('#aspects'));

	itemizationFunctions['stunts'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#stuntsList').find('.name').placeholder().focus();
		},
		init: function ($list) {
			$list.find('.name').placeholder();
		}
	}
	setupItemized($('#stunts'));
	$('#stunts').on('click', '.notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});

	itemizationFunctions['skills'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#skillList').prettify().find('.skillName').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system }).find('input').focus();
		},
		init: function ($list) {
			$list.find('.skillName').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system });
		}
	}
	setupItemized($('#skills'));
});