$(function () {
	$('#newCharLink, .editLabel, .delete').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true });
});