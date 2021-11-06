$.urlParam = function(name){
	var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	if (results === null)
		return null;
	else
		return results[1] || 0;
};

String.prototype.capitalizeFirstLetter = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

/*Array.prototype.removeEle = function (element) {
	var key = this.indexOf(element);
	if (key >= 0)
		this.splice(key, 1);
	return this;
}*/

function removeEle(from, element) {
	var key = from.indexOf(element);
	if (key >= 0)
		from.splice(key, 1);
}

function copyObject(val) {
	if (typeof val === undefined) {
		return val;
	}
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
	for (var key in pathElements)
		if (pathElements[key].length === 0)
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
	if (index === undefined) {
		index = 0;
	}
	if ('insertRule' in jsCSSSheet)
		jsCSSSheet.insertRule(selector + "{" + rules + "}", index);
	else if ('addRule' in jsCSSSheet)
		jsCSSSheet.addRule(selector, rules, index);
}

function skewElement() {
	return;

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
	});
}

function trapezoidify() {
	return;
	$element = $(this);
	$element.wrapInner('<div class="content"></div>').prepend('<div class="leftWing"></div>').append('<div class="rightWing"></div>');
	if (typeof $element.data('skew') != 'undefined')
		skewDeg = parseInt($element.data('skew'));
	else
		skewDeg = -30;
	if ($element.hasClass('facingUp'))
		direction = 'up';
	else
		direction = 'down';
	sideBorderWidth = Math.ceil(Math.tan(Math.abs(skewDeg) * Math.PI / 180) * $element.outerHeight());
	// $element.children('.content').css({
	// 	'margin-left'   : sideBorderWidth + 'px',
	// 	'margin-right'  : sideBorderWidth + 'px',
	// });
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

// load custom game logo and apply styles
var applyPageStyle=function(styleText)
{
	if(styleText)
	{
        try {
			const styleObj=JSON.parse(styleText);
			if(styleObj){
				if(styleObj.background){
					var bodyEle=$('body').addClass('style-background');
					var contentEle=$('#content');
					(!styleObj.background.image)||(contentEle.css({'background-image':'url('+styleObj.background.image+')'}));
					(!styleObj.background.color)||(contentEle.css({'background-color':styleObj.background.color}));
					(!styleObj.background.position)||(contentEle.css({'background-position':styleObj.background.position}));
					(!styleObj.background.size)||(contentEle.css({'background-size':styleObj.background.size}));
				}
				if(styleObj.logo){
					$('#charSheetLogo img').on('load',function(){$(this).parent().show();}).attr('src',styleObj.logo);
				}
			}
			}catch(e){
				//invalid Json
			}

	}
};

var fixDarkThemeColours=function(){
    // http://en.wikipedia.org/wiki/HSL_color_space
    var rgbToHsl= function(r, g, b){
        r /= 255;
        g /= 255;
        b /= 255;
        var max = Math.max(r, g, b);
        var min = Math.min(r, g, b);
        var h, s;
        var l = (0.2989 * r) + (0.587 * g) + (0.114 * b);

        if(max == min){
            h = s = 0; // grey
        }else{
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch(max){
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }

        return {h:h, s:s, l:l};
    };

    var hslToRgb=function(h, s, l){
        var r, g, b;

        if(s == 0){
            r = g = b = l; // grey
        }else{
            function hue2rgb(p, q, t){
                if(t < 0) t += 1;
                if(t > 1) t -= 1;
                if(t < 1/6) return p + (q - p) * 6 * t;
                if(t < 1/2) return q;
                if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                return p;
            }

            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1/3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1/3);
        }

        return {r: (r*255), g:(g*255), b:(b * 255)};
    };

	var invertCssColorAttribute=function(pThis,attribName){
		var curColor=window.getComputedStyle(pThis.get(0), null)[attribName];
		var parts = curColor.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		if(parts && parts.length==4){
			var hsl=rgbToHsl(parts[1],parts[2],parts[3]);
			var rgbNew=hslToRgb(hsl.h, hsl.s, 1.0-hsl.l);
			pThis.css(attribName,'rgb('+rgbNew.r+','+rgbNew.g+','+rgbNew.b+')');
		}
	};

	jQuery.fn.darkModeColorize = function (){
		if($('body').hasClass('dark')){
			$('span.userColor,span.userSize',this).each(function(){
				var pThis=$(this);
				var pEle=pThis.get(0);
				if(pEle.style){
					if((pEle.style.color)&&(!pEle.style.backgroundColor)){
						invertCssColorAttribute(pThis,'color');
					}
					if((pEle.style.backgroundColor)&&(!pEle.style.color)){
						invertCssColorAttribute(pThis,'background-color');
					}
				}
			});
		}
		return this;
	};

	$('body').darkModeColorize();

};