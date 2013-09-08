/*
	Gamer's Plane Registration Functions
	Created by RhoVisions, designer Rohit Sodhia
*/

var passValid = false;

function checkPass() {
	var pass1 = $('#password1').val();
	var pass2 = $('#password2').val();
	
	if (pass1.length < 6 && pass1.length != 0) {
		$('#passLong').fadeOut('normal', function () {
			$('#passShort').fadeIn('normal');
		});
		
		passValid = false;
	} else if (pass1.length > 16 && pass1.length != 0) {
		$('#passShort').fadeOut('normal', function () {
			$('#passLong').fadeIn('normal');
		});
		
		passValid = false;
	} else {
		$('#passShort').fadeOut('normal');
		$('#passLong').fadeOut('normal');
	}
	
	if (pass1.length >= 6 && pass1.length <= 16 && pass1 != pass2 && pass2.length != 0) {
		$('#passMismatch').fadeIn('normal');
		
		passValid = false;
	} else if ($('#passMismatch').css('display') != 'none') {
		$('#passMismatch').fadeOut('normal');
		
		passValid = true;
	} else passValid = true;
}

$(function () {
	$('form').append('<input type="hidden" name="ajaxForm" value="1">').ajaxForm({
		beforeSubmit: function () {
			$('form input[type="text"]').each(function () {
				if ($(this).val().length == 0) return false;
			});
			checkPass();
			if (!passValid) return false;

			return true;
		},
		success: function (data) {
			if (data == 'success') {
				document.location = SITEROOT + '/login/?resetSuccess=1';
			} else {
				document.location.reload();
			}
		}
	});
});