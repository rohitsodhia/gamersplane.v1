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
	else if ($container.hasClass('fancyButton')) baseClass = 'fancyButton';
	else if ($container.hasClass('wingDiv')) baseClass = 'wingDiv';
	if (element == 'a' && baseClass == 'fancyButton' || baseClass != 'fancyButton') $content = $container.children('div:not(.wing)');
	else $content = $container.children('button');
	$content.height('auto');

	var height = $container.outerHeight()/* + 2*/;
	var width = Math.ceil(height * ($container.data('ratio') == undefined?.6:Number($container.data('ratio'))));
	$container.data('height', height);
	$container.data('width', width);
	$content.css('margin', '0 ' + width + 'px').outerHeight($content.outerHeight());
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
	$.post('/tools/ajax/dice', { dice: dice, rerollAces: rerollAces }, function (data) {
		$('#fm_diceRoller .newestRolls').removeClass('newestRolls');
		var first = true;
		var classes = '';
		$('<div>').addClass('newestRolls').prependTo('#fm_diceRoller .floatRight');
		$(data).find('roll').each(function() {
			if ($(this).find('total').text() != '') $('#fm_diceRoller .newestRolls').html($(this).find('dice').text() + '<br>' + $(this).find('indivRolls').text() + ' = ' + $(this).find('total').text());
			else $('<p class="error">Sorry, there was some error. We don\'t let you roll d1s... the answer\'s 1 anyway, and you need to roll a positive number of dice.</p>').appendTo('.newestRolls');
		});
		$('#fm_diceRoller .newestRolls').slideDown(400);
	});
}