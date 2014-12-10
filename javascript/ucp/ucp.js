$(function () {
	$('#password1').blur(function () {
		passLength = $(this).val().length;
		if (passLength == 0) {
			$('#passShort').hide();
			$('#passLong').hide();
			return true;
		}

		if (passLength < 6) $('#passShort').show();
		else $('#passShort').hide();
		if (passLength > 32) $('#passLong').show();
		else $('#passLong').hide();
	});
	$('#password2').blur(function () {
		if ($('#password1').val() != $('#password2').val() && $('#password2').val().length > 0) $('#passMismatch').show();
		else $('#passMismatch').hide();
	});

	$('button').click(function (e) {
		if ($('.error:visible').length) e.preventDefault();
	})
});