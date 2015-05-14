$(function() {
	/* Attacks */
	$('#attacks').on('click', '.attack_remove', function (e) {
		e.preventDefault();

		$(this).parent().remove();
		if ($('.attack').size() == 0) $('#addAttack').click();
	}).on('click', '#addAttack', function (e) {
		e.preventDefault();

		nextAttackCount += 1;
		$.post('/characters/ajax/addItemized/', { system: system, type: 'attack', key: nextAttackCount }, function (data) {
			$(data).appendTo('#attackList');
		});
	});

	var nextAttackCount = $('#attackList .attack').length;

	/* Skills */
	$('#skills').on('click', '.skill_remove', function (e) {
		e.preventDefault();

		$(this).parent().remove();
		if ($('.skill').length == 0) $('#addSkill').click();
	}).on('click', '#addSkill', function (e) {
		e.preventDefault();

		nextSkillCount += 1;
		$.post('/characters/ajax/addSkill/', { system: system, key: nextSkillCount }, function (data) {
			$newSkill = $(data);
			$newSkill.appendTo('#skillList').find('.skill_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system, systemOnly: true }).focus();
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

	var nextSkillCount = $('#skillList .skill').length;
	$('.skill_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system, systemOnly: true });

	/* Special Abilities */
	$('#specialAbilities').on('click', '.specialAbility_remove', function (e) {
		e.preventDefault();

		$(this).parent().remove();
		if ($('.specialAbility').size() == 0) $('#addSpecialAbility').click();
	}).on('click', '#addSpecialAbility', function (e) {
		e.preventDefault();

		nextSpecialAbilityCount += 1;
		$.post('/characters/ajax/addItemized/', { system: system, type: 'specialAbility', key: nextSpecialAbilityCount }, function (data) {
			$newSpecialAbility = $(data);
			$newSpecialAbility.appendTo('#specialAbilityList').find('.specialAbility_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'specialAbility', characterID: characterID, system: system, systemOnly: true }).find('input').focus();
		});
	}).on('click', '.specialAbility_notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});

	var nextSpecialAbilityCount = $('#specialAbilityList .specialAbility').length;
	$('.specialAbility_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'specialAbility', characterID: characterID, system: system, systemOnly: true });

	/* Special Abilities */
	$('#cyphers').on('click', '.cypher_remove', function (e) {
		e.preventDefault();

		$(this).parent().remove();
		if ($('.cypher').size() == 0) $('#addCypher').click();
	}).on('click', '#addCypher', function (e) {
		e.preventDefault();

		nextCypherCount += 1;
		$.post('/characters/ajax/addItemized/', { system: system, type: 'cypher', key: nextCypherCount }, function (data) {
			$newCypher = $(data);
			$newCypher.appendTo('#cypherList').find('.cypher_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'cypher', characterID: characterID, system: system, systemOnly: true }).find('input').focus();
		});
	}).on('click', '.cypher_notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});

	var nextCypherCount = $('#cypherList .cypher').length;
	$('.cypher_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'cypher', characterID: characterID, system: system, systemOnly: true });
});