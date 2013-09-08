$(function() {
	$('#messageTextArea').markItUp(mySettings);

	leftMargin = $('.hbDark .wing').css('border-right-width');
	$('#markItUpMessageTextArea').css({ 'marginLeft': leftMargin, 'marginRight': leftMargin });
});