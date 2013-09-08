$(function () {
	leftSpacing = $('h1 .dlWing').css('borderRightWidth');
	$('#topLinkDiv').css({ 'margin': '0 ' + leftSpacing });
	$('#administrateLink').css({ 'marginRight': leftSpacing });
	$('#rules, #breadcrumbs').css({ 'marginLeft': leftSpacing, 'marginRight': leftSpacing });

	leftSpacing = $('.hbDark .dlWing').css('borderRightWidth').slice(0, -2) - 1;
	$('.sudoTable').css({ 'marginLeft': leftSpacing - 1, 'marginRight': leftSpacing - 1 });
	$('h2, #newThread > a').css('marginLeft', leftSpacing + 'px');
});