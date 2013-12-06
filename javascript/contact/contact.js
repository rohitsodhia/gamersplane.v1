$(function () {
	$('#page_contact form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			$('form input').each(function () {
				if ($(this).val().length == 0) return false;
			});
			$('#jsError').slideUp();

			return true;
		},
		success: function (data) {
			if (data == '1') {
				document.location = SITEROOT + '/contact/success';
			} else $('#jsError').slideDown();
		}
	});
});