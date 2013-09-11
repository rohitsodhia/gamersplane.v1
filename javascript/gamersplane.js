function decToB26(num) {
	var str = '';
	var letterCode;
	while(num > 0) {
		letterCode = 'a'.charCodeAt(0) + (num - 1) % 26;
		num = Math.floor((num - 1) / 26);
		str = String.fromCharCode(letterCode) + str;
	}
	
	return str;
}

function b26ToDec(str) {
	var num = 0;
	for (var count = 0; count < str.length; count++) num += (str[str.length - 1 - count].charCodeAt() - 96) * Math.pow(26, count);
	
	return num;
}

function rgb2hex(rgb) {
	rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function hex(x) {
	return ("0" + parseInt(x).toString(16)).slice(-2);
}

function showSign(val) {
	if (val >= 0) return '+' + val;
	else return val;
}

jQuery.fn.autocomplete = function (pathOption, sendData) {
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
}

function updateSaves(save) {
	var total = 0;
	if (save.substring(0, 1) == 'f') { save = 'fort'; total = parseInt($('#conModifier').text()); }
	else if (save.substring(0, 1) == 'r') { save = 'ref'; total = parseInt($('#dexModifier').text()); }
	else if (save.substring(0, 1) == 'w') { save = 'will'; total = parseInt($('#wisModifier').text()); }
	$('#' + save +'Row input').each(function () { total += $(this).val().length?parseInt($(this).val()):0; });
	$('#' + save + 'Total').text(showSign(total));
}

function updateCombatBonuses() {
	var initTotal = parseInt($('#dexModifier').text());
	var meleeTotal = parseInt($('#strModifier').text()) + parseInt($('#size').val()) + parseInt($('#bab').val()) + parseInt($('#melee_misc').val());
	var rangedTotal = parseInt($('#dexModifier').text()) + parseInt($('#size').val()) + parseInt($('#bab').val()) + parseInt($('#ranged_misc').val());
	
	$('#init input').each(function () { initTotal += $(this).val().length?parseInt($(this).val()):0; });
	$('#initTotal').text(showSign(initTotal));
//	$('#melee input').each(function () { meleeTotal += $(this).val().length?parseInt($(this).val()):0; });
	$('#meleeTotal').text(showSign(meleeTotal));
//	$('#ranged input').each(function () { rangedTotal += $(this).val().length?parseInt($(this).val()):0; });
	$('#rangedTotal').text(showSign(rangedTotal));
}

function fm_rollDice(dice, rerollAces) {
	rerollAces = typeof rerollAces == 'undefined' ? 0 : rerollAces;
	$.post(SITEROOT + '/tools/ajax/dice', { dice: dice, rerollAces: rerollAces }, function (data) {
		$('#fixedMenu_diceRoller .newestRolls').removeClass('newestRolls');
		var first = true;
		var classes = '';
		$('<div>').addClass('newestRolls').prependTo('#fixedMenu_diceRoller .floatRight');
		$(data).find('roll').each(function() {
			if ($(this).find('total').text() != '') $('<p>' + $(this).find('dice').text() + '<br>' + $(this).find('indivRolls').text() + ' = ' + $(this).find('total').text() +'</p>').appendTo('#fixedMenu_diceRoller .newestRolls');
			else $('<p class="error">Sorry, there was some error. We don\'t let you roll d1s... the answer\'s 1 anyway, and you need to roll a positive number of dice.</p>').appendTo('.newestRolls');
		});
		$('#fixedMenu_diceRoller .newestRolls').slideDown(400, function() { $(this).find('p').animate({ backgroundColor: '#000' }, 200); });
	});
}

function setupWingContainer() {
	if ($(this).hasClass('headerbar')) baseClass = 'headerbar';
	else if ($(this).hasClass('button')) baseClass = 'button';
	else if ($(this).hasClass('fancyButton')) baseClass = 'fancyButton';
	else if ($(this).hasClass('wingDiv')) baseClass = 'wingDiv';
	classes = $(this).attr('class');
	hasDark = $(this).hasClass('hbDark')?true:false;
	currentID = this.id;
	if (baseClass != 'fancyButton' && baseClass != 'wingDiv') {
		$(this).css('background', 'none').attr('class', baseClass).wrapInner('<div>').children().attr('class', classes).removeClass(baseClass);
		if (baseClass == 'button' && classes.match(/smallButton/)) $(this).addClass('smallButton').children().removeClass('smallButton');
	} else if (baseClass == 'fancyButton') {
		$(this).wrap('<div></div>').removeClass(baseClass).parent().attr('class', baseClass);//.attr('id', currentID + 'Wrapper');
	}

	if (baseClass == 'fancyButton') wingMargins($(this).parent());
	else wingMargins(this);
	if (hasDark) $(this).addClass('hbDark');
	wings = '';
	if (baseClass != 'wingDiv' && baseClass != 'fancyButton') $('<div class="wing dlWing"></div><div class="wing urWing"></div>').appendTo(this);
	else if (baseClass == 'fancyButton') $('<div class="wing dlWing"></div><div class="wing urWing"></div>').appendTo($(this).parent());
}

function wingMargins(container) {
	if ($(container).hasClass('headerbar')) baseClass = 'headerbar';
	else if ($(container).hasClass('button')) baseClass = 'button';
	else if ($(container).hasClass('fancyButton')) baseClass = 'fancyButton';
	else if ($(container).hasClass('wingDiv')) baseClass = 'wingDiv';
	if (baseClass != 'fancyButton') $content = $(container).children('div:not(.wing)');
	else $content = $(container).children('button');
	$content.height('auto');

	var height = $(container).outerHeight()/* + 2*/;
	var width = Math.ceil(height * ($(container).data('ratio') == undefined?.6:Number($(container).data('ratio'))));
	$(container).data('height', height);
	$(container).data('width', width);
	$content.css('margin', '0 ' + width + 'px').outerHeight($content.outerHeight());
	$(container).children('.wing').each(setupWings);
}

function setupWings() {
	height = $(this).parent().data('height');
	width = $(this).parent().data('width');
	if ($(this).hasClass('dlWing')) bCSS = { 'borderTopWidth': height, 'borderRightWidth': width };
	else if ($(this).hasClass('urWing')) bCSS = { 'borderTopWidth': height, 'borderRightWidth': width };
	else if ($(this).hasClass('drWing')) bCSS = { 'borderTopWidth': height, 'borderLeftWidth': width };
	else if ($(this).hasClass('ulWing')) bCSS = { 'borderTopWidth': height, 'borderLeftWidth': width };
	$(this).css(bCSS);
}

$(function() {
	$('.loginLink').colorbox({ href: function () { return this.href + '?modal=1' }, iframe: true, innerWidth: '450px', innerHeight: '240px' });

	$('.placeholder').each(function () {
		$(this).val($(this).data('placeholder')).addClass('default').focus(function () {
			if ($(this).val() == $(this).data('placeholder')) $(this).val('').removeClass('default');
		}).blur(function () {
			if ($(this).val() == '') $(this).val($(this).data('placeholder')).addClass('default');
		});
	});

	if ($('body').hasClass('modal')) parent.$.colorbox.resize({ 'innerHeight': $('body').height()} );

	$('.headerbar, a.button, .fancyButton, .wingDiv').each(setupWingContainer);
	$('.wing').each(setupWings);
	if ($('.hbDark .wing').length) {
		leftMargin = $('.hbDark .wing').css('border-right-width');
		$('.hbMargined:not(textarea)').css({ 'margin-left': leftMargin, 'margin-right': leftMargin });
		$('.hbTopper').css({ 'marginLeft': leftMargin });

		leftMargin = leftMargin.slice(0, -2);
		$('textarea.hbMargined').each(function () {
			tWidth = $(this).parent().width();
			$(this).css({ 'margin-left': leftMargin + 'px', 'margin-right': leftMargin + 'px', 'width': (tWidth - 2 * leftMargin) + 'px' });
		});
	}

	$('#mainMenu li').mouseenter(function () {
		$(this).children('ul').stop(true, true).slideDown();
	}).mouseleave(function () {
		$(this).children('ul').stop(true, true).slideUp();
	}).find('ul').each(function () {
		$(this).css('minWidth', $(this).parent().width());
	});

	if ($('#fixedMenu').size()) {
		var $fixedMenu = $('#fixedMenu_window');
		$('html').click(function () {
			$fixedMenu.find('.openMenu').removeClass('openMenu').slideUp(250);
		});
		
		$fixedMenu.click(function (e) { e.stopPropagation(); })
		$fixedMenu.data('currentlyOpen', '');
		$fixedMenu.data('currentlyOpenGroup', '');
		$fixedMenu.find('.subMenu > a, .subRootMenu > a').click(function (e) {
			e.stopPropagation();
			if ($(this).parent().attr('id') != $fixedMenu.data('currentlyOpen')) {
				clickedMenu = $(this).data('menuGroup');
				if (clickedMenu != $fixedMenu.data('currentlyOpenGroup')) { $fixedMenu.children('.subMenu').children('a').each(function () {
					if ($(this).data('menuGroup') == $fixedMenu.data('currentlyOpenGroup')) $(this).parent().find('.fixedMenu_window').removeClass('openMenu').slideUp(250);
				}); }
				$(this).parent().children('.fixedMenu_window').addClass('openMenu').slideDown(250);
				if ($(this).data('menuGroup') != $fixedMenu.data('currentlyOpenGroup')) $fixedMenu.data('currentlyOpenGroup', $(this).data('menuGroup'));
				$fixedMenu.data('currentlyOpen', $(this).parent().attr('id'));
			} else {
				$(this).parent().find('.fixedMenu_window').removeClass('openMenu').slideUp(250);
				if ($(this).parent().hasClass('subMenu')) $fixedMenu.data('currentlyOpenGroup', '');
				
				$fixedMenu.data('currentlyOpen', '');
			}
			
			e.preventDefault();
		});
		
		
		$('#fixedMenu_diceRoller > a').click(function() {
			$('#fixedMenu_cards .fixedMenu_window').slideUp();
		});
		
		$('#fixedMenu_cards > a').click(function() {
			$('#fixedMenu_diceRoller .fixedMenu_window').slideUp();
		});
		
		$('#fm_roll').click(function (e) {
			e.stopPropagation();
			var dice = $('#customDiceRoll input').val();
			if (dice != '') fm_rollDice(dice);
			
			e.preventDefault();
		});
		
		$('#fixedMenu_diceRoller input').keypress(function (e) {
			if (e.which == 13) {
				var dice = $(this).val();
				if (dice != '') fm_rollDice(dice);
				
				e.preventDefault();
			}
		}).click(function (e) { e.stopPropagation(); });
		
		$('#fixedMenu_diceRoller .indivDice button').click(function (e) {
			e.stopPropagation();
			var dice = '1' + $(this).attr('name');
			if (dice != '') fm_rollDice(dice);
		});
	}

	$('.cbf_basic').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			$('.cbf_basic .required').each(function () {
				if ($(this).val().length == 0) return false;
			});

			return true;
		},
		success: function (data) {
			if (data == '1') {
				parent.document.location.reload();
			}
		}
	});


	/* Individual Pages */
	if (!$('body').hasClass('modal')) var curPage = $('#content > div > div').attr('id').substring(5);
	else var curPage = $('body > div').attr('id').substring(5);
});