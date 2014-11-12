var characterID = parseInt($('#characterID').val()), system = $('#system').val();
$(function () {
	$('#charAvatar a').colorbox();

	$('#charDetails').on('blur', '.sumRow input', sumRow);

	if ($('#classWrapper')) {
		$('#classWrapper a').click(function (e) {
			e.preventDefault();
			$classSet = $(this).parent().find('.classSet').eq(0).clone();
			$classSet.find('input').val('');
			$classSet.appendTo($(this).parent());
		});
	}

	$('#content form').on('change', '.abilitySelect', function () {
		$abilitySelect = $(this);
		$statMod = $(this).parent().siblings('.abilitySelectMod');
		$total = $('#' + $abilitySelect.data('totalEle'));
		oldStat = $abilitySelect.data('statHold');
		newStat = $abilitySelect.val();
		totalVal = parseInt($total.html());
		if (oldStat != 'n/a') {
			$statMod.removeClass('statBonus_' + oldStat);
			$total.removeClass('addStat_' + oldStat);
			totalVal -= statBonus[oldStat];
		}
		if (newStat != 'n/a') {
			$statMod.html(showSign(statBonus[newStat])).addClass('statBonus_' + newStat);
			$total.addClass('addStat_' + newStat);
			totalVal += statBonus[newStat];
		} else $statMod.html('+0');
		$abilitySelect.data('statHold', newStat);
		$total.html(showSign(totalVal));
	});

	if ($('#skills').length && !$('#skills').hasClass('nonDefault')) {
		var nextSkillCount = 1;

		$('#skills').on('click', '.skill_remove', function (e) {
			e.preventDefault();

			$(this).parent().remove();
			if ($('.skill').length == 0) $('#addSkill').click();
		}).on('click', '#addSkill', function (e) {
			e.preventDefault();

			$.post('/characters/ajax/addSkill/', { system: system, key: nextSkillCount }, function (data) {
				$newSkill = $(data);
				$newSkill.appendTo('#skillList').prettify().find('.abilitySelect').trigger('change').closest('.skill').find('.skill_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system }).find('input').focus();
				nextSkillCount += 1;
			});
		});

		nextSkillCount = $('#skillList .skill').length + 1;
		$('.skill_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system });

		addCSSRule('.skill_stat', 'width: ' + ($('.skill .skill_stat').eq(0).outerWidth(true)) + 'px; text-align: center;');
	}

	if ($('#feats').length) {
		var nextFeatCount = 1;

		$('#feats').on('click', '.feat_remove', function (e) {
			e.preventDefault();

			$(this).parent().remove();
			if ($('.feat').size() == 0) $('#addFeat').click();
		}).on('click', '#addFeat', function (e) {
			e.preventDefault();

			$.post('/characters/ajax/addFeat/', { system: system, key: nextFeatCount }, function (data) {
				$newFeat = $(data);
				$newFeat.appendTo('#featList').find('.feat_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'feat', characterID: characterID, system: system }).find('input').focus();
				nextFeatCount += 1;
			});
		}).on('click', '.feat_notesLink', function(e) {
			e.preventDefault();

			$(this).siblings('textarea').slideToggle();
		});

		nextFeatCount = $('#featList .feat').length + 1;
		$('.feat_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'feat', characterID: characterID, system: system });
	}

	if ($('#addWeapon').length) { $('#addWeapon').click(function (e) {
		e.preventDefault();

		$.post('/characters/ajax/addWeapon/', { system: system, weaponNum: $('.weapon').size() + 1 }, function (data) { $(data).hide().appendTo('#weapons > div').slideDown(); } );
	}); }
	if ($('#addArmor').length) { $('#addArmor').click(function (e) {
		e.preventDefault();

		$.post('/characters/ajax/addArmor/', { system: system, armorNum: $('.armor').size() + 1 }, function (data) { $(data).hide().appendTo('#armor > div').slideDown(); } );
	}); }

	$('#weapons, #armor').on('click', '.remove', function (e) {
		$(this).parent().parent().remove();

		e.preventDefault()
	});

	$('#submitDiv button').click(function (e) {
		$('.placeholder').each(function () {
			if ($(this).val() == $(this).data('placeholder')) $(this).val('');
		});
	})
});