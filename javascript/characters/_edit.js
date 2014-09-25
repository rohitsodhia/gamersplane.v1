var characterID = parseInt($('#characterID').val()), system = $('#system').val();
$(function () {
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

	if ($('#skills').length) {
		var nextSkillCount = 1;

		$('#skills').on('click', '.skill_remove', function (e) {
			e.preventDefault();

			$(this).parent().remove();
			if ($('.skill').size() == 0) $('#addSkill').click();
		}).on('click', '.edit', function (e) {
			e.preventDefault();

			$skill_name = $(this).parent().children('.skill_name');
			$skill_name.find('input').val($skill_name.children('span').text()).trigger('change');
			$(this).parent().addClass('editing');
		}).on('keypress', '.skill_name input', function (e) {
			$input = $(this), $wrapper = $input.closest('.skill_name'), $span = $wrapper.children('span');

			if (e.which == 13 && $input.val() != '') {
				$span.text($input.val());
				$wrapper.parent().removeClass('editing');
			} else if (e.which == 27 && $span.text() != '') {
				$input.val($span.text());
				$wrapper.parent().removeClass('editing');
			}
		}).on('click', '#addSkill', function (e) {
			e.preventDefault();

			$.post('/characters/ajax/addSkill/', { system: system, key: nextSkillCount }, function (data) {
				$newSkill = $(data);
				$newSkill.addClass('editing').appendTo('#skillList').prettify().find('.abilitySelect').trigger('change').closest('.skill').find('.skill_name input').autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system }).find('input').placeholder().focus();
				nextSkillCount += 1;
			});
		}).on('blur', '.skill input', sumRow);

		if ($('.skill').length == 1 && $('.skill input').val() == $('.skill input').data('placeholder')) {
			$('.skill').addClass('editing');
		}

		nextSkillCount = $('#skillList .skill').length + 1;
		$('.skill').find('.skill_name input').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system });

		addCSSRule('.skill_stat', 'width: ' + ($('.skill .skill_stat').eq(0).outerWidth(true)) + 'px; text-align: center;');
	}

	if ($('#feats').length) {
		var nextFeatCount = 1;

		$('#feats').on('click', '.feat_remove', function (e) {
			e.preventDefault();

			$(this).parent().remove();
			if ($('.feat').size() == 0) $('#addFeat').click();
		}).on('click', '.edit', function (e) {
			e.preventDefault();

			$feat_name = $(this).parent().children('.feat_name');
			$feat_name.find('input').val($feat_name.children('span').text()).trigger('change');
			$(this).parent().addClass('editing');
		}).on('keypress', '.feat_name input', function (e) {
			$input = $(this), $wrapper = $input.closest('.feat_name'), $span = $wrapper.children('span');

			if (e.which == 13 && $input.val() != '') {
				$span.text($input.val());
				$wrapper.parent().removeClass('editing');
			} else if (e.which == 27 && $span.text() != '') {
				$input.val($span.text());
				$wrapper.parent().removeClass('editing');
			}
		}).on('click', '#addFeat', function (e) {
			e.preventDefault();

			$.post('/characters/ajax/addFeat/', { system: system, key: nextFeatCount }, function (data) {
				$newFeat = $(data);
				$newFeat.addClass('editing').appendTo('#featList').find('.feat_name input').autocomplete('/characters/ajax/autocomplete/', { type: 'feat', characterID: characterID, system: system }).find('input').placeholder().focus();
				nextFeatCount += 1;
			});
		}).on('click', '.feat_notesLink', function(e) {
			e.preventDefault();

			$(this).siblings('textarea').slideToggle();
		});

		if ($('.feat').length == 1 && $('.feat input').val() == $('.feat input').data('placeholder')) $('.feat').addClass('editing');

		nextFeatCount = $('#featList .feat').length + 1;
		$('.feat').find('.feat_name input').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'feat', characterID: characterID, system: system });
		
		$('#skills, #feats').on('click', '.autocompleteWrapper a', function (e) {
			hitEnter = $.Event('keypress');
			hitEnter.which = 13;
			$(this).closest('.autocompleteWrapper').children('input').trigger(hitEnter);
		});
	}

	if ($('#addWeapon').length) { $('#addWeapon').click(function (e) {
		e.preventDefault();

		$.post('/characters/ajax/addWeapon/', { system: system, weaponNum: $('.weapon').size() + 1 }, function (data) { $(data).hide().appendTo('#weapons > div').slideDown(); } );
	}); }
	if ($('#addArmor').length) { $('#addArmor').click(function (e) {
		e.preventDefault();

		$.post('/characters/ajax/addArmor/', { system: system, armorNum: $('.armor').size() + 1 }, function (data) { $(data).hide().appendTo('#armor > div').slideDown(); } );
	}); }

	$('#weapons, #armor').on('click', '.remove', function (e) {
		$(this).parent().parent().remove();

		e.preventDefault()
	});

	$('#submitDiv button').click(function (e) {
		$('.placeholder').each(function () {
			if ($(this).val() == $(this).data('placeholder')) $(this).val('');
		});
	})
});