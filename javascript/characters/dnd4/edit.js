function trigger_levelUpdate(oldLevel) {
	$('.addHL').each(function () {
		$(this).text(showSign(parseInt($(this).text()) - Math.floor(oldLevel / 2) + Math.floor(level / 2)));
	});
}

function trigger_statUpdate(stat) {
	if (stat == 'str' || stat == 'con') {
		if (statBonus['con'] > statBonus['str']) useAbility = 'con';
		else useAbility = 'str';

		$('#fortTotal').removeClass('addStat_con addStat_str').addClass('addStat_' + useAbility);
		$('#fortStatBonus').text(showSign(parseInt(statBonus[useAbility])));
		$('#fortRow input').eq(0).blur();
	} else if (stat == 'dex' || stat == 'int') {
		if (statBonus['dex'] > statBonus['int']) useAbility = 'dex';
		else useAbility = 'int';

		$('#refTotal').removeClass('addStat_dex addStat_int').addClass('addStat_' + useAbility);
		$('#refStatBonus').text(showSign(parseInt(statBonus[useAbility])));
		$('#refRow input').eq(0).blur();
	} else {
		if (statBonus['wis'] > statBonus['cha']) useAbility = 'wis';
		else useAbility = 'cha';

		$('#willTotal').removeClass('addStat_wis addStat_cha').addClass('addStat_' + useAbility);
		$('#willStatBonus').text(showSign(parseInt(statBonus[useAbility])));
		$('#willRow input').eq(0).blur();
	}
}

$(function() {
	var $hpInput = $('#hpInput'), $bloodiedVal = $('#bloodiedVal'), $surgeVal = $('#surgeVal');
	$hpInput.blur(function () {
		var hp = $hpInput.val().length?parseInt($hpInput.val()):0;
		$bloodiedVal.text(Math.floor(hp / 2));
		$surgeVal.text(Math.floor(hp / 4));
	});

	$('#addAttack').click(function (e) {
		$.post('/characters/ajax/dnd4/addAttack', { count: $('.attackBonusSet').size() + 1 }, function (data) {
			$(data).hide().appendTo('#attacks .hbdMargined').slideDown();
		});
		
		e.preventDefault();
	});
	$('#attacks').on('blur', '.sumRow input', sumRow);
	
	$('#powers').on('click', 'h3 a', function (e) {
		e.preventDefault();

		$powerCol = $(this).parent().parent();
		$.post('/characters/ajax/dnd4/addPower/', { type: $(this).data('type') }, function (data) {
			$(data).appendTo($powerCol).addClass('editing').find('input').autocomplete('/characters/ajax/autocomplete/', { type: 'dnd4_power', characterID: characterID, system: system, systemOnly: true }).find('input').placeholder().focus();
		})
	}).on('click', '.edit', function (e) {
		e.preventDefault();

		$power_name = $(this).parent().children('.power_name');
		$power_name.find('input').val($power_name.children('span').text()).trigger('change');
		$(this).parent().addClass('editing');
	}).on('keypress', '.power_name input', function (e) {
		$input = $(this), $wrapper = $input.closest('.power_name'), $span = $wrapper.children('span');

		if (e.which == 13 && $input.val() != '') {
			$span.text($input.val());
			$wrapper.parent().removeClass('editing');
		} else if (e.which == 27 && $span.text() != '') {
			$input.val($span.text());
			$wrapper.parent().removeClass('editing');
		}
	}).on('click', '.power_remove', function (e) {
		e.preventDefault();
		$(this).parent().remove();
	}).find('.power_name input').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'dnd4_power', characterID: characterID, system: system, systemOnly: true });
});