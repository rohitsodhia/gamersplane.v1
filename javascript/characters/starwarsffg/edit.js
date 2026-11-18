$(function() {
	var removeDialog = $('<dialog aria-label="Confirm removal"><form method="dialog"><p></p><button value="cancel" autofocus>Cancel</button> <button value="remove">Remove</button></form></dialog>').appendTo(document.body)[0];

	$('#skills, #talents, #weapons').on('click', '.skill_remove, .talent_remove, .remove', function (e) {
		e.preventDefault();

		var $row = $(this).closest('.skill, .talent, .weapon');
		var $name = $row.find('input[name$="[name]"]');
		var name = $name.hasClass('default') ? '' : ($name.val() || '').trim();
		var type = $row.is('.skill') ? 'skill' : $row.is('.talent') ? 'talent' : 'weapon';
		removeDialog.returnValue = '';
		$(removeDialog).find('p').text(name ? 'Remove the ' + type + ' \u201c' + name + '\u201d?' : 'Remove this ' + type + '?');
		$(removeDialog).off('close').one('close', function () {
			if (this.returnValue !== 'remove') return;

			$row.remove();
			if (type === 'talent' && !$('#talentList .talent').length) $('#addTalent').click();
		});
		removeDialog.showModal();
	});

	if ($('#talents').length) {
		var nextTalentCount = 1;

		$('#talents').on('click', '#addTalent', function (e) {
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
