$(function () {
	$('#password1').blur(function () {
		passLength = $(this).val().length;
		if (passLength == 0) return true;

		if (passLength < 6) $('#passShort').show();
		else $('#passShort').hide();
		if (passLength > 32) $('#passLong').show();
		else $('#passLong').hide();
	});
	$('#password2').blur(function () {
		if ($('#password1').val() != $('#password2').val()) $('#passMismatch').show();
		else $('#passMismatch').hide();
	});
});