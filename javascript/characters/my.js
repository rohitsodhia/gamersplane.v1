$(function () {
	$('#newCharLink').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '400px', innerHeight: '110px' });
	$('.editLabel').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '400px', innerHeight: '110px' });
	$('.delete').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '400px', innerHeight: '110px' });
});