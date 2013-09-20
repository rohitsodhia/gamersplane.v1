$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data == 'Removed' || data == 'Left') {
				parent.document.location.reload();
			}
		}
	});
});