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
	
	$('#powerName').autocomplete('/characters/ajax/dnd4/powerSearch', { search: $(this).val(), characterID: characterID });
	$('#addPower').click(function (e) {
		var type = $('#powerType').val();
		if ($('#powerName').val().length >= 3 && $('#powerName').val() != 'Power') {
			$.post('/characters/ajax/dnd4/addPower', { characterID: characterID, name: $('#powerName').val(), type: type }, function (data) {
				var appendDiv = '';
				if (type == 'a') appendDiv = 'atwill';
				else if (type == 'e') appendDiv = 'encounter';
				else if (type == 'd') appendDiv = 'daily';
				$(data).hide().appendTo('#powers_' + appendDiv).slideDown();
				$('#powerName').val('').trigger('blur');
			});
		}
		
		e.preventDefault();
	});
	$('#powers').on('click', '.power_remove', function (e) {
		var powerID = $(this).val();
		var $parent = $(this).parent();
		$.post('/characters/ajax/dnd4/removePower/', { characterID: characterID, powerID: powerID }, function (data) {
			if (data == 1) { $parent.slideUp(function () {
				$(this).remove();
			}); }
		});
		
		e.preventDefault();
	});
});