$(function () {
	leftSpacing = $('h1 .dlWing').css('borderRightWidth');
	$('#topLinkDiv').css({ 'margin': '0 ' + leftSpacing });
	$('#administrateLink').css({ 'marginRight': leftSpacing });
	$('#rules, #breadcrumbs').css({ 'marginLeft': leftSpacing, 'marginRight': leftSpacing });

	leftSpacing = parseInt($('.hbDark .dlWing').css('borderRightWidth').slice(0, -2));
	$('h2, #newThread > a').css('marginLeft', leftSpacing + 'px');
});