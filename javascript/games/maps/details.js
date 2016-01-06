$(function () {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function (data) {
			$('input[type="text"]').each(function () {
				if (this.name == 'name' && this.value.length == 0) return false;
				else if (this.name != 'name' && parseInt(this.value) < 10) return false;
			});

			return true;
		},
		success: function (data) {
			if (data == '1') {
				parent.window.location.reload();
			}
		}
	});
});