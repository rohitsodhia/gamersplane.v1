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

	itemizationFunctions['talents'] = {
		newItem: function ($newItem) {
			console.log($newItem);
			$newItem.appendTo('#talentsList').find('input').placeholder().focus();
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
	$('.name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'talent', characterID: characterID, system: system });});