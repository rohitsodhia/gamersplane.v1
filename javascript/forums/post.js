$(function() {
	$('#messageTextArea').markItUp(mySettings);

	$('#optionControls a').click(function (e) {
		e.preventDefault();

		if (!$(this).hasClass('current')) {
			oldOpen = $('#optionControls .current').removeClass('current').attr('class');
			newOpen = $(this).attr('class');
			$(this).addClass('current');

			$('span.' + oldOpen + ', div.' + oldOpen).hide();
			$('span.' + newOpen + ', div.' + newOpen).show();
		}
	});

	$rollsTable = $('#rollsTable');
	$('#addRoll').click(function (e) {
		e.preventDefault();

		$.post(SITEROOT + '/forums/ajax/addRoll', { count: $rollsTable.find('tr').length }, function (data) {
			$newRow = $(data);
			$newRow.find('input[type="checkbox"]').prettyCheckbox();
			$newRow.find('select').prettySelect();
			$newRow.appendTo($rollsTable);
		})
	});
});