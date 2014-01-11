$(function() {
	var characterID = parseInt($('#characterID').val());
	$('#skillName').autocomplete('/characters/ajax/skillSearch', { search: $('#skillName').val(), characterID: characterID, system: 'sweote' });
	
	$('#addSkill').click(function (e) {
		if ($('#skillName').val().length >= 3 && $('#skillName').val() != 'Skill Name') {
			console.log({ characterID: characterID, name: $('#skillName').val(), stat: $('#skillStat').val() });
			$.post(SITEROOT + '/characters/ajax/sweote/addSkill', { characterID: characterID, name: $('#skillName').val(), stat: $('#skillStat').val() }, function (data) {
				if ($('#noSkills').size()) $('#noSkills').remove();
				$(data).hide().appendTo('#skills .hbdMargined').slideDown().find('input[type="checkbox"]').prettyCheckbox();
				$('#skillName').val('').trigger('blur');
			});
		}
		
		e.preventDefault()
	});
	$('#skills').on('click', '.skill_remove', function (e) {
		var skillID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/sweote/removeSkill', { characterID: characterID, skillID: skillID }, function (data) {
			if (data == 1) { $('#skill_' + skillID).slideUp(function () {
				$(this).remove();
				if ($('.skill').size() == 0) $('<p id=\"noSkills\">This character currently has no skills.</p>').hide().appendTo('#skills .hbdMargined').slideDown();
			}); }
		});
		
		e.preventDefault()
	});
	
	function removeTalent(e) {
		var talentID = $(this).parent().attr('id').split('_')[1];
		$.post(SITEROOT + '/characters/ajax/sweote/removeTalent', { characterID: characterID, talentID: talentID }, function (data) {
			if (parseInt(data) == 1) { $('#talent_' + talentID).slideUp(function () {
				$(this).remove();
				if ($('.talent').size() == 0) $('<p id="noTalents">This character currently has no talents.</p>').hide().appendTo('#talents .hbdMargined').slideDown();
			}); }
		});
		
		e.preventDefault()
	}
	
	$('#talentName').autocomplete('/characters/ajax/sweote/talentSearch', { search: $(this).val(), characterID: characterID, system: 'sweote' });
	
	$('#addTalent').click(function (e) {
		if ($('#talentName').val().length >= 3) {
			$.post(SITEROOT + '/characters/ajax/sweote/addTalent', { characterID: characterID, name: $('#talentName').val() }, function (data) {
				if ($('#noTalents').size()) $('#noTalents').remove();
				$(data).hide().appendTo('#talents .hbdMargined').slideDown();
				$('#talentName').val('').trigger('blur');
			});
		}
		
		e.preventDefault()
	});
	$('.talent_notesLink').colorbox();
	$('#talents').on('click', '.talent_remove', removeTalent);
	
	$('#addWeapon').click(function (e) {
		$.post(SITEROOT + '/characters/ajax/sweote/weapon', { weaponNum: $('.weapon').size() + 1 }, function (data) { $(data).hide().appendTo('#weapons > div').slideDown(); } );
		
		e.preventDefault()
	});
	
	$('#weapons').on('click', '.remove', function (e) {
		$(this).parent().parent().remove();

		e.preventDefault()
	});
});