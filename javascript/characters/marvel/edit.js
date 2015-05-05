$(function() {
	var nextActionCount = $('.action').length + 1, nextModifierCount = $('.modifier').length + 1, nextChallengeCount = $('.challenge').length + 1;
	$('#actions').on('click', '.remove', function (e) {
		e.preventDefault();

		$(this).closest('.action').remove();
	});
	$('#addAction').click(function (e) {
		var actionName = $('#actionForm input').val();
		nextActionCount += 1;
		$.post('/characters/ajax/marvel/addAction/', { key: nextActionCount }, function (data) {
			$action = $(data);
			$action.hide().appendTo('#actions .hbdMargined').slideDown();
		});
		
		e.preventDefault();
	});

	$('#modifiers').on('click', '.remove', function (e) {
		e.preventDefault();

		$(this).closest('.modifier').remove();
	});
	$('#addModifier').click(function (e) {
		var modifierName = $('#modifierForm input').val();
		nextModifierCount += 1;
		$.post('/characters/ajax/marvel/addModifier/', { key: nextModifierCount }, function (data) {
			$modifier = $(data);
			$modifier.hide().appendTo('#modifiers .hbdMargined').slideDown();
		});
		
		e.preventDefault();
	});
	
	$('#challenges').on('click', '.remove', function (e) {
		$link = $(this);
		$(this).parent().slideUp(function () { $(this).find('input').val(0); });

		e.preventDefault();
	});
	$('#addChallenge').click(function (e) {
		nextChallengeCount += 1;
		$.post('/characters/ajax/marvel/addChallenge/', { key: nextChallengeCount }, function (data) {
			$(data).hide().appendTo('#challenges .hbdMargined').slideDown();
		});
		
		e.preventDefault();
	});
});