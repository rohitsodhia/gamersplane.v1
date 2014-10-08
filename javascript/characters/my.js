$(function () {
	$('#cboxWrapper').css('top', '14px');

	$('.editBasic, .delete').colorbox();

	$('.libraryToggle').click(function (e) {
		e.preventDefault();
		$link = $(this);
		characterID = $link.parent().parent().attr('id').split('_')[1];
		$.post('/characters/process/libraryToggle/', { characterID: characterID }, function (data) {
			if (data == 1 && $link.hasClass('off')) {
				$link.removeClass('off').attr('title', 'Remove from Library').attr('alt', 'Remove from Library');
			} else if (data == 1) {
				$link.addClass('off').attr('title', 'Add to Library').attr('alt', 'Add to Library');
			}
		});
	});
	$('.unfavorite').click(function (e) {
		e.preventDefault();
		$link = $(this);
		characterID = $link.parent().attr('id').split('_')[1];
		$.post('/characters/process/favorite/', { characterID: characterID }, function (data) {
			$link.toggleClass('off');
		});
	});
});