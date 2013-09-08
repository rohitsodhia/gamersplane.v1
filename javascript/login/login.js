$(function () {
//	$('#requestReset').attr('href', $('#requestReset').attr('href') + '?modal=1');
	$('#register, #requestReset').attr('target', '_parent');

	$('#username').focus();

	$('#page_login form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			$('form input[type="text"], form input[type="password"]').each(function () {
				if ($(this).val().length == 0) return false;
			});

			return true;
		},
		success: function (data) {
			if (data == '1') {
//				parent.$.colorbox.close();
				parent.document.location.reload();
			} else parent.document.location = data;
		}
	});
});