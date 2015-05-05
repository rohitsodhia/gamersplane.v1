$(function() {
	var nextFocusCount = 1;

	$('#focuses').on('click', '.focus_remove', function (e) {
		e.preventDefault();

		$(this).parent().remove();
		if ($('.focus').size() == 0) $('#addFocus').click();
	}).on('click', '#addFocus', function (e) {
		e.preventDefault();

		nextFocusCount += 1;
		$.post('/characters/ajax/spycraft2/addFocus/', { key: nextFocusCount }, function (data) {
			$newFocus = $(data);
			$newFocus.appendTo('#focusList').prettify().find('.focus_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'focus', characterID: characterID, system: system }).find('input').focus();
		});
	}).on('click', '.focus_notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});

	nextFocusCount = $('#focusList .focus').length;
	$('.focus_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'focus', characterID: characterID, system: system });
});