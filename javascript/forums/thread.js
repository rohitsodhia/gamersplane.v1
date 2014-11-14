$(function() {
	$('.postAsChar .userAvatar').each(function () {
		$img = $(this).find('img');
		$(this).css({'top': '-' + ($img.height() / 2) + 'px', 'right': '-' + ($img.width() / 2) + 'px' });
	});

	$('#messageTextArea').markItUp(mySettings);

	$('.deletePost').colorbox();
});