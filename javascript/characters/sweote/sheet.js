$(function() {
	$('.talent_notesLink').click(function(e) {
		e.preventDefault();

		$(this).siblings('.talent_notes').slideToggle();
	});
});