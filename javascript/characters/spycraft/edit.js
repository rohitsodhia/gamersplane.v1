function updateInit() {
	var total = 0;
	$('#init input').each(function () { total += $(this).val().length?parseInt($(this).val()):0; });
	$('#initTotal').text(showSign(total + parseInt($('#strModifier').text())));
}

function updateMelee() {
	var total = 0;
	$('#melee input').each(function () { total += $(this).val().length?parseInt($(this).val()):0; });
	total += parseInt($('#strModifier').text());
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
	var statBonus = { 'str': parseInt($('#strModifier').text()),
						'con': parseInt($('#conModifier').text()),
						'dex': parseInt($('#dexModifier').text()),
						'int': parseInt($('#intModifier').text()),
						'wis': parseInt($('#wisModifier').text()),
						'cha': parseInt($('#chaModifier').text()) }
						
	$('.stat').blur(function() {
		modifier = Math.floor(($(this).val() - 10)/2);
		change = modifier - statBonus[this.id];
		$('#' + this.id + 'Modifier').text(showSign(modifier));
		$('.statBonus_' + this.id).text(showSign(modifier));
		$('.addStat_' + this.id).each(function () { $(this).text(showSign(parseInt($(this).text()) + change)); });
		
		if (this.id == 'str') { updateMelee(); }
		else if (this.id == 'dex') { updateSaves('ref', 'spycraft'); updateRanged(); }
		else if (this.id == 'con') { updateSaves('fort', 'spycraft'); }
		else if (this.id == 'wis') { updateSaves('will', 'spycraft'); }
		
		statBonus[this.id] = modifier;
	});
	
	$('#savingThrows input').blur(function () { updateSaves($(this).attr('name'), 'spycraft'); });
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
		updateMelee();
		updateRanged();
	});
	$('#melee input').blur(function () { updateMelee(); });
	$('#ranged input').blur(function () { updateRanged() });
	
	$('#inspiration_misc').blur(function () { $('#inspiration_total').text(showSign(statBonus['wis'] + parseInt($(this).val()))); });
	$('#education_misc').blur(function () { $('#education_total').text(showSign(statBonus['int'] + parseInt($(this).val()))); });
	
	$('#featName').autocomplete('/characters/ajax/featSearch', { search: $(this).val(), characterID: characterID, system: 'spycraft' });
	$('#addFeat').click(function () {
		if ($('#featName').val().length >= 3) {
			$.post(SITEROOT + '/characters/ajax/spycraft/addFeat', { characterID: $('#characterID').val(), name: $('#featName').val() }, function (data) {
				if ($('#noFeats').size()) $('#noFeats').remove();
				$(data).hide().appendTo('#feats .hbMargined').slideDown();
				$('#featName').val('').trigger('blur');
			});
		}
		
		return false;
	});
	
	$('.feat_notesLink').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerHeight: 100, innerWidth: 500 });
	$('#feats').on('click', '.feat_remove', function () {
		var featID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/spycraft/removeFeat', { characterID: $('#characterID').val(), featID: featID }, function (data) {
			if (parseInt(data) == 1) { $('#feat_' + featID).slideUp(function () {
				$(this).remove();
				if ($('.feat').size() == 0) $('<p id="noFeats">This character currently has no feats/abilities.</p>').hide().appendTo('#feats .hbMargined').slideDown();
			}); }
		});
		
		return false;
	});

	$('#skillName').autocomplete('/characters/ajax/skillSearch', { search: $('#skillName').val(), characterID: characterID, system: 'spycraft' });

	$('#addSkill').click(function () {
		if ($('#skillName').val().length >= 3 && $('#skillName').val() != 'Skill Name') {
			$.post(SITEROOT + '/characters/ajax/spycraft/addSkill', { characterID: characterID, name: $('#skillName').val(), stat: $('#skillStat').val(), statBonus: parseInt($('#' + $('#skillStat').val() + 'Modifier').text()) }, function (data) {
				if ($('#noSkills').size()) $('#noSkills').remove();
				$(data).hide().appendTo('#skills .hbMargined').slideDown();
				$('#skillName').val('').trigger('blur');
			});
		}
		
		return false;
	});
	
	$('#skills').on('click', '.skill_remove', function () {
		var skillID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/spycraft/removeSkill', { characterID: $('#characterID').val(), skillID: skillID }, function (data) {
			if (data == 1) { $('#skill_' + skillID).slideUp(function () {
				$(this).remove();
				if ($('.skill').size() == 0) $('<p id="noSkills">This character currently has no skills.</p>').hide().appendTo('#skills .hbMargined').slideDown();
			}); }
		});
		
		return false;
	}).on('change', '.skill input', function () {
		var stat = $(this).parent().find('.skill_total').attr('class').match(/addStat_(\w{3})/)[1];
		var total = statBonus[stat];
		$(this).parent().find('.skill_ranks, .skill_misc').each(function () { total += parseInt($(this).val()); });
		$(this).parent().find('.skill_total').text(showSign(total));
	});
	
	$('#addWeapon').click(function (e) {
		$.post(SITEROOT + '/characters/ajax/spycraft/weapon', { weaponNum: $('.weapon').size() + 1 }, function (data) { $(data).hide().appendTo('#weapons > div').slideDown(); } );
		
		e.preventDefault()
	});
	
	$('#addArmor').click(function (e) {
		$.post(SITEROOT + '/characters/ajax/spycraft/armor', { armorNum: $('.armor').size() + 1 }, function (data) { $(data).hide().appendTo('#armor > div').slideDown(); } );
		
		e.preventDefault()
	});

	$('#weapons, #armor').on('click', '.remove', function (e) {
		$(this).parent().parent().remove();

		e.preventDefault()
	});
});