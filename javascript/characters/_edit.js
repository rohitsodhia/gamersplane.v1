var characterID = parseInt($('#characterID').val()), system = $('#system').val(), nextSkillCount = 1;
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

	$('.abilitySelect').change(function () {
		$statMod = $(this).parent().siblings('.abilitySelectMod');
		$total = $('#' + $statMod.data('totalEle'));
		oldStat = $statMod.data('statHold');
		newStat = $(this).val();
		$statMod.html(showSign(statBonus[newStat])).removeClass('statBonus_' + oldStat).addClass('statBonus_' + newStat).data('statHold', newStat);
		$total.removeClass('addStat_' + oldStat).addClass('addStat_' + newStat).html(showSign(parseInt($total.html()) - statBonus[oldStat] + statBonus[newStat]));
	});

	$('.skill_name input').autocomplete('/characters/ajax/skillSearch/', { characterID: characterID, system: system });
	$('#skills').on('click', '.skill_remove', function (e) {
		e.preventDefault();

		var skillID = $(this).parent().attr('id').split('_')[1];
		$.post('/characters/ajax/removeSkill/', { characterID: characterID, system: system, skillID: skillID }, function (data) {
			if (data == 1) { $('#skill_' + skillID).slideUp(function () {
				$(this).remove();
				if ($('.skill').size() == 0) $('<p id=\"noSkills\">This character currently has no skills.</p>').hide().appendTo('#skills .hbdMargined').slideDown();
			}); }
		});
	}).on('click', '.edit', function (e) {
		e.preventDefault();

		$skill_name = $(this).parent().children('.skill_name');
		$skill_name.find('input').val($skill_name.children('span').text()).trigger('change');
		$(this).parent().addClass('editing');
	}).on('keyup', '.skill_name input', function (e) {
		e.preventDefault();
		$wrapper = $(this).parent();

		if (e.which == 13) {
			$wrapper.parent().children('span').text($wrapper.val());
			$wrapper.closest('.skill').removeClass('editing');
		} else if (e.which == 27) {
			$wrapper.val($wrapper.parent().children('span').text());
			$wrapper.closest('.skill').removeClass('editing');
		}
	}).on('click', '#addSkill', function (e) {
		e.preventDefault();

		$.post('/characters/ajax/addSkill/', { system: system, key: nextSkillCount }, function (data) {
			$newSkill = $(data);
			$newSkill.appendTo('#skillList').prettify().find('.skill_name input').autocomplete('/characters/ajax/skillSearch/', { characterID: characterID, system: system }).find('input').placeholder().focus();
			nextSkillCount += 1;
		});
	});
	nextSkillCount = $('#skillList .skill').length;
	$('.skill').find('.skill_name input').placeholder().autocomplete('/characters/ajax/skillSearch/', { characterID: characterID, system: system });

	addCSSRule('.skill_stat', 'width: ' + $('.skill_total').outerWidth(true) + 'px');

	$('#featName').autocomplete('/characters/ajax/featSearch/', { characterID: characterID, system: system });
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
});