$(function() {
	$('form').ajaxForm({
		url: SITEROOT + '/games/ajax/gamesSearch',
		type: 'post',
		success: function (data) {
			$('#gamesList').slideUp(function () {
				$(this).html(data).slideDown();
			});
		}
	});

	leftSpacing = $('h1 .wing').css('borderRightWidth');
	$('#gamesList').css('padding', '0 ' + leftSpacing);
});