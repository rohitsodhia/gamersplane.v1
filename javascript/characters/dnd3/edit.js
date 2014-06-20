var statBonus = { 'str' : 0, 'dex' : 0, 'con' : 0, 'int' : 0, 'wis' : 0, 'cha' : 0 };
$(function() {
	function updateAC() {
		var total = 10;
		$('#ac input.acComponents').each(function () {
			total += ($(this).val() != '')?parseInt($(this).val()):0;
		});
		total += parseInt($('#ac .sizeVal').text());
		$('#ac_total').text(total);
	}

	statBonus = { 'str': parseInt($('#strModifier').text()),
				  'con': parseInt($('#conModifier').text()),
				  'dex': parseInt($('#dexModifier').text()),
				  'int': parseInt($('#intModifier').text()),
				  'wis': parseInt($('#wisModifier').text()),
				  'cha': parseInt($('#chaModifier').text()) }
	var size = parseInt($('#size').val());
	var bab = parseInt($('#bab').val());
						
	$('#size').blur(function() {
		oldSize = size;
		size = parseInt($(this).val());
		change = size - oldSize;
		$('.sizeVal').text(showSign(size));
		$('.addSize').text(function () {
			newVal = parseInt($(this).text()) + change;
			console.log(this.id + ':' + newVal);
			if ($(this).hasClass('showSign')) return showSign(newVal);
			else return newVal;
		});
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
		
		statBonus[this.id] = parseInt(modifier);
	});
	
	$('#savingThrows input').blur(function () { updateSaves($(this).data('saveType')); });
	$('#ac input.acComponents').blur(function () { updateAC(); });
	$('#combatBonuses input').blur(updateCombatBonuses);
	$('#bab').blur(function () { $('.bab').text(showSign($(this).val())); });
	
	$('#addSkill').click(function (e) {
		if ($('#skillName').val().length >= 3 && $('#skillName').val() != 'Skill Name') {
			$.post('/characters/ajax/addSkill/', { characterID: characterID, system: system, name: $('#skillName').val(), stat: $('#skillStat').val(), statBonus: parseInt($('#' + $('#skillStat').val() + 'Modifier').text()) }, function (data) {
				if ($('#noSkills').size()) $('#noSkills').remove();
				$(data).hide().appendTo('#skills .hbdMargined').slideDown();
				$('#skillName').val('').trigger('blur');
			});
		}
		
		e.preventDefault()
	});
	$('#skills').on('change', '.skill input', function () {
		var stat = $(this).parent().find('.skill_total').attr('class').match(/addStat_(\w{3})/)[1];
		var total = statBonus[stat];
		$(this).parent().find('.skill_ranks, .skill_misc').each(function () { total += parseInt($(this).val()); });
		$(this).parent().find('.skill_total').text(showSign(total));
	});
	
	$('#addFeat').click(function (e) {
		if ($('#featName').val().length >= 3) {
			$.post('/characters/ajax/addFeat/', { characterID: characterID, system: system, name: $('#featName').val() }, function (data) {
				if ($('#noFeats').size()) $('#noFeats').remove();
				$(data).hide().appendTo('#feats .hbdMargined').slideDown();
				$('#featName').val('').trigger('blur');
			});
		}
		
		e.preventDefault()
	});
});