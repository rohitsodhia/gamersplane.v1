$(function() {
	$('form').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		success: function (data) {
			if (data != '') {
				$.each(data, function (index) {
					$('#' + data[index]).show();
				});
				parent.$.colorbox.resize({ 'innerHeight': $('body').height() } );
//				parent.$.colorbox.close();
//				parent.document.location.reload();
			}
		}
	});
});