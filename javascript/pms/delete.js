controllers.controller('pmDelete', function ($scope, $cookies, $http) {
	pathElements = getPathElements();

	$http.post(API_HOST + '/pms/allowed/', { pmID: pathElements[2] }).success(function (data) {
		if (!data.allowed) 
			parent.window.location.href = '/pms/';
		$scope.pmID = pathElements[2];
		$scope.formData = {};
	});

	$scope.deletePM = function () {
		$http.post(API_HOST + '/pms/delete/', { pmID: pathElements[2] }).success(function (data) {
//			if (data.success) parent.window.location.href = '/pms/';
//			else parent.window.location.reload();
		});
	}

	$scope.cancel = function ($event) {
		parent.$.colorbox.close();
	}
});
/*$(function () {
	var pmID = $('#pmID').val();
	$('#page_pm_delete form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data == '1') {
				parent.deleted(pmID);
//				parent.window.location.reload();
			}
		}
	});
});*/