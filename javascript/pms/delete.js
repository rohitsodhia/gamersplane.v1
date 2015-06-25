controllers.controller('pmDelete', function ($scope, $cookies, $http) {
	pathElements = getPathElements();

	$http.post(API_HOST + '/pms/allowed/', { pmID: pathElements[2] }).success(function (data) {
		if (!data.allowed) 
			parent.document.location = '/pms/';
		$scope.pmID = pathElements[2];
		$scope.formData = {};
	});

	$scope.deletePM = function () {
		$http.post(API_HOST + '/pms/delete/', { pmID: pathElements[2] }).success(function (data) {
//			if (data.success) parent.document.location = '/pms/';
//			else parent.document.location.reload();
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
//				parent.document.location.reload();
			}
		}
	});
});*/