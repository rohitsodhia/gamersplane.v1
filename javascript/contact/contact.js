controllers.controller('contact', ['$scope', '$http', '$timeout', 'ContactService', 'CurrentUser', function ($scope, $http, $timeout, ContactService, CurrentUser) {
	$scope.dispForm = true;
	$scope.errors = {};
	$scope.$emit('pageLoading');
	CurrentUser.load().then(function () {
		$scope.$emit('pageLoading');
		$scope.CurrentUser = CurrentUser.get();
		$scope.loggedIn = $scope.CurrentUser?true:false;
		$timeout(function () { $('.animationFrame').height($('form').height() + 2); });

		// Logged-out visitors get a reCAPTCHA (the container only exists outside dev). Rendered explicitly because
		// Angular builds the form after Google's script has already scanned the page.
		var captchaID = null;
		var renderCaptcha = function (tries) {
			var el = document.getElementById('contactCaptcha');
			if (!el) {
				return;
			}
			if (window.grecaptcha && grecaptcha.render) {
				captchaID = grecaptcha.render(el, { sitekey: el.getAttribute('data-sitekey') });
			} else if (tries < 50) {
				$timeout(function () { renderCaptcha(tries + 1); }, 200);
			}
		};
		if (!$scope.loggedIn) {
			$timeout(function () { renderCaptcha(0); });
		}

		$scope.send = function () {
			var form = angular.copy($scope.form || {});
			if (captchaID !== null) {
				form.captcha = grecaptcha.getResponse(captchaID);
			}

			$scope.$emit('pageLoading');
			ContactService.send(form).then(function (data) {
				$scope.$emit('pageLoading');
				if (data.success) {
					$scope.dispForm = false;
				} else {
					$scope.errors = data.errors;
					// A token is single-use, so make them solve a fresh one after a captcha failure.
					if (data.errors && data.errors.captcha && captchaID !== null) {
						grecaptcha.reset(captchaID);
					}
				}
			});
		};
	});
}]);