$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
//			if (data == 'deleted') {
				parent.$('#char_' + $('#characterID').val()).remove();
				if (parent.$('#userChars li.character').length == 0) parent.$('#characterList .noItems').show();

				parent.$.colorbox.close();
//			}
		}
	}).find('button').focus();
});