$(function () {
	$('#addMusic').colorbox();

	$('ul.hbAttachedList').on('click', '.manageSong button', function (e) {
		e.preventDefault();

		$song = $(this).closest('li');
		songID = $song.data('id');
		action = $(this).attr('class');
		$.post('/tools/ajax/music/manage/', { songID: songID, action: action }, function (data) {
			if (data.length == 0 && action == 'toggleApproval') $song.toggleClass('unapproved').find('.toggleApproval').text($song.find('.toggleApproval').text() == 'Approve'?'Unapprove':'Approve');
			else if (data.length == 0) $song.remove();
		});
	});
});