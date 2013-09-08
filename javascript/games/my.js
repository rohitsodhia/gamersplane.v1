$(function() {
	leftMargin = $('.hbDark .wing').css('border-right-width');
	$('.hbTopper').css({ 'marginLeft': leftMargin });
	$('.gameList').css({ 'marginLeft': leftMargin, 'marginRight': leftMargin });

	$('#lfgEdit').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '690px', innerHeight: '240px' });
});