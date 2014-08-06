$(function() {
	$('a.addSkill').click(function (e) {
		e.preventDefault();

		$skills = $(this).parent().siblings('.skills');
		if ($skills.find('.newSkill').length) return false;

		$('<div class="newSkill"><input type="text" class="placeholder" data-placeholder="Skill Name"><a href="" class="sprite check small"></a><a href="" class="sprite cross small"></a></div>').appendTo($skills).find('input').each(setupPlaceholders).parent().find('.cross').click(function (e) {
			e.preventDefault();
			$(this).parent().remove();
		}).parent().find('.check').click(function (e) {
			e.preventDefault();

			$newSkill = $(this).parent();
			$skillName = $newSkill.find('input');
			if ($skillName.val() == $skillName.data('placeholder')) return false;
			$.post('/characters/ajax/addSkill/', { system: system, characterID: characterID, name: $skillName.val(), stat: $(this).closest('.statDiv').data('stat') }, function (data) {
				$(data).insertAfter($newSkill).find('select').prettySelect();
				$newSkill.remove();
			});
		}).parent().find('input').autocomplete('/characters/ajax/skillSearch/', { search: $(this).val(), characterID: characterID, system: system });
	});

	$('.skills').on('click', '.cross', function (e) {
		e.preventDefault();

		$skillDiv = $(this).closest('.skill');
		$.post('/characters/ajax/removeSkill/', { characterID: characterID, system: system, skillID: $(this).closest('.skill').attr('id').split('_')[1] }, function (data) {
			$skillDiv.remove();
		});
	});
});