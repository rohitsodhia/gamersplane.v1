$(function() {
	$('#spells').on('click', '.spell_remove', function (e) {
		e.preventDefault();

		$(this).parent().remove();
		if ($('.spell').size() == 0) $('#addSpell').click();
	}).on('click', '#addSpell', function (e) {
		e.preventDefault();

		$.post('/characters/ajax/addItemized/', { system: system, type: 'spell', key: nextSpellCount }, function (data) {
			$newSpell = $(data);
			$newSpell.appendTo('#spellList').prettify().find('.spell_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'spell', characterID: characterID, system: system }).find('input').focus();
			nextSpellCount += 1;
		});
	}).on('click', '.spell_notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});

	nextSpellCount = $('#spellList .spell').length + 1;
	$('.spell_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'spell', characterID: characterID, system: system });
});