$(function() {
	var currentlyShown = 'basic';
	$('div.sectionControls select').change(function (e) {
		e.preventDefault();

		switchTo = $(this).val();
		if (switchTo != currentlyShown) {
			$('span.dice_' + currentlyShown + ', div.dice_' + currentlyShown).hide();
			$('span.dice_' + switchTo + ', div.dice_' + switchTo).show();
			currentlyShown = switchTo;
			$('.rollWrapper button').each(adjustSkewMargins);
		}
	});

	$diceSpace = $('#diceSpace');
	$('#basic_customDice').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function (data) {
			$.each(data, function (index, element) {
				if (element.name == 'dice' && element.value.length == 0) return false;
			});

			return true;
		},
		success: function (data) {
			$('div.newestRolls').removeClass('newestRolls');
			$(data).addClass('newestRolls').prependTo($diceSpace);
			$('.newestRolls').slideDown(400);
		}
	});
	
	$sweote_dicePool = $('div.dice_sweote div.dicePool');
	$('a.addDiceLink').click(function (e) {
		e.preventDefault();

		diceType = this.id.split('_')[1];
		$sweote_dicePool.append('<a href="" class="sweote_dice ' + diceType + '"><div></div></a>');
	});
	$sweote_dicePool.on('click', '.sweote_dice', function (e) {
		e.preventDefault();

		$(this).remove();
	});
	$('#sweote_roll').click(function (e) {
		e.preventDefault();

		var dice = Array();
		$sweote_dicePool.children().each(function () {
			$.each(this.className.split(' '), function (index, curClass) {
				if (curClass.substring(0, 7) != 'sweote_') dice.push(curClass);
			});
		});
		$.post('/tools/process/dice/', { rollType: 'sweote', dice: dice.join(',') }, function (data) {
			$('.newestRolls').removeClass('newestRolls');
			$(data).addClass('newestRolls').prependTo($diceSpace);
			$('.newestRolls').slideDown(400);
		});
	});
	$('#sweote_clear').click(function (e) {
		e.preventDefault();

		$sweote_dicePool.html('');
	});

	$('#fate_roll').click(function (e) {
		var diceCount = parseInt($('#fate_count').val());
		if (diceCount <= 0) 
			diceCount = 1;
		else if (diceCount > 50) 
			diceCount = 50;
		$.post('/tools/process/dice/', { rollType: 'fate', dice: diceCount }, function (data) {
			$('.newestRolls').removeClass('newestRolls');
			$(data).addClass('newestRolls').prependTo($diceSpace);
			$('.newestRolls').slideDown(400);
		});
	});

	$('#fengshui_roll').click(function (e) {
		var actionValue = parseInt($('#fengshui_av').val());
		if (actionValue < 0) 
			diceCount = 0;
		else if (actionValue > 50) 
			diceCount = 50;
		var type = 'standard';
		if (['standard', 'fortune', 'closed'].indexOf($('#fengshui_type').val()) >= 0) 
			type = $('#fengshui_type').val();
		$.post('/tools/process/dice/', { rollType: 'fengshui', dice: actionValue, 'options': [type] }, function (data) {
			$('.newestRolls').removeClass('newestRolls');
			$(data).addClass('newestRolls').prependTo($diceSpace);
			$('.newestRolls').slideDown(400);
		});
	});
});