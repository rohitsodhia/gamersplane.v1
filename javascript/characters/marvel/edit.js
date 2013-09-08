$(function() {
	var characterID = parseInt($('#characterID').val());
	
	$('.placeholderText').css('color', '#666').each(function () {
		$(this).data('placeholder', $(this).val());
	}).focus(function () {
		if ($(this).val() == $(this).data('placeholder')) $(this).val('').css('color', '#FFF');
	}).blur(function () {
		if ($(this).val() == '') $(this).val($(this).data('placeholder')).css('color', '#666');
	});
	
	$('#actionForm input').autocomplete('marvel/actionSearch', { characterID: characterID });
	$('#addAction').click(function () {
		var actionName = $('#actionForm input').val();
		$.post(SITEROOT + '/characters/ajax/marvel/addAction', { characterID: characterID, actionName: actionName }, function (data) {
			if (data.length > 0) {
				$(data).hide().appendTo('#actions').slideDown();
			}
		});
		
		return false;
	});
	
	$('#modifierForm input').autocomplete('marvel/modifierSearch', { characterID: characterID });
	$('#addModifier').click(function () {
		var modifierName = $('#modifierForm input').val();
		$.post(SITEROOT + '/characters/ajax/marvel/addModifier', { characterID: characterID, modifierName: modifierName }, function (data) {
			if (data.length > 0) {
				$(data).hide().appendTo('#modifiers').slideDown();
			}
		});
		
		return false;
	});
	
	$('#addChallenge').click(function () {
		var challengeName = $('#challengeName').val();
		var stones = $('#challengeStones').val();
		$.post(SITEROOT + '/characters/ajax/marvel/addChallenge', { characterID: characterID, challengeName: challengeName, stones: stones }, function (data) {
			if (data.length > 0) {
				$(data).hide().appendTo('#challenges').slideDown();
			}
		});
		
		return false;
	});
});