$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data.success) {
				var appElement = parent.document.querySelector('[ng-app=gamersplane]');
				var $scope = parent.angular.element(appElement).scope();
				$scope.$apply(function() {
					$scope.modalWatch = { action: data.action + 'Player', playerID: data.userID };
				});
			}
		}
	});
});