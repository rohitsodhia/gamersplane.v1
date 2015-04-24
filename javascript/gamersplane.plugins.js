$.fn.autocomplete = function (pathOption, sendData) {
	function search(pathOption, sendData, $resultsDiv) {
		$.post(pathOption, sendData, function (data) {
			if (data.length > 0) {
				$inputBox.parent().addClass('open');
				$resultsDiv.html(data).slideDown();
			} else {
				$resultsDiv.slideUp(function () { $inputBox.parent().removeClass('open'); });
			}
		});
	}

	var $inputBox = $(this), onWrapper = false, searchTimeout;
	if ($inputBox.parent().hasClass('autocompleteWrapper')) return $inputBox.parent();
	$inputBox.attr('autocomplete', 'off');
	$inputBox.wrap('<div class="autocompleteWrapper"></div>');
	if ($inputBox.attr('id') && $inputBox.attr('id').length) $inputBox.parent().attr('id', $inputBox.attr('id') + 'Wrapper');
	var $resultsDiv = $('<div class="autocompleteResultsWrapper"><div class="autocompleteResults"></div></div>').css({ top: ($inputBox.outerHeight(false) - 1) + 'px', left: 0, width: $inputBox.outerWidth(false) + 'px' }).appendTo($inputBox.parent()).find('.autocompleteResults');
	$inputBox.keyup(function () {
		if ($resultsDiv.parent().css('top') == '-1px') $resultsDiv.parent().css('top', ($inputBox.outerHeight(false) - 1) + 'px');
		if ($(this).val().length >= 3 && $(this).val() != $(this).data('placeholder')) {
			$.extend(sendData, { search: $(this).val() });
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(function () { search(pathOption, sendData, $resultsDiv); }, 500);
		} else {
			$resultsDiv.slideUp(function () { $inputBox.parent().removeClass('open'); });
		}
	}).blur(function () {
		if (onWrapper == false) {
			$resultsDiv.slideUp(function () { $inputBox.parent().removeClass('open'); });
		}
	}).focus(function () {
		if ($resultsDiv.find('a').size() > 0 && $(this).val().length >= 3) {
			$inputBox.addClass('open');
			$resultsDiv.slideDown();
		}
	}).keypress(function (e) {
		if (e.which == 13) e.preventDefault();
	});
	
	$resultsDiv.on('click', 'a', function (e) {
		$inputBox.val($(this).text());
		$resultsDiv.slideUp(function () { $inputBox.parent().removeClass('open'); });

		e.preventDefault();
	}).mouseenter(function () { onWrapper = true; }).mouseleave(function () { onWrapper = false; });

	return $inputBox.parent();
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
				if ($select.attr('id') && $select.attr('id').length > 0) $prettySelect.attr('id', 'ps_' + $select.attr('id'));
//				if ($select.attr('class') && $select.attr('class').length > 0) $prettySelect.attr('class', $select.attr('class')).removeClass('prettySelect');
				$prettySelectCurrent = $('<div class="prettySelectCurrent">');
				$prettySelectLongest = $('<div class="prettySelectLongest">');
				$prettySelectDropdown = $('<div class="prettySelectDropdown">&nbsp;</div>');
				$prettySelectOptions = $('<ul class="prettySelectOptions">');

				$prettySelectCurrent.add($prettySelectDropdown).click(function (e) {
					e.stopPropagation();
					$prettySelect = $(this).parent(),
					$prettySelectOptions = $prettySelect.find('.prettySelectOptions'),
					numOptions = $prettySelect.find('option').length;

					if (numOptions > 8) {
						$prettySelectOptions.height($prettySelect.find('.prettySelectLongest').outerHeight() * 5 + 1).addClass('showScroll');
						console.log('more than 8');
					} else $prettySelectOptions.height($prettySelect.find('.prettySelectLongest').outerHeight() * numOptions + 1);
					$prettySelectOptions.width($(this).parent().outerWidth() - 2).show();
					$prettySelect.addClass('open');
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
				text = '';
				if ($(this).find('option[value="' + $(this).val() + '"]').length) text = $(this).find('option[value="' + $(this).val() + '"]').text();
				else text = $(this).val();
				$parent.find('.prettySelectCurrent').text(text);
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
		}

		$('html').click(function () {
			$('div.prettySelect').removeClass('open').find('.prettySelectOptions').hide();
		});
	};
}(jQuery));

$.fn.prettyCheckbox = function () {
	$(this).each(function () {
		var $checkbox = $(this);
		if ($checkbox.parent('div.prettyCheckbox').length == 0) {
			$checkbox.wrap('<div class="prettyCheckbox"></div>');
			if ($checkbox.is(':checked')) 
				$checkbox.parent().addClass('checked');
			if ($checkbox.data('disabled') == 'disabled') 
				$checkbox.parent().addClass('disabled');
			$checkbox.change(function (e) {
				$checkbox.parent().toggleClass('checked');
			})
		}
	});
};

toggleCheckbox = function (e) {
	if (!$(this).hasClass('disabled')) {
		$(this).toggleClass('checked');
		$checkbox = $(this).find('input');
		$checkbox.prop('checked', !$checkbox.prop('checked'));
	}
}

toggleLinkedCheckbox = function (e) {
	linkedID = $(this).attr('for');
	if ($('#' + linkedID).length) $('#' + linkedID).parent().trigger('click');
}

syncCheckbox = function () {
	$wrapper = $(this), $input = $wrapper.find('input');
	if (($wrapper.hasClass('checked') && !$input.prop('checked')) || (!$wrapper.hasClass('checked') && $input.prop('checked'))) $input.change();
}

$.fn.prettyRadio = function () {
	$(this).each(function () {
		$(this).wrap('<div class="prettyRadio"></div>');
		if ($(this).is(':checked')) $(this).parent().addClass('checked');
	}).hide().change(function (e) {
		$(this).parent().toggleClass('checked');
	});
};

toggleRadio = function (e) {
	if (!$(this).hasClass('checked')) {
		$radio = $(this).find('input');
		radioName = $radio.attr('name');
		$('input[name="' + radioName + '"]').prop('checked', false).parent().removeClass('checked');
		$(this).addClass('checked');
		$radio.prop('checked', true);
	}
}

syncRadio = function () {
	$wrapper = $(this), $input = $wrapper.find('input');
	if (($wrapper.hasClass('checked') && !$input.prop('checked')) || (!$wrapper.hasClass('checked') && $input.prop('checked'))) $input.change();
}

$('body').on('click', '.prettyCheckbox', toggleCheckbox).on('click', 'label', toggleLinkedCheckbox).on('click', '.prettyRadio', toggleRadio);

$.fn.prettify = function () {
	$(this).find('select').prettySelect();
	$(this).find('input[type="checkbox"]').prettyCheckbox();
	$(this).find('input[type="radio"]').prettyRadio();

	return $(this);
};

(function ($) {
	$.placeholder = function () {
		$eles = $('input.placeholder');
		$eles.each(setupPlaceholders).trigger('blur');

		return $eles;
	};

	$.fn.placeholder = function () {
		$eles = $(this);
		$eles.each(setupPlaceholders).trigger('blur');

		return $eles;
	};

	function setupPlaceholders() {
		var $input = $(this);
		if ($input.val() == '' || $input.val() == $input.data('placeholder')) $input.addClass('default');
		$input.val(function () { return $input.data('placeholder') == ''?$input.data('placeholder'):$input.val(); }).focus(function () {
			if ($input.val() == $input.data('placeholder') || $input.val() == '') $input.val('').removeClass('default');
		}).blur(function () {
			if ($input.val() == '') $input.val($input.data('placeholder')).addClass('default');
		}).change(function () {
			if ($input.val() != $input.data('placeholder')) $input.removeClass('default');
			else if ($input.val() == $input.data('placeholder')) $input.addClass('default');
		});
	}
}(jQuery));