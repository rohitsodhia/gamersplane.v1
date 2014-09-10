var userValid = false, titleValid = false, messageValid = false, validated = false;

function checkUsername() {
	var username = $('#username').val();
	
	var validChars = /^\w[\w\.]*$/i;
	var charValidation = validChars.exec(username);
	
	if (charValidation) {
		return ajax_searchUser(username);
	} else {
		$('#invalidUser').fadeOut('normal');
		userValid = false;
	}
}

function ajax_searchUser(username) {
	return $.post('/pms/ajax/userSearch', { username: username }, function (data) {
		if ($(data).find('user').size()) {
			$('#invalidUser').fadeOut('normal');
			userValid = true;
		} else {
			$('#invalidUser').fadeIn('normal');
			userValid = false;
		}
	});
}

function checkTitle() {
	var titleLength = $('#title').val().trim().length;
	if (titleLength > 0) titleValid = true;
	else titleValid = false;
}

function checkMessage() {
	var messageLength = $('#messageTextArea').val().trim().length;
	if (messageLength > 0) messageValid = true;
	else messageValid = false;
}

$(function() {
	$('div.alert').hide();

	$('#username').blur(function () {
		checkUsername()
	});
	$('#title').blur(checkTitle);
	$('#messageTextArea').blur(checkMessage).markItUp(mySettings);

	$('#page_pm_send form').submit(function () {
		if (userValid && titleValid && messageValid) return true;
		else {
			$form = $(this);
			$.when(checkUsername()).done(function (a) {
				checkTitle();
				checkMessage();

				if (userValid && titleValid && messageValid) return true;
			});

			return false;
		}
	});
});