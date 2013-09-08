$(function() {
	$('#acpMenu a').click(function () {
		if (!$(this).parent().hasClass('current')) {
			$(this).parent().parent().children('.current').removeClass('current');
			$(this).parent().addClass('current');
			$('.acpContent.current').removeClass('current');
			$('#' + $(this).attr('id').split('_')[1]).addClass('current');
		}
		
		return false;
	});
});