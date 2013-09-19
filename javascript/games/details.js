$(function () {
	$('#changeStatus').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '450px', innerHeight: '110px' });
	$('.actionLinks a').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '550px', innerHeight: '110px' });
});