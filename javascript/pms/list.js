$(function () {
	leftSpacing = $('#pms .hbDark .dlWing').css('borderRightWidth');
	$('#pms .tr:not(.headerTR), #newPM').css('margin', '0 ' + leftSpacing);
});

function deleted(pmID) {
	$('#pm_' + pmID).remove();
	if ($('div.pm').length == 0) $('#noPMs').show();

	$.colorbox.close();
}