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

	$('#stress .type a').click(function (e) {
		e.preventDefault();
		$labels = $(this).parent().siblings('.track').children('.labels');
		$checkboxes = $labels.siblings('.checkboxes');
		numBoxes = $labels.find('label').length;
		stressType = $labels.parent().data('type');
		if ($(this).hasClass('add')) {
			if (numBoxes == 8) return false;
			numBoxes++;
			$stressBox = $('<div><input id="stress_' + stressType + '_' + numBoxes + '" type="checkbox" name="stresses[' + stressType + '][' + numBoxes + ']"></div>');
			$labels.append('<label for="stress_' + stressType + '_' + numBoxes + '">' + numBoxes + '</label>')
			$stressBox.appendTo($checkboxes).find('input[type="checkbox"]').prettyCheckbox();
		} else {
			if (numBoxes == 1) return false;
			numBoxes--
			$labels.children('label').last().remove();
			$checkboxes.children('div').last().remove();
		}
		$checkboxes.parent().siblings('input[type="hidden"]').val(numBoxes);
	});
});