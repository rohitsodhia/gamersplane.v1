$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			if ($('#newLabel').val().length > 0) return true;
			else return false;
		},
		success: function (data) {
			if (data == 'updated') {
				console.log(parent.$('#char_' + $('#characterID').val() + ' .label').html());
				parent.$('#char_' + $('#characterID').val() + ' .label').text($('#newLabel').val());
				parent.$.colorbox.close();
			}
		}
	});
});