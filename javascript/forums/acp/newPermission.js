$(function () {
	$('#optSearch').autocomplete('/forums/acp/ajax/optSearch', { search: $(this).val(), permissionType: $('#permissionType').val(), forumID: $('#forumID').val() });

	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			$optSearch = $('#optSearch');
			if ($optSearch.val() == $optSearch.data('placeholder')) return false;

			return true;
		},
		success: function (data) {
			if (data == '1') {
				parent.document.location.reload();
			}
		}
	});
});