$(function() {
	$('.spell_notesLink').click(function(e) {
		e.preventDefault();

		$(this).siblings('.spell_notes').slideToggle();
	});
});