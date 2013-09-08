function sendMessage() {
	var message = $('#message input').val();
	var gameID = $('#gameID').val();
	$.post(SITEROOT + '/chat/ajax/sendMessage', { message: message, gameID: gameID }, function (data) {
		if ($(data).find('success').size()) {
//			var postInfo = $(data).find('success');
//			$('#chatArea').append('<div class="chatItem"><b>[' + $(postInfo).find('date').text() + '] ' + $(postInfo).find('username').text() + '</b>: ' + message).scrollTo('max');
//			$('#chatArea').scrollTo('max');
			$('#message input').val('').focus();
		}
	});
}

function getNewMessages() {
	var gameID = $('#gameID').val();
	$.post(SITEROOT + '/chat/ajax/retrieveMessages', { gameID: gameID }, function (data) {
//		alert(data);
//		alert($(data).find('messages').size());
		$(data).find('messageSet').each(function() {
			$('#chatArea').append('<div class="chatItem"><b>[' + $(this).find('date').text() + '] ' + $(this).find('poster').text() + '</b> > ' + $(this).find('message').text()).scrollTo('max');
//			alert($(this).find('chatID').text());
		});
	});
}

function getUsers() {
	var gameID = $('#gameID').val();
	$.post(SITEROOT + '/chat/ajax/retrieveUsers', { gameID: gameID }, function (data) {
		$('#users').html('');
		$(data).find('user').each(function() {
			$('#users').append('<div class="user">' + $(this).text() + '</div>');
		});
	});
}

$(function() {
	$('#username').blur(checkUsername);
	$('#password1').blur(checkPass);
	$('#password2').blur(checkPass);
	$('#email').blur(checkEmail);
	$('#submit').click(checkForm);
});