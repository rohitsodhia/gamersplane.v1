$(function() {
	itemizationFunctions['skills'] = {
		newItem: function ($newItem, $link) {
			$newItem.find('input').each(function () {
				$(this).attr('name', $(this).attr('name').replace('[replace]', '[' + $link.data('type') + ']'));
			});
			$newItem.appendTo($link.siblings('.subskills')).find('input.name').placeholder().focus();
		},
		init: function ($list) {
			$list.find('input').placeholder();
		}
	}
	setupItemized($('#skills'));
	$('#skills').on('click', '.notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});
	$('.name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system });

	itemizationFunctions['traits'] = {
		newItem: function ($newItem, $link) {
			$newItem.find('input, textarea').each(function () {
				$(this).attr('name', $(this).attr('name').replace('[replace]', '[' + $link.data('type') + ']'));
			});
			$newItem.appendTo($link.closest('.itemList')).find('input').placeholder().focus();
		},
		init: function ($list) {
			$list.find('input').placeholder();
		}
	}
	setupItemized($('#traits'));
	$('#traits').on('click', '.notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});
	$('.name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'trait', characterID: characterID, system: system });});