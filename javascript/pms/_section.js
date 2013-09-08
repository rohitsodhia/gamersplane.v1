$(function () {
	$('.deletePM').click(function (e) {
		if ($(this).is('div')) var url = this.parentNode.href;
		else var url = this.href;
		url = url + '?modal=1'
		$.colorbox({ href: url, iframe: true, innerWidth: '420px', innerHeight: '140px' });

		e.preventDefault();
	});
});