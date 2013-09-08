$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			if ($('#newLabel').val().length > 0) return true;
			else return false;
		},
		success: function (data) {
			if (data == 'updated') {
				parent.$('#char_' + $('#characterID').val() + ' .charLabel').text($('#newLabel').val());
				parent.$.colorbox.close();
			}
		}
	});
});