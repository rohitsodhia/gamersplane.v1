$(function() {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data != 0) {
				parent.$.colorbox.close();
//				parent.document.location.reload();
			}
		}
	});
});