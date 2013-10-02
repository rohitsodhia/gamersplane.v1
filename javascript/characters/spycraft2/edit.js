function updateInit() {
	var total = 0;
	$('#init input').each(function () { total += $(this).val().length?parseInt($(this).val()):0; });
	$('#initTotal').text(showSign(total + parseInt($('#strModifier').text())));
}

function updateUnarmed() {
	var total = 0;
	$('#unarmed input').each(function () { total += $(this).val().length?parseInt($(this).val()):0; });
	total += parseInt($('#strModifier').text());
	$('#unarmedTotal').text(showSign(total));
}

function updateMelee() {
	var total = 0;
	$('#melee input').each(function () { total += $(this).val().length?parseInt($(this).val()):0; });
	total += parseInt($('#strModifier').text()) + parseInt($('#bab').val());
	$('#meleeTotal').text(showSign(total));
}

function updateRanged() {
	var total = 0;
	$('#ranged input').each(function () { total += $(this).val().length?parseInt($(this).val()):0; });
	total += parseInt($('#dexModifier').text()) + parseInt($('#bab').val());
	$('#rangedTotal').text(showSign(total));
}

$(function() {
	var characterID = parseInt($('#characterID').val());
	var levels = $('#classes').val().match(/\d+/g);
	var level = 0;
	var oldLevel = 0;
	for (cLevel in levels) level += parseInt(levels[cLevel]);
	$('.level').text(showSign(level));
	var statBonus = { 'str': parseInt($('#strModifier').text()),
						'con': parseInt($('#conModifier').text()),
						'dex': parseInt($('#dexModifier').text()),
						'int': parseInt($('#intModifier').text()),
						'wis': parseInt($('#wisModifier').text()),
						'cha': parseInt($('#chaModifier').text()) }
	
	$('#classes').blur(function() {
		oldLevel = level;
		level = 0;
		var levels = $(this).val().match(/\d+/g);
		for (cLevel in levels) level += parseInt(levels[cLevel]);
		$('.addLevel').each(function () {
			$(this).text(showSign(parseInt($(this).text()) - Math.floor(oldLevel) + Math.floor(level)));
		});
		$('.level').text(showSign(level));
	});
	
	$('.stat').blur(function() {
		modifier = Math.floor(($(this).val() - 10)/2);
		change = modifier - statBonus[this.id];
		$('#' + this.id + 'Modifier').text(showSign(modifier));
		$('.statBonus_' + this.id).text(showSign(modifier));
		$('.addStat_' + this.id).each(function () { $(this).text(showSign(parseInt($(this).text()) + change)); });
		
		if (this.id == 'str') { updateMelee(); }
		else if (this.id == 'dex') { updateSaves('ref', 'spycraft2'); updateRanged(); }
		else if (this.id == 'con') { updateSaves('fort', 'spycraft2'); }
		else if (this.id == 'wis') { updateSaves('will', 'spycraft2'); }
		
		statBonus[this.id] = modifier;
	});
	
	$('#savingThrows input').blur(function () { updateSaves($(this).attr('name'), 'spycraft2'); });
	$('#ac input.acComponents').blur(function () {
		var total = 10;
		$('#ac input.acComponents').each(function () {
			total += parseInt($(this).val())?parseInt($(this).val()):0;
		});
		$('#ac_total').text(total);
	});
	$('#init input').blur(function () { updateInit(); });
	$('#bab').blur(function () {
		$('.bab').text(showSign($(this).val()));
		updateUnarmed();
		updateMelee();
		updateRanged();
	});
	$('#unarmed input').blur(function () { updateMelee(); });
	$('#melee input').blur(function () { updateMelee(); });
	$('#ranged input').blur(function () { updateRanged() });
	
	$('#knowledge_misc').blur(function () { $('#knowledge_total').text(showSign(statBonus['int'] + level + parseInt($(this).val()))); });
	$('#request_misc').blur(function () { $('#request_total').text(showSign(statBonus['cha'] + level + parseInt($(this).val()))); });
	$('#gear_misc').blur(function () { $('#gear_total').text(showSign(statBonus['wis'] + level + parseInt($(this).val()))); });
	
	$('#skillName').autocomplete('/characters/ajax/skillSearch', { search: $('#skillName').val(), characterID: characterID, system: 'spycraft2' });
	
	$('#addSkill').click(function (e) {
		if ($('#skillName').val().length >= 3 && $('#skillName').val() != 'Skill Name' && $('#skillStat_1').val() != $('#skillStat_2').val()) {
			$.post(SITEROOT + '/characters/ajax/spycraft2/addSkill', {
				characterID: characterID,
				name: $('#skillName').val(),
				stat_1: $('#skillStat_1').val(),
				stat_2: $('#skillStat_2').val(),
				statBonus_1: statBonus[$('#skillStat_1').val()],
				statBonus_2: $('#skillStat_2').val() != '' ? statBonus[$('#skillStat_2').val()] : 0
			}, function (data) {
				if ($('#noSkills').size()) $('#noSkills').remove();
				$(data).hide().appendTo('#skills .hbdMargined').slideDown();
				$('#skillName').val('').trigger('blur');
			});
		}
		
		e.preventDefault();
	});
	
	$('#skills').on('click', '.skill_remove', function (e) {
		var skillID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/spycraft2/removeSkill', { characterID: characterID, skillID: skillID }, function (data) {
			if (data == 1) { $('#skill_' + skillID).slideUp(function () {
				$(this).remove();
				if ($('.skill').length == 0) $('<p id="noSkills">This character currently has no skills.</p>').hide().appendTo('#skills .hbdMargined').slideDown();
			}); }
		});
		
		e.preventDefault();
	}).on('change', '.skill input', function () {
		var inputTotal = 0;
		$(this).parent().find('.skill_ranks, .skill_misc').each(function () { inputTotal += parseInt($(this).val()); });
		$(this).parent().find('.skill_total span').each(function () {
			stat = $(this).attr('class').match(/addStat_\w{3}/)[0];
			total = statBonus[stat.substr(8)] + inputTotal;
			$(this).text(showSign(total));
		});
	});
	
	$('#focusName').autocomplete('/characters/ajax/spycraft2/focusSearch', { characterID: characterID });

	$('#addFocus').click(function (e) {
		if ($('#focusName').val().length >= 3) { $.post(SITEROOT + '/characters/ajax/spycraft2/addFocus', { characterID: characterID, name: $('#focusName').val() }, function (data) {
			if ($('#noFocuses').size()) {
				$('#noFocuses').slideUp();
				$('#focuses .labelTR').slideDown();
			}
			$(data).hide().appendTo('#focuses .hbdMargined').slideDown();
			$('#focusName').val('').trigger('blur');
		}); }
		
		e.preventDefault();
	});
	
	$('#focuses').on('click', '.focus_remove', function (e) {
		var focusID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/spycraft2/removeFocus', { characterID: characterID, focusID: focusID }, function (data) {
			if (parseInt(data) == 1) { $('#focus_' + focusID).slideUp(function () {
				$(this).remove();
				if ($('.focus').size() == 0) {
					$('#noFocuses').slideDown();
					$('#focuses .labelTR').slideUp();
				}
			}); }
		});
		
		e.preventDefault();
	});
	
	$('#featName').autocomplete('/characters/ajax/featSearch', { characterID: characterID, system: 'spycraft2' });
	
	$('#addFeat').click(function (e) {
		if ($('#featName').val().length >= 3) { $.post(SITEROOT + '/characters/ajax/spycraft2/addFeat', { characterID: characterID, name: $('#featName').val() }, function (data) {
			if ($('#noFeats').size()) $('#noFeats').slideUp();
			$(data).hide().appendTo('#feats .hbdMargined').slideDown();
			$('#featName').val('').trigger('blur');
		}); }
		
		e.preventDefault();
	});

	$('#feats').on('click', '.feat_notesLink', function (e) {
		curLink = this.href;
		$.colorbox();
		
		e.preventDefault();
	}).on('click', '.feat_remove', function (e) {
		var featID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/spycraft2/removeFeat', { characterID: characterID, featID: featID }, function (data) {
			if (parseInt(data) == 1) { $('#feat_' + featID).slideUp(function () {
				$(this).remove();
				if ($('.feat').size() == 0) $('#noFeats').slideDown();
			}); }
		});
		
		e.preventDefault();
	});
	
	$('#addWeapon').click(function (e) {
		$.post(SITEROOT + '/characters/ajax/spycraft2/weapon', { weaponNum: $('.weapon').size() + 1 }, function (data) { $(data).hide().appendTo('#weapons > div').slideDown(); } );
		
		e.preventDefault()
	});
	
	$('#addArmor').click(function (e) {
		$.post(SITEROOT + '/characters/ajax/spycraft2/armor', { armorNum: $('.armor').size() + 1 }, function (data) { $(data).hide().appendTo('#armor > div').slideDown(); } );
		
		e.preventDefault()
	});

	$('#weapons, #armor').on('click', '.remove', function (e) {
		$(this).parent().parent().remove();

		e.preventDefault()
	});
});