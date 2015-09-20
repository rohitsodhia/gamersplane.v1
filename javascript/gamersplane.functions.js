$.urlParam = function(name){
	var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	if (results == null) 
		return null;
	else 
		return results[1] || 0;
}

String.prototype.capitalizeFirstLetter = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

String.prototype.printReady = function () {
	return this.replace(/(?\r\n|\r|\n:)/g, '<br>');
}

function removeEle(from, element) {
	var key = from.indexOf(element);
	if (key >= 0) 
		from.splice(key, 1);
}

function copyObject(val) {
	return JSON.parse(JSON.stringify(val));
}

function isUndefined(val) {
	return typeof val === 'undefined'?true:false;
}

function decodeHTML(html) {
	var txt = document.createElement("textarea");
	txt.innerHTML = html;
	return txt.value;
}

function getPathElements() {
	pathElements = window.location.pathname.split('/');
	for (key in pathElements) 
		if (pathElements[key].length == 0) 
			pathElements.splice(key, 1);

	return pathElements;
}

function getModalAngularParent() {
	var appElement = parent.document.querySelector('[ng-app]');
	var $scope = parent.angular.element(appElement).scope();
	return $scope;
}

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
	for (var count = 0; count < str.length; count++) 
		num += (str[str.length - 1 - count].charCodeAt() - 96) * Math.pow(26, count);
	
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
	if (val >= 0) 
		return '+' + val;
	else 
		return val;
}

function convertTZ(dtString, parseString, displayString) {
	parseString = typeof parseString !== 'undefined'?parseString:'MMM D, YYYY h:mm a';
	displayString = typeof displayString !== 'undefined'?displayString:'MMM D, YYYY h:mm a';

	utcDT = moment.utc(dtString, parseString);
	return utcDT.local().format(displayString);
}

function addCSSRule(selector, rules, index) {
	if ('insertRule' in jsCSSSheet) 
		jsCSSSheet.insertRule(selector + "{" + rules + "}", index);
	else if ('addRule' in jsCSSSheet) 
		jsCSSSheet.addRule(selector, rules, index);
}

function skewElement() {
	$element = $(this);
	if ($element.children('div.skewedDiv').length) 
		return;
	if (typeof $element.data('skew') != 'undefined') 
		skewDeg = parseInt($element.data('skew'));
	else 
		skewDeg = -30;
	$skewDiv = $element.wrapInner('<div class="skewedDiv"></div>').children('div');
	skewedOut = Math.tan(Math.abs(skewDeg) * Math.PI / 180) * $element.outerHeight() / 2;
	$element.css({
		'-webkit-transform' : 'skew(' + skewDeg + 'deg)',
		'-moz-transform'    : 'skew(' + skewDeg + 'deg)',
		'-ms-transform'     : 'skew(' + skewDeg + 'deg)',
		'-o-transform'      : 'skew(' + skewDeg + 'deg)',
		'transform'         : 'skew(' + skewDeg + 'deg)',
//		'margin-left'       : Math.ceil(skewedOut) + 'px',
//		'margin-right'      : Math.ceil(skewedOut) + 'px'
	}).data('skewedOut', skewedOut);
	if (parseInt($element.css('margin-left').slice(0, -2)) < Math.ceil(skewedOut)) 
		$element.css('margin-left', Math.ceil(skewedOut) + 'px');
	if (parseInt($element.css('margin-right').slice(0, -2)) < Math.ceil(skewedOut)) 
		$element.css('margin-right', Math.ceil(skewedOut) + 'px');
	$skewDiv.css({
		'-webkit-transform' : 'skew(' + (skewDeg * -1) + 'deg)',
		'-moz-transform'    : 'skew(' + (skewDeg * -1) + 'deg)',
		'-ms-transform'     : 'skew(' + (skewDeg * -1) + 'deg)',
		'-o-transform'      : 'skew(' + (skewDeg * -1) + 'deg)',
		'transform'         : 'skew(' + (skewDeg * -1) + 'deg)',
		'margin-left'       : Math.ceil(skewedOut) + 'px',
		'margin-right'      : Math.ceil(skewedOut) + 'px'
	});
}

function adjustSkewMargins() {
	$element = $(this);
	$skewDiv = $element.children('div');
	if (typeof $element.data('skew') != 'undefined') 
		skewDeg = parseInt($element.data('skew'));
	else 
		skewDeg = -30;
	skewedOut = Math.tan(Math.abs(skewDeg) * Math.PI / 180) * $element.outerHeight() / 2;
	$element.add($skewDiv).css({
		'margin-left'  : skewedOut + 'px',
		'margin-right' : skewedOut + 'px'
	})
}

function trapezoidify() {
	$element = $(this);
	$element.wrapInner('<div class="content"></div>').prepend('<div class="leftWing"></div><div class="rightWing"></div>');
	if (typeof $element.data('skew') != 'undefined') 
		skewDeg = parseInt($element.data('skew'));
	else 
		skewDeg = -30;
	if ($element.hasClass('facingUp')) 
		direction = 'up';
	else 
		direction = 'down';
	sideBorderWidth = Math.ceil(Math.tan(Math.abs(skewDeg) * Math.PI / 180) * $element.outerHeight());
	$element.children('.content').css({
		'margin-left'   : sideBorderWidth + 'px',
		'margin-right'  : sideBorderWidth + 'px',
	});
	$element.children('.leftWing').css({
		'border-left-width' : sideBorderWidth + 'px',
	});
	$element.children('.rightWing').css({
		'border-right-width' : sideBorderWidth + 'px',
	});
	if (direction == 'down') 
		$element.children('.leftWing, .rightWing').css('border-bottom-width', $element.outerHeight() + 'px');
	else 
		$element.children('.leftWing, .rightWing').css('border-top-width', $element.outerHeight() + 'px');
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
	$parent = $(this).closest('.sumRow');
	$parent.find('input[type="text"]').not('.dontAdd').each(function () { inputTotal += parseInt($(this).val()); });
	$parent.find('.total').each(function () {
		var $indivTotal = $(this);
		var classes = $indivTotal.attr('class').split(/\s+/);
		var finalTotal = inputTotal;
		$.each(classes, function (index, item) {
			if (item.substring(3, 7) == 'Stat') 
				finalTotal += statBonus[item.split('_')[1]];
			else if (item.substring(3, 6) == 'Int') 
				finalTotal += parseInt(item.split('_')[1]);
			else if (item.substring(3, 6) == 'BAB') 
				finalTotal += parseInt($('#bab').val());
			else if (item.substring(0, 7) == 'addSize') 
				finalTotal += size;
			else if (item.substring(0, 7) == 'subSize') 
				finalTotal -= size;
			else if (item.substring(3, 8) == 'Level') 
				finalTotal += level;
			else if (item.substring(3, 5) == 'HL') 
				finalTotal += Math.floor(level / 2);
		});

		if ($indivTotal.hasClass('noSign')) 
			$indivTotal.text(finalTotal);
		else 
			$indivTotal.text(showSign(finalTotal));
	});
}