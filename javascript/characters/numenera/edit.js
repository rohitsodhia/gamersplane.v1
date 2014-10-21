$(function() {
	$('#attacks').on('click', '.attack_remove', function (e) {
		e.preventDefault();

		$(this).parent().remove();
		if ($('.attack').size() == 0) $('#addAttack').click();
	}).on('click', '#addAttack', function (e) {
		e.preventDefault();

		$.post('/characters/ajax/addItemized/', { system: system, type: 'attack', key: nextAttackCount }, function (data) {
			$(data).appendTo('#attackList');
			nextAttackCount += 1;
		});
	});

	var nextAttackCount = $('#attackList .attack').length + 1;

	$('#skills').on('click', '.skill_remove', function (e) {
		e.preventDefault();

		$(this).parent().remove();
		if ($('.skill').length == 0) $('#addSkill').click();
	}).on('click', '#addSkill', function (e) {
		e.preventDefault();

		$.post('/characters/ajax/addSkill/', { system: system, key: nextSkillCount }, function (data) {
			$newSkill = $(data);
			$newSkill.appendTo('#skillList').find('.skill_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system, systemOnly: true }).focus();
			nextSkillCount += 1;
		});
	}).on('click', '.skill_prof', function (e) {
		$div = $(this).find('div');
		$input = $(this).find('input');

		if ($div.html() == "&nbsp;") {
			$div.html('T');
			$input.val('T');
		} else if ($div.html() == 'T') {
			$div.html('S');
			$input.val('S');
		} else {
			$div.html("&nbsp;");
			$input.val('');
		}
	});

	var nextSkillCount = $('#skillList .skill').length + 1;
	$('.skill_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system, systemOnly: true });

	$('#specialAbilities').on('click', '.specialAbility_remove', function (e) {
		e.preventDefault();

		$(this).parent().remove();
		if ($('.specialAbility').size() == 0) $('#addSpecialAbility').click();
	}).on('click', '#addSpecialAbility', function (e) {
		e.preventDefault();

		$.post('/characters/ajax/addSpecialAbility/', { system: system, key: nextSpecialAbilityCount }, function (data) {
			$newSpecialAbility = $(data);
			$newSpecialAbility.appendTo('#specialAbilityList').find('.specialAbility_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'specialAbility', characterID: characterID, system: system }).find('input').focus();
			nextSpecialAbilityCount += 1;
		});
	}).on('click', '.specialAbility_notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});

	var nextSpecialAbilityCount = $('#specialAbilityList .specialAbility').length + 1;
	$('.specialAbility_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'specialAbility', characterID: characterID, system: system });
});