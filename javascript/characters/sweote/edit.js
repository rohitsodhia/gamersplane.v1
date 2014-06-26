$(function() {
	$('#talentName').autocomplete('/characters/ajax/sweote/talentSearch', { search: $(this).val(), characterID: characterID, system: 'sweote' });
	
	$('#addTalent').click(function (e) {
		if ($('#talentName').val().length >= 3) {
			$.post('/characters/ajax/sweote/addTalent', { characterID: characterID, name: $('#talentName').val() }, function (data) {
				if ($('#noTalents').size()) $('#noTalents').remove();
				$(data).hide().appendTo('#talents .hbdMargined').slideDown();
				$('#talentName').val('').trigger('blur');
			});
		}
		
		e.preventDefault()
	});
	$('.talent_notesLink').colorbox();
	$('#talents').on('click', '.talent_remove', function (e) {
		var talentID = $(this).parent().attr('id').split('_')[1];
		$.post('/characters/ajax/sweote/removeTalent', { characterID: characterID, talentID: talentID }, function (data) {
			if (parseInt(data) == 1) { $('#talent_' + talentID).slideUp(function () {
				$(this).remove();
				if ($('.talent').size() == 0) $('<p id="noTalents">This character currently has no talents.</p>').hide().appendTo('#talents .hbdMargined').slideDown();
			}); }
		});
		
		e.preventDefault()
	});
});