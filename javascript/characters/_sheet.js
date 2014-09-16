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

	if ($('#feats').length) {
		$('#feats').on('click', '.feat_notesLink', function (e) {
			e.preventDefault();

			if ($(this).siblings('div.feat_notes').length) $(this).siblings('div.feat_notes').toggleSlide();
		})
	}
});