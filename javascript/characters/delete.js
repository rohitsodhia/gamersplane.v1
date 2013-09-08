$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
		},
		success: function (data) {
				alert(data);
			if (data == 'deleted') {
				parent.$('#char_' + $('#characterID').val()).remove();
				parent.$.colorbox.close();
			}
		}
	});
});