$(function() {
	var $newDeckLink = $('.newDeckLink a'),
		$newDeck = $('div.newDeck'),
		$deckName = $('p.deckName'),
		$cardsLeft = $('span.cardsLeft'),
		$cardSpaceContent = $('div.cardSpace div'),
		$cardControls = $('.cardControls'),
		$drawCards = $('.drawCards button'),
		$numCards = $('input.numCards'),
		numCards = 0,
		cardPosition = 1;
	if ($('#fixedMenu').length) {
		var $arrows = $('.arrow'),
			$upArrow = $('#fm_upArrow'),
			$downArrow = $('#fm_downArrow');
	}

	$newDeckLink.click(function (e) {
		$newDeck.slideToggle();
		
		e.preventDefault();
	});
	
	$newDeck.find('a').click(function (e) {
		$.post('/tools/ajax/newDeck', { newDeck: this.id.split('_')[1] }, function (data) {
			deckInfo = data.split('~');
			$deckName.text(deckInfo[1]);
			$cardsLeft.text(deckInfo[2]);
			$newDeck.slideUp();
			$cardSpaceContent.html('<p id="deckAnnouncement">Draw cards on the left</p>');
			$cardControls.fadeIn();
		});
		
		e.preventDefault();
	});
	
	$drawCards.click(function (e) {
		numCards = parseInt($numCards.val()), size = $(this).closest('#fm_cards').length > 0?'mini':'';
		if (numCards >= 1) {
			$.post('/tools/process/cards', { ajax: true, numCards: numCards, size: size }, function (data) {
				if (data.length > 0) {
					$cardSpaceContent.css('top', 0).html(data);
					if ($arrows) $arrows.addClass('hideArrow');
					cardPosition = 1;
					$cardsLeft.html(parseInt($cardsLeft.text()) - numCards >= 0 ? parseInt($cardsLeft.text()) - numCards : 0)
					if ($('#fixedMenu').length && numCards > 5) {
						$downArrow.removeClass('hideArrow');
					}
				} else {
					$cardControls.hide();
					$newDeck.slideDown();
					$cardSpaceContent.html('<p id="deckAnnouncement">Deck empty. Please select a new deck from above.</p>');
					if ($('#fixedMenu').length) {
						$upArrow.addClass('hideArrow');
						$downArrow.addClass('hideArrow');
					}
				}
			});
		}
		
		e.preventDefault();
	});

	if ($('#fixedMenu').length) {
		$arrows.click(function (e) {
			e.preventDefault();
			totalPages = Math.ceil(numCards / 5);

			if (!$(this).hasClass('hideArrow')) {
				position = $cardSpaceContent.position();
				if (this.id == 'fm_downArrow' && cardPosition <= totalPages) {
					$cardSpaceContent.css('top', position.top - 125);
					cardPosition += 1;
					if (cardPosition > 1) $upArrow.removeClass('hideArrow');
				} else if (this.id == 'fm_upArrow' && cardPosition > 1) {
					$cardSpaceContent.css('top', position.top + 125);
					cardPosition -= 1;
					if (cardPosition < totalPages) $downArrow.removeClass('hideArrow');
				}

				if (cardPosition == 1) $upArrow.addClass('hideArrow');
				else if (cardPosition == totalPages) $downArrow.addClass('hideArrow');
			}
		});
	}
});