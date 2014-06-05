var characterID = parseInt($('#characterID').val()), system = $('#system').val();
$(function () {
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

	$('#skillName').autocomplete('/characters/ajax/skillSearch/', { search: $(this).val(), characterID: characterID, system: system });
	$('#skills').on('click', '.skill_remove', function (e) {
		var skillID = $(this).parent().attr('id').split('_')[1];
		$.post('/characters/ajax/removeSkill/', { characterID: characterID, system: system, skillID: skillID }, function (data) {
			if (data == 1) { $('#skill_' + skillID).slideUp(function () {
				$(this).remove();
				if ($('.skill').size() == 0) $('<p id=\"noSkills\">This character currently has no skills.</p>').hide().appendTo('#skills .hbdMargined').slideDown();
			}); }
		});
		
		e.preventDefault();
	});

	$('#featName').autocomplete('/characters/ajax/featSearch/', { search: $(this).val(), characterID: characterID, system: system });
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