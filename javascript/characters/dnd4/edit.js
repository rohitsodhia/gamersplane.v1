$(function() {
	var characterID = parseInt($('#characterID').val());
	var levels = $('#classes').val().match(/\d+/g);
	var level = 0;
	var oldLevel = 0;
	for (cLevel in levels) level += parseInt(levels[cLevel]);
	var statBonus = { 'str': parseInt($('#strModifier').text()),
					  'con': parseInt($('#conModifier').text()),
					  'dex': parseInt($('#dexModifier').text()),
					  'int': parseInt($('#intModifier').text()),
					  'wis': parseInt($('#wisModifier').text()),
					  'cha': parseInt($('#chaModifier').text()) }
	
	function updateSaves(save, level) {
		var total = 0;
		if (save.substring(0, 1) == 'f') {
			save = 'fort';
			total = statBonus['con'] > statBonus['str']?statBonus['con']:statBonus['str'];
		} else if (save.substring(0, 1) == 'r') {
			save = 'ref';
			total = statBonus['dex'] > statBonus['int']?statBonus['dex']:statBonus['int'];
		} else if (save.substring(0, 1) == 'w') {
			save = 'will';
			total = statBonus['wis'] > statBonus['cha']?statBonus['wis']:statBonus['cha'];
		} else if (save.substring(0, 1) == 'a') save = 'ac';
		
		total += Math.floor(level / 2) + 10;
		$('#' + save +'Row input').each(function () { total += $(this).val().length?parseInt($(this).val()):0; });
		$('#' + save + 'Total').text(showSign(total));
	}

	$('#classes').blur(function() {
		oldLevel = level;
		level = 0;
		var levels = $(this).val().match(/\d+/g);
		for (cLevel in levels) level += parseInt(levels[cLevel]);
		$('.addHL').each(function () {
			$(this).text(showSign(parseInt($(this).text()) - Math.floor(oldLevel / 2) + Math.floor(level / 2)));
		});
	});
	
	$('.stat').blur(function() {
		modifier = Math.floor(($(this).val() - 10)/2);
		
		if (this.id == 'dex') $('#init_total').text(showSign(parseInt($('#init_total').text()) - statBonus['dex'] + modifier));
		
		oldBonus = statBonus[this.id];
		statBonus[this.id] = modifier;
		if ($(this).val() == '') modifier = 0;
		$('#' + this.id + 'Modifier').text(showSign(modifier));
		$('.statBonus_' + this.id).text(showSign(modifier));
		$('#' + this.id + 'ModifierPL').text(showSign(modifier + Math.floor(level / 2)));
		
		if (this.id == 'str' || this.id == 'con') {
			updateSaves('fort', level);
			$('#fortStatBonus').text(showSign(parseInt(statBonus['con'] > statBonus['str']?statBonus['con']:statBonus['str'])));
		} else if (this.id == 'dex' || this.id == 'int') {
			updateSaves('ref', level);
			$('#refStatBonus').text(showSign(parseInt(statBonus['dex'] > statBonus['int']?statBonus['dex']:statBonus['int'])));
		} else/* if (this.id == 'wis' || this.id == 'cha')*/ {
			updateSaves('will', level);
			$('#willStatBonus').text(showSign(parseInt(statBonus['wis'] > statBonus['cha']?statBonus['wis']:statBonus['cha'])));
		}
		
		$('.addStat_' + this.id).each(function () {
			$(this).text(showSign(parseInt($(this).text()) + modifier - oldBonus));
		});
	});
	
	$('#hpInput').blur(function () {
		var hp = $(this).val().length?parseInt($(this).val()):0;
		$('#bloodiedVal').text(Math.floor(hp / 2));
		$('#surgeVal').text(Math.floor(hp / 4));
	});
	$('#saves input').blur(function () { updateSaves($(this).attr('name'), level); });
	$('#init_misc').blur(function () { $('#init_total').text(showSign(statBonus['dex'] + Math.floor(level / 2) + $(this).val().length?parseInt($(this).val()):0)); });
	$('#movement input, #passiveSenses input, #combatBonuses input').blur(function () {
		var total = 0;
		$(this).parent().find('input').each(function () { total += $(this).val().length?parseInt($(this).val()):0; });
		if ($(this).parent().parent().attr('id') == 'passiveSenses') {
			total += 10;
			$(this).parent().find('.total').text(total);
		} else if ($(this).parent().parent().attr('id') == 'movement') { $(this).parent().find('.total').text(total); }
		else {
			if ($(this).parent().parent().attr('class') == 'attackBonusSet') total += Math.floor(level / 2);
			$(this).parent().find('.total').text(showSign(total));
		}
	});
	
	$('#addAttack').click(function (e) {
		$.post(SITEROOT + '/characters/ajax/dnd4/addAttack', { count: $('.attackBonusSet').size() + 1 }, function (data) {
			$(data).hide().appendTo('#combatBonuses').slideDown();
		});
		
		e.preventDefault();
	});
	
/*	$('#skillName').focus(function () {
		if ($(this).val() == 'Skill Name') $(this).val('').css('color', '#FFF');
		if ($('#skillAjaxResults a').size() > 1 && $(this).val() >= 3) $('#skillAjaxResults').slideDown();
	}).blur(function () {
		if ($(this).val() == '') $(this).val('Skill Name').css('color', '#666');
		$('#skillAjaxResults').slideUp();
	}).keyup(function () {
		if ($(this).val().length >= 3 && $(this).val() != 'Skill Name') { $.post(SITEROOT + '/characters/ajax/skillSearch', { search: $(this).val(), characterID: characterID, system: 'dnd4' }, function (data) {
			if (data.length > 0) {
				$('#skillAjaxResults').html(data).slideDown();
				
				$('#skillAjaxResults a').click(function (e) {
					$('#skillName').val($(this).text());
					
					e.preventDefault();
				});
			} else $('#skillAjaxResults').slideUp();
		}); } else $('#skillAjaxResults').slideUp();
	}).keypress(function (event) {
		if (event.which == 13) return false;
	});*/
	
	$('#skillName').autocomplete('/characters/ajax/skillSearch', { search: $('#skillName').val(), characterID: characterID, system: 'dnd4' });
	
	function removeSkill (e) {
		var skillID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/dnd4/removeSkill', { characterID: characterID, skillID: skillID }, function (data) {
			if (data == 1) { $('#skill_' + skillID).slideUp(function () {
				$(this).remove();
				if ($('.skill').size() == 0) $('<p id=\"noSkills\">This character currently has no skills.</p>').hide().appendTo('#skills .hbdMargined').slideDown();
			}); }
		});
		
		e.preventDefault();
	}
	
	function updateSkill() {
		var stat = $(this).parent().find('.skill_total').attr('class').match(/addStat_(\w{3})/)[1];
		var total = statBonus[stat];
		$(this).parent().find('.skill_ranks, .skill_misc').each(function () { total += parseInt($(this).val()); });
		$(this).parent().find('.skill_total').text(showSign(total));
	}
	
	$('#addSkill').click(function (e) {
		if ($('#skillName').val().length >= 3 && $('#skillName').val() != 'Skill Name') {
			$.post(SITEROOT + '/characters/ajax/dnd4/addSkill', { characterID: characterID, name: $('#skillName').val(), stat: $('#skillStat').val(), statBonus: parseInt($('#' + $('#skillStat').val() + 'Modifier').text()) }, function (data) {
				if ($('#noSkills').size()) $('#noSkills').remove();
				$(data).hide().appendTo('#skills .hbdMargined').slideDown();
				$('#skillName').val('').trigger('blur');
			});
		}
		
		e.preventDefault();
	});
	$('#skills').on('click', '.skill_remove', removeSkill).on('change', '.skill input', updateSkill);
	
	function removeFeat(e) {
		var featID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/dnd4/removeFeat', { characterID: characterID, featID: featID }, function (data) {
			if (parseInt(data) == 1) { $('#feat_' + featID).slideUp(function () {
				$(this).remove();
				if ($('.feat').size() == 0) $('<p id=\"noFeats\">This character currently has no feats/features.</p>').hide().appendTo('#feats .hbdMargined').slideDown();
			}); }
		});
		
		e.preventDefault();
	}
	
/*	$('#featName').focus(function () {
		if ($('#featAjaxResults a').size() > 1 && $(this).val() >= 3) $('#featAjaxResults').slideDown();
	}).blur(function () {
		$('#featAjaxResults').slideUp();
	}).keyup(function () {
		if ($(this).val().length >= 3 && $(this).val() != 'Skill Name') { $.post(SITEROOT + '/characters/ajax/featSearch', { search: $(this).val(), characterID: characterID, system: 'dnd4' }, function (data) {
			if (data.length > 0) {
				$('#featAjaxResults').html(data).slideDown();
				
				$('#featAjaxResults a').click(function (e) {
					$('#featName').val($(this).text());
					
					e.preventDefault();
				});
			} else $('#featAjaxResults').slideUp();
		}); } else $('#featAjaxResults').slideUp();
	}).keypress(function (event) {
		if (event.which == 13) return false;
	});*/

	$('#featName').autocomplete('/characters/ajax/featSearch', { search: $(this).val(), characterID: characterID, system: 'dnd4' });
	
	$('#addFeat').click(function (e) {
		if ($('#featName').val().length >= 3) { $.post(SITEROOT + '/characters/ajax/dnd4/addFeat', { characterID: characterID, name: $('#featName').val() }, function (data) {
			if ($('#noFeats').size()) $('#noFeats').remove();
			$(data).hide().appendTo('#feats .hbdMargined').slideDown();
			$('#featName').val('').trigger('blur');
		}); }
		
		e.preventDefault();
	});
	$('.feat_notesLink').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerHeight: 100, innerWidth: 500 });
	$('#feats').on('click', '.feat_remove', removeFeat);
	
/*	$('#powerName').focus(function () {
		if ($(this).val() == 'Power') $(this).val('').css('color', '#FFF');
		if ($('#powerAjaxResults a').size() > 1 && $(this).val() >= 3) $('#powerAjaxResults').slideDown();
	}).blur(function () {
		if ($(this).val() == '') $(this).val('Power').css('color', '#666');
		$('#powerAjaxResults').slideUp();
	}).keyup(function () {
		if ($(this).val().length >= 3 && $(this).val() != 'Power') { $.post(SITEROOT + '/characters/ajax/dnd4/powerSearch', { search: $(this).val(), characterID: characterID }, function (data) {
			if (data.length > 0) {
				$('#powerAjaxResults').html(data).slideDown();
				
				$('#powerAjaxResults a').click(function (e) {
					$('#powerName').val($(this).text());
					
					e.preventDefault();
				});
			} else $('#powerAjaxResults').slideUp();
		}); } else $('#powerAjaxResults').slideUp();
	}).keypress(function (event) {
		if (event.which == 13) return false;
	});*/
	
	$('#powerName').autocomplete('/characters/ajax/dnd4/powerSearch', { search: $(this).val(), characterID: characterID });

	function removePower(e) {
		var power = $(this).val();
		$.post(SITEROOT + '/characters/ajax/dnd4/removePower', { characterID: characterID, power: power }, function (data) {
			if (data == 1) { $('#power_' + power.replace(' ', '_')).slideUp(function () {
				$(this).remove();
			}); }
		});
		
		e.preventDefault();
	}
	
	$('#addPower').click(function (e) {
		var type = $('#powerType').val();
		if ($('#powerName').val().length >= 3 && $('#powerName').val() != 'Power') {
			$.post(SITEROOT + '/characters/ajax/dnd4/addPower', { characterID: characterID, name: $('#powerName').val(), type: type }, function (data) {
				var appendDiv = '';
				if (type == 'a') appendDiv = 'atwill';
				else if (type == 'e') appendDiv = 'encounter';
				else if (type == 'd') appendDiv = 'daily';
				$(data).hide().appendTo('#powers_' + appendDiv).slideDown();
				$('#powerName').val('').trigger('blur');
			});
		}
		
		e.preventDefault();
	});
	$('#powers').on('click', '.power_remove', removePower).on('click', '.power_remove', removePower);
});