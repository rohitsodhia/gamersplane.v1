$(function() {
	leftMargin = $('.hbDark .wing').css('border-right-width');
	$('.gameList').css({ 'marginLeft': leftMargin, 'marginRight': leftMargin });

	$('#lfgEdit').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true });
});