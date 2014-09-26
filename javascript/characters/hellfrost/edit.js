$(function() {
	$('#skills').on('click', 'a.addSkill', function (e) {
		e.preventDefault();

		$skills = $('#skills');
		if ($skills.find('.newSkill').length) return false;

		$('<div class="newSkill"><input type="text" class="placeholder" data-placeholder="Skill Name"><a href="" class="sprite check small"></a><a href="" class="sprite cross small"></a></div>').appendTo($skills).find('input').placeholder().parent().find('.cross').click(function (e) {
			e.preventDefault();
			$(this).parent().remove();
		}).parent().find('.check').click(function (e) {
			e.preventDefault();

			$newSkill = $(this).parent();
			$skillName = $newSkill.find('input');
			if ($skillName.val() == $skillName.data('placeholder')) return false;
			$.post('/characters/ajax/addSkill/', { system: system, characterID: characterID, name: $skillName.val() }, function (data) {
				$(data).insertAfter($newSkill).find('select').prettySelect();
				$newSkill.remove();
			});
		}).parent().find('input').autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system });
	}).on('click', '.cross', function (e) {
		e.preventDefault();

		$(this).closest('.skill').remove();
	});
});