$(function() {
	function equalizeHeights(selector) {
		maxHeight = 0;
		$(selector).not('.spacer').each(function () {
			indivHeight = $(this).css('height');
			indivHeight = parseInt(indivHeight.substring(0, indivHeight.length - 2));
			if (indivHeight > maxHeight) maxHeight = indivHeight;
		}).css('height', maxHeight + 'px');
	}

	$('#actions').on('click', '.remove', function (e) {
		e.preventDefault();

		$action = $(this).closest('.actionWrapper');
		$.post('/characters/ajax/marvel/removeAction/', { characterID: characterID, actionID: $action.attr('id').split('_')[1] }, function (data) {
			$action.slideUp(function () { $(this).remove(); });
			equalizeHeights('.action .name');
		});
	});
	equalizeHeights('.action .name');
	$('#actionSearch').autocomplete('/characters/ajax/marvel/actionSearch/', { characterID: characterID });
	$('#addAction').click(function (e) {
		var actionName = $('#actionForm input').val();
		$.post('/characters/ajax/marvel/addAction/', { characterID: characterID, actionName: actionName }, function (data) {
			if (data.length > 0) {
				$action = $(data);
				$action.hide().appendTo('#actions .hbdMargined').slideDown(function () { equalizeHeights('.action .name'); });
				$('#actionSearch').val('').trigger('blur');
			}
		});
		
		e.preventDefault();
	});

	$('#modifiers').on('click', '.remove', function (e) {
		e.preventDefault();

		$modifier = $(this).closest('.modifierWrapper');
		$.post('/characters/ajax/marvel/removeModifier/', { characterID: characterID, modifierID: $modifier.attr('id').split('_')[1] }, function (data) {
			$modifier.slideUp(function () { $(this).remove(); });
			equalizeHeights('.modifier .name');
		});
	});
	equalizeHeights('.modifier .name');
	$('#modifierSearch').autocomplete('/characters/ajax/marvel/modifierSearch/', { characterID: characterID });
	$('#addModifier').click(function (e) {
		var modifierName = $('#modifierForm input').val();
		$.post('/characters/ajax/marvel/addModifier/', { characterID: characterID, modifierName: modifierName }, function (data) {
			if (data.length > 0) {
				$modifier = $(data);
				$modifier.hide().appendTo('#modifiers .hbdMargined').slideDown(function () { equalizeHeights('.modifier .name'); });
				$('#modifierSearch').val('').trigger('blur');
			}
		});
		
		e.preventDefault();
	});
	
	$('#challenges').on('click', '.remove', function (e) {
		$link = $(this);
		$(this).parent().slideUp(function () { $(this).find('input').val(0); });

		e.preventDefault();
	});
	$('#addChallenge').click(function (e) {
		challengeNum = $('.challenge').length + 1;
		$.post('/characters/ajax/marvel/addChallenge/', { challengeNum: challengeNum }, function (data) {
			$(data).hide().appendTo('#challenges .hbdMargined').slideDown();
		});
		
		e.preventDefault();
	});
});