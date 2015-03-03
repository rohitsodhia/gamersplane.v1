app = angular.module('gamersplane', []);
app.controller('pmList', function ($scope) {
	console.log(1);
});

$(function () {
	$('.deletePM').click(function (e) {
		if ($(this).is('div')) var url = $(this).parent().attr('href');
		else var url = this.href;
		url = url + '?modal=1'
		$.colorbox({href: url});

		e.preventDefault();
	});
});