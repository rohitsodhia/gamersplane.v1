$(function () {
	$('#changeStatus').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '390px', innerHeight: '115px' });
	$('.actionLinks a').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '550px', innerHeight: '100px' });

	leftSpacing = $('.hbDark .dlWing').css('borderRightWidth');
	$('.playerList, #applyToGame p').css({ 'margin-left': leftSpacing, 'margin-right': leftSpacing });

	$('#addGM').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '500px', innerHeight: '200px' });
	$('.removeGM').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '550px', innerHeight: '100px' });
	$('.approveChar').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '550px', innerHeight: '100px' });
	$('.removeChar').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '550px', innerHeight: '100px' });
});