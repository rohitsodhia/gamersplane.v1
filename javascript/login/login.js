$(function () {
	$('#requestReset').attr('href', $('#requestReset').attr('href') + '?modal=1');

	$('#user').focus();

	if ($('body').hasClass('modal')) {
		$('#page_login form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
			beforeSubmit: function () {
				$('form input[type="text"], form input[type="password"]').each(function () {
					if ($(this).val().length === 0) {
						return false;
					}
				});

				return true;
			},
			success: function (data) {
				if (data == '1') {
//					parent.$.colorbox.close();
					parent.window.location.reload();
				} else {
					parent.window.location.href = data;
				}
			}
		});
	}
});
