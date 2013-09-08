$(function() {
	$('#featDescForm').submit(function () {
		$.post(SITEROOT + '/characters/ajax/dnd3/featNotes', { characterID: $('#characterID').val(), featID: $('#featID').val(), notes: $('#notes').val() }, function (data) {
			if (parseInt(data) == 1) parent.$.colorbox.close();
		});
		
		return false;
	});
});