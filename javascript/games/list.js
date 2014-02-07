$(function() {
	$('form').ajaxForm({
		url: '/games/ajax/gamesSearch',
		type: 'post',
		success: function (data) {
			$('#gamesList').slideUp(function () {
				$(this).html(data).slideDown();
			});
		}
	});
});