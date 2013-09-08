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
		$(data).find('message').each(function() {
			$('#chatArea').append('<div class="chatItem"><b>[' + $(this).find('date').text() + '] ' + $(this).find('poster').text() + '</b> > ' + $(this).find('text').text()).scrollTo('max');
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
	$('#message input').keydown(function (key) { if (key.which == 13) sendMessage(); });
	$('#send').click(sendMessage);
	setInterval('getNewMessages()', 1000);
	setInterval('getUsers()', 5000);
	getNewMessages();
	getUsers();
});