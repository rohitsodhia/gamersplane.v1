$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data == 'refresh') parent.window.location.reload();
			else if (data != '0') parent.window.location.href = '/forums/' + data + '/';
			else parent.$.colorbox.close();
		}
	});
});