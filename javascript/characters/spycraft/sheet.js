$(function() {
	$('.feat_notesLink').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerHeight: 100, innerWidth: 500 });
});