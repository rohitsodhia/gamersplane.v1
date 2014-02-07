$(function() {
	var currentlyShown = 'basic';
	$('#controls select').change(function (e) {
		e.preventDefault();

		switchTo = $(this).val();
		if (switchTo != currentlyShown) {
			$('span.dice_' + currentlyShown + ', div.dice_' + currentlyShown).hide();
			$('span.dice_' + switchTo + ', div.dice_' + switchTo).show();
			currentlyShown = switchTo;
			$('div.dice_' + switchTo + ' .fancyButton').each(function () { wingMargins($(this)[0]); });
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
			$('<div>').addClass('roll newestRolls').prependTo($diceSpace);
			$(data).find('roll').each(function() {
				if ($(this).find('result').text() != '') $('.newestRolls').append($(this).find('dice').text() + '<br>' + $(this).find('indivRolls').text() + ' = ' + $(this).find('result').text());
				else $('<p class="error">Sorry, there was some error. We don\'t let you roll d1s... the answer\'s 1 anyway, and you need to roll a positive number of dice.</p>').appendTo('.newestRolls');
			});
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
		$.post('/tools/process/dice/', { rollType: 'sweote', dice: dice }, function (data) {
			$('.newestRolls').removeClass('newestRolls');
			$newestRolls = $('<div>').addClass('newestRolls roll').prependTo($diceSpace);
			values = {};
			$(data).find('roll').each(function() {
				dice = $(this).find('dice').text();
				result = $(this).find('result').text();
				$('<div>').addClass('sweote_dice ' + dice + ' ' + result).append('<div>').appendTo($newestRolls);
				$.each($(this).find('values').text().split(','), function (index, value) {
					if (value != '') {
						if (values[value]) values[value]++;
						else values[value] = 1;
					}
				});
			});
			valueText = '';
			if (values != {}) {
				if (values.success) valueText += values.success + ' Success' + (values.success > 1?'es':'') + ', ';
				if (values.advantage) valueText += values.advantage + ' Advantage, ';
				if (values.triumph) valueText += values.triumph + ' Triumph, ';
				if (values.failure) valueText += values.failure + ' Failure' + (values.failure > 1?'s':'') + ', ';
				if (values.threat) valueText += values.threat + ' Threat, ';
				if (values.dispair) valueText += values.dispair + ' Dispair, ';
				if (values.whiteDot) valueText += values.whiteDot + ' White Force Point' + (values.whiteDot > 1?'s':'') + ', ';
				if (values.blackDot) valueText += values.blackDot + ' Black Force Point' + (values.blackDot > 1?'s':'') + ', ';
			}
			$newestRolls.append('<p>' + valueText.substring(0, valueText.length - 2) + '</p>').slideDown(400, function() { $(this).animate({ backgroundColor: '#000' }, 200); });
		});
	});
	$('#sweote_clear').click(function (e) {
		e.preventDefault();

		$sweote_dicePool.html('');
	});
});