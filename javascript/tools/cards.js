$(function() {
	var $newDeckLink = $('a.newDeckLink'),
		$newDeck = $('div.newDeck'),
		$deckName = $('p.deckName'),
		$cardsLeft = $('span.cardsLeft'),
		$cardSpace = $('div.cardSpace'),
		$cardControls = $('.cardControls'),
		$drawCards = $('button.drawCards'),
		$numCards = $('input.numCards');

	$newDeckLink.click(function (e) {
		$newDeck.slideToggle();
		
		e.preventDefault();
	});
	
	$newDeck.find('a').click(function (e) {
		$.post(SITEROOT + '/tools/ajax/newDeck', { newDeck: this.id.split('_')[1] }, function (data) {
			deckInfo = data.split('~');
			$deckName.text(deckInfo[1]);
			$cardsLeft.text(deckInfo[2]);
			$newDeck.slideUp();
			$cardSpace.html('<p id="deckAnnouncement">Draw cards on the left</p>');
			$cardControls.fadeIn();
		});
		
		e.preventDefault();
	});
	
	$drawCards.click(function (e) {
		numCards = parseInt($numCards.val()), size = $(this).closest('#fixedMenu_cards').length > 0?65:'';
		if (numCards > 1) {
			$.post(SITEROOT + '/tools/process/cards', { ajax: true, numCards: numCards, size: size }, function (data) {
				if (data.length > 0) {
					$cardSpace.html(data);
					$cardsLeft.html(parseInt($cardsLeft.text()) - numCards >= 0 ? parseInt($cardsLeft.text()) - numCards : 0)
				} else {
					$cardControls.hide();
					$newDeck.slideDown();
					$cardSpace.html('<p id="deckAnnouncement">Deck empty. Please select a new deck from above.</p>');
				}
			});
		}
		
		e.preventDefault();
	});
});