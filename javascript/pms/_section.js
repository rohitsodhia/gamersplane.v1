$(function () {
	$('.deletePM').click(function (e) {
		if ($(this).is('div')) var url = this.parentNode.href;
		else var url = this.href;
		url = url + '?modal=1'
		$.colorbox();

		e.preventDefault();
	});
});