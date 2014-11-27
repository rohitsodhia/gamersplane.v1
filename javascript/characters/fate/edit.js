$(function() {
/*	var nextSkillCount = 1;

	$('#primaryTraits').on('click', '.remove a', function (e) {
		e.preventDefault();

		$(this).closest('.skill').remove();
	}).on('click', '.skillHeader a', function (e) {
		e.preventDefault();

		var $skills = $(this).parent().siblings('.skills'), trait = $skills.closest('.traitDiv').data('trait');
		$.post('/characters/ajax/addSkill/', { system: system, key: nextSkillCount }, function (data) {
			$newSkill = $(data);
			$newSkill.html($newSkill.html().replace(/\[trait\]/g, '[' + trait + ']')).appendTo($skills).prettify().find('.skillName').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system }).find('input').focus();
			nextSkillCount += 1;
		});
	});

	nextSkillCount = $('.skill').length + 1;
	$('.skillName').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system });*/

	itemizationFunctions['aspects'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#aspectList').find('input').placeholder().focus();
		},
		init: function ($list) {
			$list.find('input').placeholder();
		}
	}
	setupItemized($('#aspects'));

	itemizationFunctions['skills'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#skillList').prettify().find('.name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system }).find('input').focus();
		},
		init: function ($list) {
			$list.find('.name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system });
		}
	}
	setupItemized($('#skills'));

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

	$('#stress h3 a').click(function (e) {
		e.preventDefault();
		$track = $(this).parent().siblings('.track');
		numBoxes = $track.find('.stressBox').length;
		if ($(this).hasClass('add')) {
			if (numBoxes >= 5) return false;
			$stressBox = $('<div class="stressBox"><input type="radio" name="stress[physical]"> <span></span></div>');
			$stressBox.find('input').val(numBoxes).siblings('span').text(numBoxes);
			$stressBox.appendTo($track).find('input[type="radio"]').prettyRadio();
			numBoxes++;
		} else {
			$track.find('.stressBox').last().remove();
			numBoxes--
		}
		$track.find('input[type="hidden"]').val(numBoxes - 1);
	});
});