$(function () {
	$('.deletePM').click(function (e) {
		if ($(this).is('div')) var url = this.parentNode.href;
		else var url = this.href;
		url = url + '?modal=1'
		$.colorbox({ href: function () { return this.href + '?modal=1', iframe: true });

		e.preventDefault();
	});
});