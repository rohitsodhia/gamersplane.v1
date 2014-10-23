function equalizeHeights() {
	$('#gamesList li').each(function () {
		var maxHeight = 0;
		var allSame = true;
		$(this).children().each(function () {
			if ($(this).height() > maxHeight) {
				if (maxHeight != 0) allSame = false;
				maxHeight = $(this).height();
			}
		});
		if (allSame) $(this).children().height(maxHeight);
	});
}

$(function() {
	$('form').ajaxForm({
		url: '/games/ajax/gamesSearch',
		type: 'post',
		success: function (data) {
			$('#gamesList').slideUp(function () {
				$(this).html(data).slideDown(function () {
					equalizeHeights();
				});
			});
		}
	});

	equalizeHeights();
});