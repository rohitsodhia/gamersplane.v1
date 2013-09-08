$(function() {
	$('#messageTextArea').markItUp(mySettings);

	leftMargin = $('.headerbar .wing').css('border-right-width');
	$('form').css({ 'marginLeft': leftMargin, 'marginRight': leftMargin });
	
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