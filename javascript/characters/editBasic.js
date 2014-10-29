$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			if ($('#label').val().length > 0) return true;
			else return false;
		},
		success: function (data) {
			if (data == 'updated') {
				parent.$('#char_' + $('#characterID').val() + ' .label').text($('#label').val());
				parent.$('#char_' + $('#characterID').val() + ' .charType').text($('#charType option[value=' + $('#charType').val() + ']').text());
				parent.$.colorbox.close();
			}
		}
	});
});