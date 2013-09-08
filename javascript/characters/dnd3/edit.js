$(function() {
	var characterID = parseInt($('#characterID').val());
	var statBonus = { 'str': parseInt($('#strModifier').text()),
						'con': parseInt($('#conModifier').text()),
						'dex': parseInt($('#dexModifier').text()),
						'int': parseInt($('#intModifier').text()),
						'wis': parseInt($('#wisModifier').text()),
						'cha': parseInt($('#chaModifier').text()) }
						
	$('#size').blur(function() {
		size = parseInt($(this).val());
		$('.sizeVal').text(size);
	});
	$('.stat').blur(function() {
		modifier = Math.floor(($(this).val() - 10)/2);
		change = modifier - statBonus[this.id];
		if ($(this).val() == '') { modifier = 0; }
		else if (modifier > 0) { modifier = '+' + modifier; }
		$('#' + this.id + 'Modifier').text(modifier);
		$('.statBonus_' + this.id).text(modifier);
		$('.addStat_' + this.id).each(function () { $(this).text(showSign(parseInt($(this).text()) + change)); });
		
		if (this.id == 'str') { updateCombatBonuses(); }
		else if (this.id == 'dex') { updateSaves('ref', 'dnd3'); updateCombatBonuses('dex'); }
		else if (this.id == 'con') { updateSaves('fort', 'dnd3'); }
		else if (this.id == 'wis') { updateSaves('will', 'dnd3'); }
		
		statBonus[this.id] = modifier;
	});
	
	$('#savingThrows input').blur(function () { updateSaves($(this).attr('name'), 'dnd3'); });
	$('#ac input.acComponents').blur(function () {
		var total = 10;
		$('#ac input.acComponents').each(function () {
			total += ($(this).val() != '')?parseInt($(this).val()):0;
		});
		total += parseInt($('#ac .sizeVal').text());
		$('#ac input.first').val(total);
	});
	$('#combatBonuses input').blur(updateCombatBonuses);
	$('#bab').blur(function () { $('.bab').text(showSign($(this).val())); });
	
	$('#skillName').focus(function () {
		if ($(this).val() == 'Skill Name') $(this).val('').css('color', '#FFF');
		if ($('#skillAjaxResults a').size() > 1 && $(this).val() >= 3) $('#skillAjaxResults').slideDown();
	}).blur(function () {
		if ($(this).val() == '') $(this).val('Skill Name').css('color', '#666');
		$('#skillAjaxResults').slideUp();
	}).keyup(function () {
		if ($(this).val().length >= 3 && $(this).val() != 'Skill Name') { $.post(SITEROOT + '/characters/ajax/skillSearch', { search: $(this).val(), characterID: characterID, system: 'dnd3' }, function (data) {
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
	});
	
	function removeSkill () {
		var skillID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/dnd3/removeSkill', { characterID: $('#characterID').val(), skillID: skillID }, function (data) {
			if (data == 1) { $('#skill_' + skillID).slideUp(function () {
				$(this).remove();
				if ($('.skill').size() == 0) $('<p id=\"noSkills\">This character currently has no skills.</p>').hide().appendTo('#skills').slideDown();
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
		if ($('#skillName').val().length >= 3 && $('#skillName').val() != 'Skill Name') { $.post(SITEROOT + '/characters/ajax/dnd3/addSkill', { characterID: $('#characterID').val(), name: $('#skillName').val(), stat: $('#skillStat').val(), statBonus: parseInt($('#' + $('#skillStat').val() + 'Modifier').text()) }, function (data) {
			if ($('#noSkills').size()) $('#noSkills').remove();
			$(data).change(updateSkill).hide().appendTo('#skills').slideDown().find('.skill_remove').click(removeSkill);
			$('#skillName').val('Skill Name').css('color', '#666');
		}); }
		
		return false;
	});
	$('.skill_remove').click(removeSkill);
	$('.skill input').change(updateSkill);
	
	function removeFeat() {
		var featID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/dnd3/removeFeat', { characterID: $('#characterID').val(), featID: featID }, function (data) {
			if (parseInt(data) == 1) { $('#feat_' + featID).slideUp(function () {
				$(this).remove();
				if ($('.feat').size() == 0) $('<p id=\"noFeats\">This character currently has no feats/abilities.</p>').hide().appendTo('#feats').slideDown();
			}); }
		});
		
		return false;
	}
	$('.feat_remove').click(removeFeat);
	
	$('#featName').focus(function () {
		if ($('#featAjaxResults a').size() > 1 && $(this).val() >= 3) $('#featAjaxResults').slideDown();
	}).blur(function () {
		$('#featAjaxResults').slideUp();
	}).keyup(function () {
		if ($(this).val().length >= 3 && $(this).val() != 'Skill Name') { $.post(SITEROOT + '/characters/ajax/featSearch', { search: $(this).val(), characterID: characterID, system: 'dnd3' }, function (data) {
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
	});
	
	$('#addFeat').click(function () {
		if ($('#featName').val().length >= 3) { $.post(SITEROOT + '/characters/ajax/dnd3/addFeat', { characterID: $('#characterID').val(), name: $('#featName').val() }, function (data) {
			if ($('#noFeats').size()) $('#noFeats').remove();
			$(data).hide().appendTo('#feats').slideDown().find('.feat_remove').click(removeFeat);
			$('#featName').val('');
		}); }
		
		return false;
	});
	$('.feat_notesLink').colorbox({ iframe: true, height: 250, width: 500 });
	
	$('#addWeapon').click(function () {
		var weaponNum = $('.weapon').size() + 1;
		$.get(SITEROOT + '/characters/ajax/dnd3/weapon', function (data) { $(data.replace(/weaponNum/g, weaponNum )).hide().appendTo('#weapons').slideDown(); } );
		
		return false;
	});
	
	$('#addArmor').click(function () {
		var armorNum = $('.armor').size() + 1;
		$.get(SITEROOT + '/characters/ajax/dnd3/armor', function (data) { $(data.replace(/armorNum/g, armorNum)).hide().appendTo('#armor').slideDown(); } );
		
		return false;
	});
});