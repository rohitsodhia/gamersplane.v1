$(function () {
	$('#requestReset').attr('href', $('#requestReset').attr('href') + '?modal=1');

	$('#user').focus();

	$('#page_login form').submit(function (e) {
		e.preventDefault();

		var formDataArray = $(this).serializeArray();
		var formDataJSON = {};
		var invalid = false;
		$(formDataArray).each(function (index, obj) {
			if (obj.name == 'modal') {
				return;
			}
			if (obj.value == '') {
				invalid = true;
			}
			formDataJSON[obj.name] = obj.value;
		});
		if (invalid) {
			return;
		}
		$.ajax({
			type: 'post',
			url: $(this).attr('action'),
			data: JSON.stringify(formDataJSON),
			contentType: 'application/json',
			xhrFields: {
				withCredentials: true
			},
			success: function (data) {
				if (!('success' in data)) {
					parent.window.location.href = '/login?failed=1';
				}
				if ($('body').hasClass('modal')) {
					parent.window.location.reload();
				} else {
					window.location.href = '/';
				}
			}
		});
	});
});
