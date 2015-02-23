$(function () {
	leftSpacing = parseInt($('.hbDark .dlWing').css('borderRightWidth').slice(0, -2));
	$('h2, #newThread > a').css('marginLeft', leftSpacing + 'px');

	$('#forumSub').click(function (e) {
		e.preventDefault();

		$link = $(this);
		$.get($(this).attr('href'), {}, function (data) {
			if ($link.text().substring(0, 3) == 'Uns') 
				$link.text('Subscribe to ' + $link.text().split(' ')[2]);
			else 
				$link.text('Unsubscribe from ' + $link.text().split(' ')[2]);
		});
	});
});