$(function() {
	if ($('#talents').length) {
		var nextTalentCount = 1;

		$('#talents').on('click', '.talent_remove', function (e) {
			e.preventDefault();

			$(this).parent().remove();
			if ($('.talent').size() == 0) $('#addTalent').click();
		}).on('click', '#addTalent', function (e) {
			e.preventDefault();

			nextTalentCount += 1;
			$.post('/characters/ajax/starwarsffg/addTalent/', { key: nextTalentCount }, function (data) {
				$newTalent = $(data);
				$newTalent.appendTo('#talentList').find('.talent_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'talent', characterID: characterID, system: system }).find('input').focus();
			});
		}).on('click', '.talent_notesLink', function(e) {
			e.preventDefault();

			$(this).siblings('textarea').slideToggle();
		});

		nextTalentCount = $('#talentList .talent').length;
		$('.talent_name').placeholder().each(function () {
			$(this).autocomplete('/characters/ajax/autocomplete/', { type: 'talent', characterID: characterID, system: system });
		});
	}
});
