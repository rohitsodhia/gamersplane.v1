$(function () {
	if ($('body').hasClass('viewCharacter')) {
		$('#sheetActions').setupWingContainer();
	}

	$('.characterSheet .favoriteChar').click(function (e) {
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
});