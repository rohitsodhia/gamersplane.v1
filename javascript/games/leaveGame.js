$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data == 'Removed' || data == 'Left') {
/*				var appElement = parent.document.querySelector('[ng-app=gamersplane]');
				var $scope = angular.element(appElement).scope();
				console.log(angular.element(appElement));
				$scope.$apply(function() {
					$scope.triggered = 'withdraw'; 
				});*/
//				$ngInterface = $('#ngInterface', parent.document);
//				$ngInterface.val('withdraw');
				parent.document.location.reload();
			}
		}
	});
});