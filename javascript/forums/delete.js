$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data == 'refresh') parent.document.location.reload();
			else if (data != '0') parent.document.location.href = '/forums/' + data + '/';
			else parent.$.colorbox.close();
		}
	});
});