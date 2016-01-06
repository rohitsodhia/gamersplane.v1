/*
	Gamer's Plane Registration Functions
	Created by RhoVisions, designer Rohit Sodhia
*/

var passValid = false;

$('#password1').blur(function () {
	var pass = $(this).val();
	
	if (pass.length < 6 && pass.length != 0) {
		$('#passLong').fadeOut('normal', function () {
			$('#passShort').fadeIn('normal');
		});
		
		passValid = false;
	} else if (pass.length > 16 && pass.length != 0) {
		$('#passShort').fadeOut('normal', function () {
			$('#passLong').fadeIn('normal');
		});
		
		passValid = false;
	} else {
		$('#passShort').fadeOut('normal');
		$('#passLong').fadeOut('normal');
	}
})

$('#password2').blur(function () {
	var pass1 = $('#password1').val();
	var pass2 = $('#password2').val();
	
	if (pass1.length >= 6 && pass1.length <= 16 && pass1 != pass2 && pass2.length != 0) {
		$('#passMismatch').fadeIn('normal');
		
		passValid = false;
	} else if ($('#passMismatch').css('display') != 'none') {
		$('#passMismatch').fadeOut('normal');
		
		passValid = true;
	} else passValid = true;
});

$(function () {
	$('form').append('<input type="hidden" name="ajaxForm" value="1">').ajaxForm({
		beforeSubmit: function () {
			$('form input[type="text"]').each(function () {
				if ($(this).val().length == 0) return false;
			});
			$('input[type="password"]').blur();
			if (!passValid) return false;

			return true;
		},
		success: function (data) {
			if (data == 'success') {
				window.location.href = '/login/?resetSuccess=1';
			} else {
//				window.location.reload();
			}
		}
	});
});