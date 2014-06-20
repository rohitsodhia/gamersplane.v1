$(function() {
	function equalizeHeights(selector) {
		maxHeight = 0;
		$(selector).not('.spacer').each(function () {
			indivHeight = $(this).css('height');
			indivHeight = parseInt(indivHeight.substring(0, indivHeight.length - 2));
			if (indivHeight > maxHeight) maxHeight = indivHeight;
		}).css('height', maxHeight + 'px');
	}

	equalizeHeights('.action');
	equalizeHeights('.modifier');
	equalizeHeights('.action .name');
	equalizeHeights('.modifier .name');
	equalizeHeights('.action .details');
	equalizeHeights('.modifier .details');
});