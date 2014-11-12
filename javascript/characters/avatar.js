$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data != 0) {
				if (data == 1) parent.$('#charAvatar .sprite').attr('class', 'sprite cross small');
				else parent.$('#charAvatar .sprite').attr('class', 'sprite check green small');
				parent.$.colorbox.close();
			}
		}
	});
});