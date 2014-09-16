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
			$total.removeClass('addStat_' + oldStat).html(showSign( - statBonus[oldStat] + statBonus[newStat]));
			totalVal -= statBonus[oldStat];
		}
		if (newStat != 'n/a') {
			$statMod.html(showSign(statBonus[newStat])).addClass('statBonus_' + newStat);
			$total.addClass('addStat_' + newStat);
			totalVal += statBonus[newStat];
		}
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
		}).on('keyup', '.skill_name input', function (e) {
			e.preventDefault();
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
				$newSkill.appendTo('#skillList').prettify().find('.skill_name input').autocomplete('/characters/ajax/skillSearch/', { characterID: characterID, system: system, key: nextSkillCount }).find('input').placeholder().focus();
				nextSkillCount += 1;
			});
		}).on('blur', '.skill input', sumRow);

		nextSkillCount = $('#skillList .skill').length + 1;
		$('.skill').find('.skill_name input').placeholder().autocomplete('/characters/ajax/skillSearch/', { characterID: characterID, system: system });

		addCSSRule('.skill_stat', 'width: ' + $('.skill_total').outerWidth(true) + 'px; text-align: center;');
	}

	if ($('#feats').length) {
		$('#feats').on('click', '.feat_remove', function (e) {
			var featID = $(this).parent().attr('id').split('_')[1];
			$.post('/characters/ajax/removeFeat/', { characterID: characterID, system: system, featID: featID }, function (data) {
				if (parseInt(data) == 1) { $('#feat_' + featID).slideUp(function () {
					$(this).remove();
					if ($('.feat').size() == 0) $('<p id="noFeats">This character currently has no feats/abilities.</p>').hide().appendTo('#feats .hbdMargined').slideDown();
				}); }
			});
			
			e.preventDefault()
		});
		$('#feats').on('click', '.feat_notesLink', function (e) { $(this).colorbox(); });
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