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

function convertTZ(dtString, parseString, displayString) {
	parseString = typeof parseString !== 'undefined'?parseString:'MMM D, YYYY h:mm a';
	displayString = typeof displayString !== 'undefined'?displayString:'MMM D, YYYY h:mm a';

	utcDT = moment.utc(dtString, parseString);
	return utcDT.local().format(displayString);
}

function addCSSRule(selector, rules, index) {
	if ('insertRule' in jsCSSSheet) jsCSSSheet.insertRule(selector + "{" + rules + "}", index);
	else if ('addRule' in jsCSSSheet) jsCSSSheet.addRule(selector, rules, index);
}

function setupWingContainer() {
	element = this.nodeName.toLowerCase();
	if ($(this).hasClass('headerbar')) baseClass = 'headerbar';
	else if ($(this).hasClass('fancyButton')) baseClass = 'fancyButton';
	else if ($(this).hasClass('wingDiv')) baseClass = 'wingDiv';
	classes = $(this).attr('class');
	modClasses = new Array();
	modClasses['fancyButton'] = new Array('smallButton', 'disabled');
	modClasses['headerbar'] = new Array('hb_hasButton', 'hb_hasList');
	hasDark = $(this).hasClass('hbDark')?true:false;
	currentID = this.id;
	if ((element == 'a' && baseClass == 'fancyButton' || baseClass != 'fancyButton') && baseClass != 'wingDiv') {
		$(this).css('background', 'none').attr('class', baseClass).wrapInner('<div>').children().attr('class', classes).removeClass(baseClass);
		if (typeof modClasses[baseClass] !== 'undefined') {
			for (key in modClasses[baseClass]) {
				modClass = modClasses[baseClass][key];
				if (classes.match(new RegExp(modClass))) $(this).addClass(modClass).children().removeClass(modClass);
			}
		}
//		if (baseClass == 'headerbar' && (classes.match(/hb_hasButton/) || classes.match(/hb_hasList/))) $(this).addClass
	} else if (baseClass == 'fancyButton') {
		$(this).wrap('<div></div>').removeClass(baseClass).parent().attr('class', baseClass);
		if (currentID.length) $(this).parent().attr('id', 'ww_' + currentID);
		if (typeof modClasses[baseClass] !== 'undefined') {
			for (key in modClasses[baseClass]) {
				modClass = modClasses[baseClass][key];
					if (classes.match(new RegExp(modClass))) $(this).removeClass(modClass).parent().addClass(modClass);
			}
		}
	}

	if (element != 'a' && baseClass == 'fancyButton') wingMargins($(this).parent()[0]);
	else wingMargins(this);
	if (hasDark) $(this).addClass('hbDark');
	wings = '';
	if (baseClass != 'wingDiv' && (element == 'a' && baseClass == 'fancyButton' || baseClass != 'fancyButton')) $('<div class="wing dlWing"></div><div class="wing urWing"></div>').appendTo(this);
	else if (baseClass == 'fancyButton') $('<div class="wing dlWing"></div><div class="wing urWing"></div>').appendTo($(this).parent());
}

function wingMargins(container) {
	element = container.nodeName.toLowerCase();
	$container = $(container);
	if ($container.hasClass('headerbar')) baseClass = 'headerbar';
	else if ($container.hasClass('fancyButton')) {
		baseClass = 'fancyButton';
		if ($container.children('button').length) $container.height('auto').children('button').height('auto');
	} else if ($container.hasClass('wingDiv')) baseClass = 'wingDiv';
	if (element == 'a' && baseClass == 'fancyButton' || baseClass != 'fancyButton') $content = $container.children('div:not(.wing)');
	else $content = $container.children('button');

	var height = $container.outerHeight()/* + 2*/;
	var width = Math.ceil(height * ($container.data('ratio') == undefined?.6:Number($container.data('ratio'))));
	console.log(height);
	$container.data('height', height).data('width', width);
	$content.css('margin', '0 ' + width + 'px');
	if ($container.hasClass('headerbar') || $container.hasClass('fancyButton') || $container.attr('id') != 'controls') {
		$container.css({'height': height + 'px', 'overflow-y': 'hidden'});;
		$content.outerHeight(height + 1);
	}
	$container.children('.wing').each(setupWings);
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

function fm_rollDice(dice, rerollAces) {
	rerollAces = typeof rerollAces == 'undefined' ? 0 : rerollAces;
	$.post('/tools/process/dice', { rollType: 'basic', dice: dice, rerollAces: rerollAces }, function (data) {
		$('#fm_diceRoller .newestRolls').removeClass('newestRolls');
		var first = true;
		var classes = '';
		$(data).addClass('newestRolls').prependTo('#fm_diceRoller .floatRight');
		$('#fm_diceRoller .newestRolls').slideDown(400);
	});
}

function sumRow() {
	if ($(this).hasClass('dontAdd')) return false;

	var inputTotal = 0;
	console.log($(this));
	$parent = $(this).closest('.sumRow');
	$parent.find('input[type="text"]').not('.dontAdd').each(function () { inputTotal += parseInt($(this).val()); });
	$total.each(function () {
		var $indivTotal = $(this);
		var classes = $indivTotal.attr('class').split(/\s+/);
		var finalTotal = inputTotal;
		$.each(classes, function (index, item) {
			if (item.substring(3, 7) == 'Stat') finalTotal += statBonus[item.split('_')[1]];
			else if (item.substring(3, 6) == 'Int') finalTotal += parseInt(item.split('_')[1]);
			else if (item.substring(3, 6) == 'BAB') finalTotal += parseInt($('#bab').val());
			else if (item.substring(3, 7) == 'Size') finalTotal += size;
			else if (item.substring(3, 8) == 'Level') finalTotal += level;
			else if (item.substring(3, 5) == 'HL') finalTotal += Math.floor(level / 2);
		});

		if ($indivTotal.hasClass('noSign')) $indivTotal.text(finalTotal);
		else $indivTotal.text(showSign(finalTotal));
	});
}