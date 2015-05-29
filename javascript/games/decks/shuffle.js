$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data.success) {
				var $scope = getModalAngularParent();
				$scope.$apply(function () {
					$scope.modalWatch = { action: 'shuffleDeck', deckID: data.deckID, deckSize: data.deckSize };
				});
			}
		}
	});
});