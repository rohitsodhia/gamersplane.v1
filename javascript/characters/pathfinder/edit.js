$(function() {
	function updateAC() {
		var total = 10;
		$('#ac input.acComponents').each(function () {
			total += ($(this).val() != '')?parseInt($(this).val()):0;
		});
		total += parseInt($('#ac .sizeVal').text());
		$('#ac_total').text(total);
	}

	var characterID = parseInt($('#characterID').val());
	var statBonus = { 'str': parseInt($('#strModifier').text()),
						'con': parseInt($('#conModifier').text()),
						'dex': parseInt($('#dexModifier').text()),
						'int': parseInt($('#intModifier').text()),
						'wis': parseInt($('#wisModifier').text()),
						'cha': parseInt($('#chaModifier').text()) }
	var size = parseInt($('#size').val());
	var bab = parseInt($('#bab').val());
	
	$('#size').blur(function() {
		newSize = parseInt($(this).val());
		change = newSize - size;
		$('.sizeVal').text(showSign(newSize));
		$('.nSizeVal').text(showSign(0 - newSize));
		$('.addSize').each(function () { $(this).text(showSign(parseInt($(this).text()) + change)); });
		$('#ac_total').text(parseInt($('#ac_total').text()));
		$('.subSize').each(function () { $(this).text(showSign(parseInt($(this).text()) - change)); });
		size = newSize;
	});
	$('.stat').blur(function() {
		modifier = Math.floor(($(this).val() - 10)/2);
		change = modifier - statBonus[this.id];
		if ($(this).val() == '') { modifier = 0; }
		else if (modifier >= 0) { modifier = '+' + modifier; }
		$('#' + this.id + 'Modifier').text(modifier);
		$('.statBonus_' + this.id).text(modifier);
		$('.addStat_' + this.id).each(function () { $(this).text(showSign(parseInt($(this).text()) + change)); });
		
		statBonus[this.id] = modifier;
	});
	
	$('#savingThrows input').blur(function () { updateSaves($(this).attr('name'), 'pathfinder'); });
	$('#ac input.acComponents').blur(function () { updateAC(); });
	$('#combatBonuses input').blur(updateCombatBonuses);
	$('#bab').blur(function () {
		newBAB = $(this).val();
		change = newBAB - bab;
		$('.bab').text(showSign(newBAB));
		$('.addBAB').each(function () { $(this).text(showSign(parseInt($(this).text()) + change)); });
		bab = newBAB;
	});
	
	$('#skillName').autocomplete('/characters/ajax/skillSearch', { search: $('#skillName').val(), characterID: characterID, system: 'pathfinder' });
	
	function removeSkill () {
		var skillID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/pathfinder/removeSkill', { characterID: $('#characterID').val(), skillID: skillID }, function (data) {
			if (data == 1) { $('#skill_' + skillID).slideUp(function () {
				$(this).remove();
				if ($('.skill').size() == 0) $('<p id="noSkills">This character currently has no skills.</p>').hide().appendTo('#skills .hbdMargined').slideDown();
			}); }
		});
		
		return false;
	}
	
	function updateSkill() {
		var stat = $(this).parent().find('.skill_total').attr('class').match(/addStat_(\w{3})/)[1];
		var total = statBonus[stat];
		$(this).parent().find('.skill_ranks, .skill_misc').each(function () { total += parseInt($(this).val()); });
		$(this).parent().find('.skill_total').text(showSign(total));
	}
	
	$('#addSkill').click(function () {
		if ($('#skillName').val().length >= 3 && $('#skillName').val() != 'Skill Name') {
			$.post(SITEROOT + '/characters/ajax/pathfinder/addSkill', { characterID: characterID, name: $('#skillName').val(), stat: $('#skillStat').val(), statBonus: parseInt($('#' + $('#skillStat').val() + 'Modifier').text()) }, function (data) {
				if ($('#noSkills').size()) $('#noSkills').remove();
				$(data).hide().appendTo('#skills .hbdMargined').slideDown();
				$('#skillName').val('').trigger('blur');

			});
		}
		
		return false;
	});
	$('#skills').on('click', '.skill_remove', removeSkill).on('change', '.skill input', updateSkill);
	
	function removeFeat() {
		var featID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/pathfinder/removeFeat', { characterID: $('#characterID').val(), featID: featID }, function (data) {
			if (parseInt(data) == 1) { $('#feat_' + featID).slideUp(function () {
				$(this).remove();
				if ($('.feat').size() == 0) $('<p id="noFeats">This character currently has no feats/abilities.</p>').hide().appendTo('#feats .hbdMargined').slideDown();
			}); }
		});
		
		return false;
	}
	
	$('#featName').autocomplete('/characters/ajax/featSearch', { search: $(this).val(), characterID: characterID, system: 'pathfinder' });
	
	$('#addFeat').click(function () {
		if ($('#featName').val().length >= 3) {
			$.post(SITEROOT + '/characters/ajax/pathfinder/addFeat', { characterID: $('#characterID').val(), name: $('#featName').val() }, function (data) {
				if ($('#noFeats').size()) $('#noFeats').remove();
				$(data).hide().appendTo('#feats .hbdMargined').slideDown();
				$('#featName').val('').trigger('blur');
			});
		}
		
		return false;
	});
	$('.feat_notesLink').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true });
	$('#feats').on('click', '.feat_remove', removeFeat);
	
	$('#addWeapon').click(function (e) {
		$.post(SITEROOT + '/characters/ajax/pathfinder/weapon', { weaponNum: $('.weapon').size() + 1 }, function (data) { $(data).hide().appendTo('#weapons > div').slideDown(); } );
		
		e.preventDefault()
	});
	
	$('#addArmor').click(function (e) {
		$.post(SITEROOT + '/characters/ajax/pathfinder/armor', { armorNum: $('.armor').size() + 1 }, function (data) { $(data.replace(/armorNum/g, armorNum)).hide().appendTo('#armor > div').slideDown(); } );
		
		e.preventDefault()
	});

	$('#weapons, #armor').on('click', '.remove', function (e) {
		$(this).parent().parent().remove();

		e.preventDefault()
	});
});