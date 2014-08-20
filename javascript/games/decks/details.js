$(function () {
	$('#checkAll').click(function (e) {
		e.preventDefault();
		$('input[type="checkbox"]').not('[disabled=disabled]').prop('checked', true);
		$('.prettyCheckbox').not('.disabled').addClass('checked');
	});
	$('#uncheckAll').click(function (e) {
		e.preventDefault();
		$('input[type="checkbox"]').not('[disabled=disabled]').prop('checked', false);
		$('.prettyCheckbox').not('.disabled').removeClass('checked');
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