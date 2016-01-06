$(function() {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data != '') {
				data = $.parseJSON(data);
				$('.alert').filter(function (index) { return !$(this).hasClass('hideDiv'); }).addClass('hideDiv');
				$.each(data, function (index, value) {
					$('#' + index).removeClass('hideDiv');
				});
				parent.$.colorbox.resize({ 'innerHeight': $('body').height() } );
			} else {
				parent.window.location.reload();
			}
		}
	});
});