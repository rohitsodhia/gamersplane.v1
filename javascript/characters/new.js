$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			if ($('input').val().length > 0 && $('select').val() != '') return true;
			else return false;
		},
		success: function (data) {
			if (data == '1') {
				parent.document.location.reload();
			}
		}
	});
});