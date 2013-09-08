$(function() {
	$('#roll').click(function() {
		var dice = $('#dice').val();
		var rerollAces = $('#rerollAces').attr('checked')?1:0;
		if (dice != '') {
			$.post(SITEROOT + '/tools/ajax/dice', { dice: dice, rerollAces: rerollAces }, function (data) {
				$('.newestRolls').removeClass('newestRolls');
				var first = true;
				var classes = '';
				$('<div>').addClass('newestRolls').prependTo('#diceSpace');
				$(data).find('roll').each(function() {
					if ($(this).find('total').text() != '') $('<p>' + $(this).find('dice').text() + '<br>' + $(this).find('indivRolls').text() + ' = ' + $(this).find('total').text() +'</p>').appendTo('.newestRolls');
					else $('<p class="error">Sorry, there was some error. We don\'t let you roll d1s... the answer\'s 1 anyway, and you need to roll a positive number of dice.</p>').appendTo('.newestRolls');
				});
				$('.newestRolls').slideDown(400, function() { $(this).find('p').animate({ backgroundColor: '#000' }, 200); });
			});
		}
		
		return false;
	});
	
	$('#indivDice button').click(function() {
		var dice = '1' + $(this).attr('name');
		$.post(SITEROOT + '/tools/ajax/dice', { dice: dice }, function (data) {
			$('.newestRolls').removeClass('newestRolls');
			var first = true;
			var classes = '';
			$('<div>').addClass('newestRolls').prependTo('#diceSpace');
			$(data).find('roll').each(function() {
				if ($(this).find('total').text() != '') $('<p>' + $(this).find('dice').text() + '<br>' + $(this).find('indivRolls').text() + ' = ' + $(this).find('total').text() +'</p>').appendTo('.newestRolls');
				else $('<p class="error">Sorry, there was some error. We don\'t let you roll d1s... the answer\'s 1 anyway, and you need to roll a positive number of dice.</p>').appendTo('.newestRolls');
			});
			$('.newestRolls').slideDown(400, function() { $(this).find('p').animate({ backgroundColor: '#000' }, 200); });
		});
		
		return false;
	});
});
