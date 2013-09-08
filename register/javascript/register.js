/*
	Gamer's Plane Registration Functions
	Created by RhoVisions, designer Rohit Sodhia
*/

var userValid = false;
var passValid = false;
var emailValid = false;

function checkUsername() {
	var username = $('#username').val();
	
	var validChars = /^\w[\w\.]*$/i;
	var charValidation = validChars.exec(username);
	
	if (username.length <= 24 && username.length >= 4 && charValidation) {
		$.post(SITEROOT + '/register/ajax/loginSearch', { username: username }, function (data) {
			if ($(data).find('user').size()) {
				$('#userLong').fadeOut('normal');
				$('#userInvalid').fadeOut('normal', function () {
					$('#userTaken').fadeIn('normal');
				});
				
				userValid = false;
			} else if ($(data).find('error').text() == 'Dirty') {
				$('#userLong').fadeOut('normal');
				$('#userTaken').fadeOut('normal', function () {
					$('#userInvalid').fadeIn('normal');
				});
				
				userValid = false;
			} else if ($('#userTaken').css('display') != 'none' || $('#userInvalid').css('display') != 'none') {
				$('#userLong').fadeOut('normal');
				$('#userTaken').fadeOut('normal');
				$('#userInvalid').fadeOut('normal');
				
				userValid = true;
			} else { userValid = true; }
		});
	} else if (username.length <= 24 && username.length >= 4 && !charValidation) {
		$('#userLong').fadeOut('normal');
		$('#userTaken').fadeOut('normal', function () {
			$('#userInvalid').fadeIn('normal');
		});
		userValid = false;
	} else if (username.length > 24) {
		$('#userTaken').fadeOut('normal');
		$('#userInvalid').fadeOut('normal', function () {
			$('#userLong').fadeIn('normal');
		});
		userValid = false;
	} else {
		$('#userLong').fadeOut('normal');
		$('#userTaken').fadeOut('normal');
		$('#userInvalid').fadeOut('normal');
		userValid = false;
	}
}

function checkPass() {
	var pass1 = $('#password1').val();
	var pass2 = $('#password2').val();
	
	var delayLength = ($('#passShort').css('display') == 'none' && $('#passLong').css('display') == 'none') ? 0 : 'slow';
	
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
	} else { passValid = true; }
}

function checkEmail() {
	var email = $('#email').val();
	
	if (email.length != 0) {
		$.post(SITEROOT + '/register/ajax/loginSearch', { email: email }, function (data) {
			if ($(data).find('user').size()) {
				$('#emailTaken').fadeIn('normal');
				
				emailValid = false;
			} else if ($('#emailTaken').css('display') != 'none') {
				$('#emailTaken').fadeOut('normal');
				
				emailValid = true;
			} else { emailValid = true; }
		});
	} else {
		if ($('#emailTaken').css('display') != 'none') { $('#emailTaken').fadeOut('normal'); }
		emailValid = false;
	}
}

function checkForm() {
	checkUsername();
	checkPass();
	checkEmail();
	
	if (userValid && passValid && emailValid) { return true; }
	else { alert('You have not completely filled out the form or there is an error.'); return false; }
}