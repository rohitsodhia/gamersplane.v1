var app = angular.module('gamersplane', ['ngCookies']);
app.controller('pmList', function ($scope, $cookies, $http) {
	function getPMs() {
		$http.post('http://api.gamersplane.local/pms/view/inbox/', { loginHash: $cookies.loginHash }).success(function (data) {
			data.forEach(function (value, key) {
				data[key].datestamp = convertTZ(value.datestamp, 'YYYY-MM-DD HH:mm:ss', 'MMMM D, YYYY h:mm a')
			});
			$scope.pms = data;
		});
	}
	
	$scope.box = 'Inbox';
	getPMs();

	$scope.switchBox = function ($event, box) {
		$event.preventDefault();
		newBox = box.capitalizeFirstLetter();
		if ($scope.box != newBox) {
			$scope.box = newBox;
			getPMs();
		}
	};
});

$(function () {
	leftSpacing = $('#pms .hbDark .dlWing').css('borderRightWidth');
	$('#pmList, #newPM').css('margin', '0 ' + leftSpacing);
});

function deleted(pmID) {
	$('#pm_' + pmID).remove();
	if ($('div.pm').length == 0) $('#noPMs').show();

	$.colorbox.close();
}