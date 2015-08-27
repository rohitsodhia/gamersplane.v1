$(function () {
	$('#checkAll').click(function (e) {
		e.preventDefault();
		$('input[type="checkbox"]').filter(function () { return $(this).data('disabled') == 'disabld'?false:true; }).prop('checked', true);
		$('.prettyCheckbox').not('.disabled').addClass('checked');
	});
	$('#uncheckAll').click(function (e) {
		e.preventDefault();
		$('input[type="checkbox"]').filter(function () { return $(this).data('disabled') == 'disabld'?false:true; }).prop('checked', false);
		$('.prettyCheckbox').not('.disabled').removeClass('checked');
	});
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function (data) {
			if ($('#deckLabel').val().length == 0) 
				return false;
			if ($('input[type="radio"]:checked').val().length == 0) 
				return false;

			return true;
		},
		success: function (data) {
			if (data.success) {
				var $scope = getModalAngularParent();
				$scope.$apply(function() {
					$scope.modalWatch = { action: data.action, deck: data.deck };
				});
			}
		}
	});
});