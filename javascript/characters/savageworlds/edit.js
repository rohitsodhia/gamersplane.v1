$(function() {
	var nextSkillCount = 1;

	$('#primaryTraits').on('click', '.remove a', function (e) {
		e.preventDefault();

		$(this).closest('.skill').remove();
	}).on('click', '.skillHeader a', function (e) {
		e.preventDefault();

		var $skills = $(this).parent().siblings('.skills'), trait = $skills.closest('.traitDiv').data('trait');
		nextSkillCount += 1;
		$.post('/characters/ajax/addSkill/', { system: system, key: nextSkillCount }, function (data) {
			$newSkill = $(data);
			$newSkill.html($newSkill.html().replace(/\[trait\]/g, '[' + trait + ']')).appendTo($skills).prettify().find('.skillName').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system }).find('input').focus();
		});
	});

	nextSkillCount = $('.skill').length;
	$('.skillName').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system });
});