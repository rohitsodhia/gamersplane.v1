$(function () {
	$('#changeStatus').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '390px', innerHeight: '110px' });
	$('.actionLinks a').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '550px', innerHeight: '110px' });

	$('#addGM').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '500px', innerHeight: '110px' });
	$('.removeGM').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '550px', innerHeight: '110px' });
	$('.approveChar').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '550px', innerHeight: '110px' });
	$('.removeChar').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '550px', innerHeight: '110px' });
});