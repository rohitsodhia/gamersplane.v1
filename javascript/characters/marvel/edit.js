$(function() {
	var characterID = parseInt($('#characterID').val());

	action = $('#actions').on('click', '.remove', function (e) {
		$set = $(this).closest('.action');
		$allActions = $('#actions .action');
		if ($allActions.index($set) == $allActions.length - 1) $set.slideUp(function () { $(this).parent().slideUp(function () { $(this).remove() }); });
		else $set.slideUp(function () { $(this).parent().animate({ width: 'toggle' }, function () { $(this).remove(); }); });

		e.preventDefault();
	}).find('.action')[0];
	height = $(action).outerHeight();
//	width = $(action).outerWidth();
	$('#actions .actionWrapper').css({ height: height/*, width: width*/ });
	$('#actionSearch').autocomplete('/characters/ajax/marvel/actionSearch', { characterID: characterID });
	$('#addAction').click(function (e) {
		var actionName = $('#actionForm input').val();
		$.post(SITEROOT + '/characters/ajax/marvel/addAction', { characterID: characterID, actionName: actionName }, function (data) {
			if (data.length > 0) {
				$(data).hide();
				if ($('#actions .actionRow:last-of-type .action').length == 3) $('<div class="actionRow clearfix"></div>').html(data).appendTo('#actions .hbMargined');
				else $(data).appendTo('#actions .actionRow:last-of-type');
				$(data).slideDown();
				$('#actionSearch').val('').trigger('blur');
				$(data).find('.actionWrapper').css({ height: height, width: width });
			}
		});
		
		e.preventDefault();
	});

	modifier = $('#modifiers').on('click', '.remove', function (e) {
		$set = $(this).closest('.modifier');
		$allModifiers = $('#modifiers .modifier');
		if ($allModifiers.index($set) == $allModifiers.length - 1) $set.slideUp(function () { $(this).parent().slideUp(function () { $(this).remove() }); });
		else $set.slideUp(function () { $(this).parent().animate({ width: 'toggle' }, function () { $(this).remove(); }); });

		e.preventDefault();
	}).find('.modifier')[0];
	height = $(modifier).outerHeight();
//	width = $(modifier).outerWidth();
	$('#modifiers .modifierWrapper').css({ height: height/*, width: width*/ });
	$('#modifierSearch').autocomplete('/characters/ajax/marvel/modifierSearch', { characterID: characterID });
	$('#addModifier').click(function (e) {
		var modifierName = $('#modifierForm input').val();
		$.post(SITEROOT + '/characters/ajax/marvel/addModifier', { characterID: characterID, modifierName: modifierName }, function (data) {
			if (data.length > 0) {
				$(data).hide();
				if ($('#modifiers .modifierRow:last-of-type .modifier').length == 3) $('<div class="modifierRow clearfix"></div>').html(data).appendTo('#modifiers .hbMargined');
				else $(data).appendTo('#modifiers .modifierRow:last-of-type');
				$(data).slideDown();
				$('#modifierSearch').val('').trigger('blur');
				$(data).find('.modifierWrapper').css({ height: height, width: width });
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
		var challengeName = $('#challengeName').val();
		var stones = $('#challengeStones').val();
		$.post(SITEROOT + '/characters/ajax/marvel/addChallenge', { characterID: characterID, challengeName: challengeName, stones: stones }, function (data) {
			if (data.length > 0) {
				$(data).hide().appendTo('#challenges .hbMargined').slideDown();
				$('#addChallenge, #challengeStones').val('');
			}
		});
		
		e.preventDefault();
	});
});