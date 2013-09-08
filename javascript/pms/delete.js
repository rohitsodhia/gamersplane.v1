$(function () {
	var pmID = $('#pmID').val();
	$('#page_pm_delete form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data == '1') {
				parent.deleted(pmID);
//				parent.document.location.reload();
			}
		}
	});
});