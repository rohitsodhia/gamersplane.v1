$(function () {
	$('#checkAll').click(function (e) {
		e.preventDefault();
		$('input[type="checkbox"]').prop('checked', true);
		$('.prettyCheckbox').addClass('checked');
	});
	$('#uncheckAll').click(function (e) {
		e.preventDefault();
		$('input[type="checkbox"]').prop('checked', false);
		$('.prettyCheckbox').removeClass('checked');
	});
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function (data) {
			if ($('#deckLabel').val().length == 0) return false;
			if ($('input[type="radio"]:checked').val().length == 0) return false;

			return true;
		},
		success: function (data) {
			if (data == '1') {
				parent.document.location.reload();
			}
		}
	});
});