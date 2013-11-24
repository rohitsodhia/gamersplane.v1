$(function () {
	leftSpacing = parseInt($('.hbDark .dlWing').css('borderRightWidth').slice(0, -2));
	$('h2, #newThread > a').css('marginLeft', leftSpacing + 'px');
});