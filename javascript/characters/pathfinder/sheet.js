$(function() {
	$('.feat_notesLink').click(function () {
		var inline = '#featNotes_' + $(this).parent().attr('id').split('_')[1];
		$.colorbox({ inline: true, href: inline,  height: 250, width: 500 });
		
		return false;
	});
});