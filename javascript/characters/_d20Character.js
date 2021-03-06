var size = 0, level = 0, statBonus = { 'str' : 0, 'dex' : 0, 'con' : 0, 'int' : 0, 'wis' : 0, 'cha' : 0 };
$(function() {
	size = parseInt($('#size').val());
	$('#classWrapper .levelInput').each(function () { level += !isNaN(parseInt($(this).val()))?parseInt($(this).val()):0; });
	
	$('#size').blur(function() {
		oldSize = size;
		size = parseInt($(this).val());
		change = size - oldSize;
		$('.sizeVal').text(showSign(size));
		$('.addSize').text(function () {
			newVal = parseInt($(this).text()) + change;
			if ($(this).hasClass('showSign')) return showSign(newVal);
			else return newVal;
		});
	});
	$('#classWrapper').on('blur', '.levelInput', function () {
		oldLevel = level;
		level = 0;
		$('#classWrapper .levelInput').each(function () { level += parseInt($(this).val()); });
		if (isNaN(level)) 
			level = 0;

		if (typeof trigger_levelUpdate == 'function') 
			trigger_levelUpdate(oldLevel);
	});
	$('.stat').blur(function() {
		modifier = Math.floor(($(this).val() - 10)/2);
		change = modifier - statBonus[this.id];
		if ($(this).val() == '') modifier = 0;
		else if (modifier >= 0) modifier = '+' + modifier;
		$('#' + this.id + 'Modifier').text(modifier);
		$('.statBonus_' + this.id).text(modifier);
		$('.addStat_' + this.id).each(function () { $(this).text(showSign(parseInt($(this).text()) + change)); });
		
		statBonus[this.id] = parseInt(modifier);

		if (typeof trigger_statUpdate == 'function') trigger_statUpdate(this.id);
	}).blur();
	$('#bab').blur(function () {
		$('.bab').text(showSign($(this).val()));
		$('#ranged_misc').blur();
	});
});