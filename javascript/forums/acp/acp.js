$(function () {
	$('#controls a').click(function (e) {
		e.preventDefault();

		oldOpen = $('#controls .current').removeClass('current').attr('class');
		newOpen = $(this).attr('class');
		$(this).addClass('current');

		$('span.' + oldOpen + ', div.' + oldOpen + ', form.' + oldOpen).hide();
		$('span.' + newOpen + ', div.' + newOpen + ', form.' + newOpen).show();

	});

	$('a.sprite.cross, a.newPermission, a.permission_delete').colorbox();
	$('a.permission_edit').click(function (e) {
		e.preventDefault();

		$permissions = $(this).parent().parent().children('.permissions');
		$('#permissions .permissions').not($permissions).hide();
		$permissions.toggle();
	});
});