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

	$newRolls = $('#newRolls');
	$addRoll_type = $('#addRoll select');
	rollCount = 0;
	$('div.newRoll').each(function() {
		name = $(this).find('input[type="hidden"]').attr('name');
		posCount = /rolls\[(\d+)\]/.exec(name);
		if (posCount[1] >= rollCount) rollCount = parseInt(posCount[1]) + 1;
	});
	$('#addRoll button').click(function (e) {
		e.preventDefault();

		$.post('/forums/ajax/addRoll', { count: rollCount, type: $addRoll_type.val() }, function (data) {
			$newRow = $(data);
			$newRow.find('input[type="checkbox"]').prettyCheckbox();
			$newRow.find('select').prettySelect();
			$newRow.appendTo($newRolls);
			rollCount += 1;
		})
	});

	$('#newRolls').on('click', '.close', function (e) {
		e.preventDefault();

		$(this).parent().remove();
	}).on('click', '.add', function (e) {
		e.stopPropagation();

		$(this).siblings('.diceOptions').toggle();
	}).on('click', '.diceOptions .diceIcon', function (e) {
		e.stopPropagation();

		$clickedDice = $(this), $input = $(this).closest('.dicePool').children('input');
		$clickedDice.closest('.dicePool').children('.selectedDice').append($clickedDice.clone());
		inputVal = $input.val().length?$input.val().split(','):[];
		inputVal[inputVal.length] = $clickedDice.attr('class').charAt(16);
		$input.val(inputVal.join());
	}).on('click', '.selectedDice .diceIcon', function (e) {
		e.stopPropagation();

		$selectedDice = $(this).parent();
		$(this).remove();
		var inputVal = [];
		$selectedDice.find('.diceIcon').each(function () {
			inputVal[inputVal.length] = $(this).attr('class').charAt(16);
		});
		$selectedDice.siblings('input').val(inputVal.join());
	});

	$('html').click(function () {
		$('div.diceOptions').hide();
	});
});