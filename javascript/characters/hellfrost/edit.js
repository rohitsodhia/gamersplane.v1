$(function() {
	var nextSkillCount = 1;
	$('#skills').on('click', 'a.addSkill', function (e) {
		e.preventDefault();

		if ($('#skills').find('.newSkill').length) return false;

		$.post('/characters/ajax/addSkill/', { system: system, key: nextSkillCount }, function (data) {
			$newSkill = $(data);
			$newSkill.appendTo('#skills').prettify().find('.skillName').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system }).find('input').focus();
			nextSkillCount += 1;
		});
	}).on('click', '.cross', function (e) {
		e.preventDefault();

		$(this).closest('.skill').remove();
	});
	nextSkillCount = $('#skills .skill').length + 1;
});