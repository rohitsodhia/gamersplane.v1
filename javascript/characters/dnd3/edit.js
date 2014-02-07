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
						
	$('#size').blur(function() {
		size = parseInt($(this).val());
		$('.sizeVal').text(showSign(size));
		updateAC();
		updateCombatBonuses();
	});
	$('.stat').blur(function() {
		modifier = Math.floor(($(this).val() - 10)/2);
		change = modifier - statBonus[this.id];
		if ($(this).val() == '') modifier = 0;
		else if (modifier >= 0) modifier = '+' + modifier;
		$('#' + this.id + 'Modifier').text(modifier);
		$('.statBonus_' + this.id).text(modifier);
		$('.addStat_' + this.id).each(function () { $(this).text(showSign(parseInt($(this).text()) + change)); });
		
		if (this.id == 'str') { updateCombatBonuses(); }
		else if (this.id == 'dex') { updateSaves('ref'); updateCombatBonuses('dex'); }
		else if (this.id == 'con') { updateSaves('fort'); }
		else if (this.id == 'wis') { updateSaves('will'); }
		
		statBonus[this.id] = modifier;
	});
	
	$('#savingThrows input').blur(function () { updateSaves($(this).attr('name')); });
	$('#ac input.acComponents').blur(function () { updateAC(); });
	$('#combatBonuses input').blur(updateCombatBonuses);
	$('#bab').blur(function () { $('.bab').text(showSign($(this).val())); });
	
/*	$('#skillName').focus(function () {
		if ($(this).val() == 'Skill Name') $(this).val('').css('color', '#FFF');
		if ($('#skillAjaxResults a').size() > 1 && $(this).val() >= 3) $('#skillAjaxResults').slideDown();
	}).blur(function () {
		if ($(this).val() == '') $(this).val('Skill Name').css('color', '#666');
		$('#skillAjaxResults').slideUp();
	}).keyup(function () {
		if ($(this).val().length >= 3 && $(this).val() != 'Skill Name') { $.post('/characters/ajax/skillSearch', { search: $(this).val(), characterID: characterID, system: 'dnd3' }, function (data) {
			if (data.length > 0) {
				$('#skillAjaxResults').html(data).slideDown();
				
				$('#skillAjaxResults a').click(function () {
					$('#skillName').val($(this).text());
					
					return false;
				});
			} else $('#skillAjaxResults').slideUp();
		}); } else $('#skillAjaxResults').slideUp();
	}).keypress(function (event) {
		if (event.which == 13) return false;
	});*/

	$('#skillName').autocomplete('/characters/ajax/skillSearch', { search: $('#skillName').val(), characterID: characterID, system: 'dnd3' });
	
	$('#addSkill').click(function (e) {
		if ($('#skillName').val().length >= 3 && $('#skillName').val() != 'Skill Name') {
			$.post('/characters/ajax/dnd3/addSkill', { characterID: characterID, name: $('#skillName').val(), stat: $('#skillStat').val(), statBonus: parseInt($('#' + $('#skillStat').val() + 'Modifier').text()) }, function (data) {
				if ($('#noSkills').size()) $('#noSkills').remove();
				$(data).hide().appendTo('#skills .hbdMargined').slideDown();
				$('#skillName').val('').trigger('blur');
			});
		}
		
		e.preventDefault()
	});
	$('#skills').on('click', '.skill_remove', function (e) {
		var skillID = $(this).parent().attr('id').split('_')[1];
		$.post('/characters/ajax/dnd3/removeSkill', { characterID: characterID, skillID: skillID }, function (data) {
			if (data == 1) { $('#skill_' + skillID).slideUp(function () {
				$(this).remove();
				if ($('.skill').size() == 0) $('<p id=\"noSkills\">This character currently has no skills.</p>').hide().appendTo('#skills .hbdMargined').slideDown();
			}); }
		});
		
		e.preventDefault()
	}).on('change', '.skill input', function () {
		var stat = $(this).parent().find('.skill_total').attr('class').match(/addStat_(\w{3})/)[1];
		var total = statBonus[stat];
		$(this).parent().find('.skill_ranks, .skill_misc').each(function () { total += parseInt($(this).val()); });
		$(this).parent().find('.skill_total').text(showSign(total));
	});
	
	function removeFeat(e) {
		var featID = $(this).parent().attr('id').split('_')[1];
		$.post('/characters/ajax/dnd3/removeFeat', { characterID: characterID, featID: featID }, function (data) {
			if (parseInt(data) == 1) { $('#feat_' + featID).slideUp(function () {
				$(this).remove();
				if ($('.feat').size() == 0) $('<p id="noFeats">This character currently has no feats/abilities.</p>').hide().appendTo('#feats .hbdMargined').slideDown();
			}); }
		});
		
		e.preventDefault()
	}
	
/*	$('#featName').focus(function () {
		if ($('#featAjaxResults a').size() > 1 && $(this).val() >= 3) $('#featAjaxResults').slideDown();
	}).blur(function () {
		$('#featAjaxResults').slideUp();
	}).keyup(function () {
		if ($(this).val().length >= 3 && $(this).val() != 'Skill Name') { $.post('/characters/ajax/featSearch', { search: $(this).val(), characterID: characterID, system: 'dnd3' }, function (data) {
			if (data.length > 0) {
				$('#featAjaxResults').html(data).slideDown();
				
				$('#featAjaxResults a').click(function () {
					$('#featName').val($(this).text());
					
					return false;
				});
			} else $('#featAjaxResults').slideUp();
		}); } else $('#featAjaxResults').slideUp();
	}).keypress(function (event) {
		if (event.which == 13) return false;
	});*/

	$('#featName').autocomplete('/characters/ajax/featSearch', { search: $(this).val(), characterID: characterID, system: 'dnd3' });
	
	$('#addFeat').click(function (e) {
		if ($('#featName').val().length >= 3) {
			$.post('/characters/ajax/dnd3/addFeat', { characterID: characterID, name: $('#featName').val() }, function (data) {
				if ($('#noFeats').size()) $('#noFeats').remove();
				$(data).hide().appendTo('#feats .hbdMargined').slideDown();
				$('#featName').val('').trigger('blur');
			});
		}
		
		e.preventDefault()
	});
	$('.feat_notesLink').colorbox();
	$('#feats').on('click', '.feat_remove', removeFeat);
	
	$('#addWeapon').click(function (e) {
		$.post('/characters/ajax/dnd3/weapon', { weaponNum: $('.weapon').size() + 1 }, function (data) { $(data).hide().appendTo('#weapons > div').slideDown(); } );
		
		e.preventDefault()
	});
	
	$('#addArmor').click(function (e) {
		$.post('/characters/ajax/dnd3/armor', { armorNum: $('.armor').size() + 1 }, function (data) { $(data).hide().appendTo('#armor > div').slideDown(); } );
		
		e.preventDefault()
	});

	$('#weapons, #armor').on('click', '.remove', function (e) {
		$(this).parent().parent().remove();

		e.preventDefault()
	});
});