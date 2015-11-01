controllers.controller('contact', ['$scope', '$http', 'CurrentUser', function ($scope, $http, CurrentUser) {
	CurrentUser.load().then(function () {
		$scope.CurrentUser = CurrentUser.get();
		$scope.loggedIn = $scope.CurrentUser?true:false;
		$scope.dispForm = true;

		$scope.send = function () {
			$scope.dispForm = false;
		};
	});
}]);

$(function () {
	$('#page_contact form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			$('form input').each(function () {
				if ($(this).val().length == 0) return false;
			});
			$('#jsError').slideUp();

			return true;
		},
		success: function (data) {
			if (data == '1') {
				document.location = '/contact/success';
			} else $('#jsError').slideDown();
		}
	});
});