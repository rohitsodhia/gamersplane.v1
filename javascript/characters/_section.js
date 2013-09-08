$(function () {
	if ($('.hbDark .wing').length) {
		leftMargin = $('.hbDark .wing').css('border-right-width');
		$('.hbMargined:not(textarea)').css({ 'margin-left': leftMargin, 'margin-right': leftMargin });

		leftMargin = leftMargin.slice(0, -2);
		$('textarea.hbMargined').each(function () {
			tWidth = $(this).parent().width();
			$(this).css({ 'margin-left': leftMargin + 'px', 'margin-right': leftMargin + 'px', 'width': (tWidth - 2 * leftMargin) + 'px' });
		});
	}
});