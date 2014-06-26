function verifySkill() {
	if ($('#skillName').val().length >= 3 && $('#skillName').val() != 'Skill Name' && $('#skillStat_1').val() != $('#skillStat_2').val()) return true;
	else return false;
}

function newSkillObj() {
	return {
		characterID: characterID,
		system: 'spycraft2',
		name: $('#skillName').val(),
		stat_1: $('#skillStat_1').val(),
		stat_2: $('#skillStat_2').val(),
		statBonus_1: statBonus[$('#skillStat_1').val()],
		statBonus_2: $('#skillStat_2').val() != '' ? statBonus[$('#skillStat_2').val()] : 0
	}
}

$(function() {
	$('#focusName').autocomplete('/characters/ajax/spycraft2/focusSearch', { characterID: characterID });

	$('#addFocus').click(function (e) {
		if ($('#focusName').val().length >= 3) { $.post('/characters/ajax/spycraft2/addFocus', { characterID: characterID, name: $('#focusName').val() }, function (data) {
			if ($('#noFocuses').size()) {
				$('#noFocuses').slideUp();
				$('#focuses .labelTR').slideDown();
			}
			$(data).hide().appendTo('#focuses .hbdMargined').slideDown();
			$('#focusName').val('').trigger('blur');
		}); }
		
		e.preventDefault();
	});
	
	$('#focuses').on('click', '.focus_remove', function (e) {
		var focusID = $(this).parent().attr('id').split('_')[1];
		$.post('/characters/ajax/spycraft2/removeFocus', { characterID: characterID, focusID: focusID }, function (data) {
			if (parseInt(data) == 1) { $('#focus_' + focusID).slideUp(function () {
				$(this).remove();
				if ($('.focus').size() == 0) {
					$('#noFocuses').slideDown();
					$('#focuses .labelTR').slideUp();
				}
			}); }
		});
		
		e.preventDefault();
	});
});