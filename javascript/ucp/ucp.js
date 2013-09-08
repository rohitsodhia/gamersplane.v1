$(function () {
	leftSpacing = $('.hbDark .dlWing').css('borderRightWidth').slice(0, -2) - 1;

	$('#controls a').click(function (e) {
		oldOpen = $('#controls .current').removeClass('current').attr('class');
		newOpen = $(this).attr('class');
		$(this).addClass('current');

		$('span.' + oldOpen + ', form.' + oldOpen).hide();
		$('span.' + newOpen + ', form.' + newOpen).show();
		wingMargins('form.' + newOpen + ' .fancyButton');
		$('form.' + newOpen + ' .wing').each(setupWings);

		return e.preventDefault();
	})
});