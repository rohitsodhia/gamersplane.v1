$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data == 'unfavorited') {
				parent.$('#char_' + $('#characterID').val()).remove();
				if (parent.$('#libraryChars li.character').length == 0) parent.$('#libraryFavorites .noItems').show();

				parent.$.colorbox.close();
			}
		}
	});
});