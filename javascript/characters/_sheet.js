$(function () {
	$('.favoriteChar').click(function (e) {
		e.preventDefault();
		$link = $(this);
		characterID = $('#characterID').val();
		$.post('/characters/process/favorite/', { characterID: characterID }, function (data) {
			if (data != 0 && $link.hasClass('off')) {
				$link.removeClass('off').attr('title', 'Unfavorite').attr('alt', 'Unfavorite');
			} else if (data != 0) {
				$link.addClass('off').attr('title', 'Favorite').attr('alt', 'Favorite');
			}
		});
	});

	function toggleNotes(e) {
		e.preventDefault();

		$(this).siblings('.notes').slideToggle();
	}

	if ($('#feats').length) {
		$('#feats').on('click', '.feat_notesLink', toggleNotes);
	}
});