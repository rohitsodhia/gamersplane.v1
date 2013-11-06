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

(function ($) {
	$.fn.prettySelect = function (options) {
		var $selects = $(this);

		init();

		if (options == 'render') $selects.filter(function () { return !$(this).has('.rendered'); }).each(function () { updateOptions($(this).parent()); });
		if (options == 'updateOptions') $selects.each(function () { updateOptions($(this).parent()); });

		function init() {
			$selects.filter(function () { return $(this).parent('div.prettySelect').length != 1; }).each(function () {
				$select = $(this).wrap('<div class="prettySelect">');
				$prettySelect = $select.parent();
				$prettySelectCurrent = $('<div class="prettySelectCurrent">');
				$prettySelectLongest = $('<div class="prettySelectLongest">');
				$prettySelectDropdown = $('<div class="prettySelectDropdown">&nbsp;</div>');
				$prettySelectOptions = $('<ul class="prettySelectOptions">');

				$prettySelectCurrent.add($prettySelectDropdown).click(function (e) {
					e.stopPropagation();
					$prettySelect = $(this).parent(),
					$prettySelectOptions = $prettySelect.find('.prettySelectOptions'),
					numOptions = $prettySelect.find('option').length;

					$prettySelect.addClass('open');
					if (numOptions > 8) $prettySelectOptions.height($prettySelect.find('.prettySelectLongest').outerHeight() * 5);
					else $prettySelectOptions.height($prettySelect.find('.prettySelectLongest').outerHeight() * numOptions);
					$prettySelectOptions.width($(this).parent().outerWidth() - 2).show();
				});
				$prettySelectOptions.on('click', 'li', function () {
					$parent = $(this).closest('div.prettySelect');
					$parent.removeClass('open');
					$parent.find('.prettySelectOptions').hide();
					$parent.find('select').val($(this).data('value')).change();
				});
				$select.hide();
				$prettySelect.append($prettySelectCurrent).append($prettySelectLongest).append($prettySelectDropdown).append($prettySelectOptions);

				updateOptions($prettySelect);
			}).change(function () {
				$parent = $(this).closest('div.prettySelect');
				$parent.find('.prettySelectCurrent').text($(this).find('option[value=' + $(this).val() + ']').text());
			});
		}

		function updateOptions($prettySelect) {
			$select = $prettySelect.find('select');
			$prettySelectCurrent = $prettySelect.find('.prettySelectCurrent');
			$prettySelectLongest = $prettySelect.find('.prettySelectLongest');
			$prettySelectDropdown = $prettySelect.find('.prettySelectDropdown');
			$prettySelectOptions = $prettySelect.find('.prettySelectOptions');
			longest = '', current = '';
			$prettySelectOptions.html('');
			$prettySelect.find('option').each(function () {
				if ($(this).val() == $select.val()) current = $(this).text();
				if ($(this).text().length > longest.length) longest = $(this).text();
				$('<li>').data('value', $(this).val()).text($(this).text()).appendTo($prettySelect.find('.prettySelectOptions'));
			});
			if (current == '') current = $select.find('option:first').text();
			$prettySelectLongest.text(longest);
			$prettySelectCurrent.text(current);
			$prettySelectCurrent.text(current);
		}

		$('html').click(function () {
			$('div.prettySelect').removeClass('open').find('.prettySelectOptions').hide();
		});
	};
}(jQuery));

$.fn.prettyCheckbox = function () {
	$(this).each(function () {
		$(this).wrap('<div class="prettyCheckbox"></div>');
		if ($(this).is(':checked')) $(this).parent().addClass('checked');
		if ($(this).data('disabled') == 'disabled') $(this).parent().addClass('disabled');
	}).hide().change(function (e) {
		$(this).parent().toggleClass('checked');
	});
};

toggleCheckbox = function(e) {
	if (!$(this).hasClass('disabled')) {
		$(this).toggleClass('checked');
		$checkbox = $(this).find('input');
		$checkbox.prop('checked', !$checkbox.prop('checked'));
	}
}

$('body').on('click', '.prettyCheckbox', toggleCheckbox);