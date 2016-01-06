$(function() {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data == '1') {
				parent.window.location.reload();
			}
		}
	});
});