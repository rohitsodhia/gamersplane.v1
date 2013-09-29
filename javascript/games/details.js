$(function () {
	$('#changeStatus, .actionLinks a').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true });
});