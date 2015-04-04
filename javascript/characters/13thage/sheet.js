$(function() {
	$('.notesLink').click(function(e) {
		e.preventDefault();

		$(this).siblings('.notes').slideToggle();
	});
});