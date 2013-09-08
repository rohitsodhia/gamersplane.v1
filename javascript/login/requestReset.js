$(function () {
	leftSpacing = $('h1 .wing').css('borderRightWidth');
	$('p, form, .alertBox_error').css({ 'margin-left': leftSpacing, 'margin-right': leftSpacing });

/*	parent.$.colorbox.resize({ innerHeight: 170 });

	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			$('form input[type="text"]').each(function () {
				if ($(this).val().length == 0) return false;
			});

			return true;
		},
		success: function (data) {
			if (data == '1') {
				parent.$.colorbox.close();
//				parent.document.location.reload();
			}
		}
	});*/
});