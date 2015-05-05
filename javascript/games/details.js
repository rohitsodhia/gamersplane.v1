$(function () {
	$('#changeStatus, #withdrawFromGame, .actionLinks a, #newMap, .mapActions a, #newDeck, .deckActions a').colorbox();
	$('#toggleForumVisibility').click(function (e) {
		e.preventDefault();

		$forumVis = $(this);

		$.post('/games/process/toggleForumVisibility/', { gameID: $('#gameID').val() }, function () {
			status = $forumVis.siblings('span').text();
			$forumVis.siblings('span').text(status == 'Public'?'Private':'Public');
			$forumVis.text('[ Make game ' + (status == 'Public'?'Public':'Private') + ' ]');
		});
	});
});