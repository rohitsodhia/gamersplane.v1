$.fn.autocomplete = function (pathOption, sendData) {
	function search(pathOption, sendData, $resultsDiv) {
		$.post(SITEROOT + pathOption, sendData, function (data) {
			if (data.length > 0) {
				$resultsDiv.html(data).slideDown();
			} else $resultsDiv.slideUp();
		});
	}

	var $inputBox = $(this), onWrapper = false, searchTimeout;
	$inputBox.wrap('<div class="autocompleteWrapper"></div>').parent().attr('id', $inputBox.attr('id') + 'Wrapper');
	var $resultsDiv = $('<div class="autocompleteResultsWrapper"><div class="autocompleteResults"></div></div>').css({ top: ($inputBox.outerHeight(false) - 1) + 'px', left: 0, width: $inputBox.outerWidth(false) + 'px' }).appendTo($inputBox.parent()).find('.autocompleteResults');
	$inputBox.keyup(function () {
		if ($(this).val().length >= 3 && $(this).val() != $(this).data('placeholder')) {
			$.extend(sendData, { search: $(this).val() });
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(function () { search(pathOption, sendData, $resultsDiv); }, 500);
		} else $resultsDiv.slideUp();
	}).blur(function () {
		if (onWrapper == false) $resultsDiv.slideUp();
	}).focus(function () {
		if ($resultsDiv.find('a').size() > 0 && $(this).val().length >= 3) $resultsDiv.slideDown();
	}).keypress(function (e) {
		if (e.which == 13) e.preventDefault();
	});
	
	$resultsDiv.on('click', 'a', function (e) {
		$inputBox.val($(this).text());
		$resultsDiv.slideUp();

		e.preventDefault();
	}).mouseenter(function () { onWrapper = true; }).mouseleave(function () { onWrapper = false; });
};

$.fn.prettySelect = function () {
	$(this).each(function () {
		$select = $(this).wrap('<div class="prettySelect">');
		$prettySelect = $select.parent();
		$prettySelectCurrent = $('<div class="prettySelectCurrent">');
		$prettySelectDropdown = $('<div class="prettySelectDropdown">&nbsp;</div>');
		$prettySelectOptions = $('<ul class="prettySelectOptions">');
		longest = '', current = '';
		numOptions = $select.find('option').each(function () {
			if ($(this).val() == $select.val()) current = $(this).text();
			if ($(this).text().length > longest.length) longest = $(this).text();
			$('<li>').data('value', $(this).val()).text($(this).text()).appendTo($prettySelectOptions);
		}).length;
		if (current == '') current = $select.find('option:first').text();
		$select.hide();
		$prettySelect.append($prettySelectCurrent).append($prettySelectDropdown).append($prettySelectOptions);
		$prettySelectCurrent.text(longest).width($prettySelectCurrent.width());
		if (numOptions > 8) {
			$prettySelectOptions.height($prettySelectCurrent.outerHeight() * 5);
		}
		$prettySelect.width($prettySelect.width());
		$prettySelectOptions.width($prettySelect.outerWidth() - 2).hide();
		$prettySelectCurrent.text(current);

		$prettySelectCurrent.add($prettySelectDropdown).click(function (e) {
			e.stopPropagation();
			$(this).parent().addClass('open');
			$(this).parent().find('.prettySelectOptions').show();
		});
		$prettySelectOptions.children('li').click(function () {
			$parent = $(this).closest('.prettySelect');
			$parent.removeClass('open');
			$parent.find('.prettySelectCurrent').text($(this).text());
			$parent.find('.prettySelectOptions').hide();
			$parent.find('select').val($(this).data('value'));
		});
	});

	$('html').click(function () {
		$('.prettySelect').removeClass('open').find('.prettySelectOptions').hide();
	});
};

(function ($) {
	$.fn.prettyCheckbox = function () {
		$(this).each(function () {
			$(this).wrap('<div class="prettyCheckbox"></div>');
			if ($(this).is(':checked')) $(this).parent().addClass('checked');
			if ($(this).data('disabled') == 'disabled') $(this).parent().addClass('disabled');
		}).hide().change(function (e) {
			$(this).parent().toggleClass('checked');
		});
	}

	toggleCheckbox = function(e) {
		if (!$(this).hasClass('disabled')) {
			$(this).toggleClass('checked');
			$checkbox = $(this).find('input');
			$checkbox.prop('checked', !$checkbox.prop('checked'));
		}
	}

	$('body').on('click', '.prettyCheckbox', toggleCheckbox);
})(jQuery);