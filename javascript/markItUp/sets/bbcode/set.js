// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
	previewParserPath:	'', // path to your BBCode parser
	markupSet: [
		{name:'Bold', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Italic', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Underline', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{name:'Strikethrough', openWith:'[s]', closeWith:'[/s]'},
		{name:'Line break', openWith:'\n[linebreak]\n'},
		{name:'Color', openWith:'[color="[![Text color]!]"]', closeWith:'[/color]',
		dropMenu :[
			{name:'Red', openWith:'[color="red"]', closeWith:'[/color]' },
			{name:'Blue', openWith:'[color="blue"]', closeWith:'[/color]' },
			{name:'Green', openWith:'[color="green"]', closeWith:'[/color]' }
		]},
		{separator:'---------------' },
		{name:'Image', replaceWith:'[img][![Url]!][/img]'},
		{name:'Link', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...'},
		{separator:'---------------' },
/*		{name:'Size', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
		dropMenu :[
			{name:'Big', openWith:'[size=200]', closeWith:'[/size]' },
			{name:'Normal', openWith:'[size=100]', closeWith:'[/size]' },
			{name:'Small', openWith:'[size=50]', closeWith:'[/size]' }
		]},
		{separator:'---------------' },
		{name:'Bulleted list', openWith:'[list]\n', closeWith:'\n[/list]'},
		{name:'Numeric list', openWith:'[list=[![Starting number]!]]\n', closeWith:'\n[/list]'},
		{name:'List item', openWith:'[*] '},
		{separator:'---------------' },
*/		{name:'Quotes', openWith:'[quote]', closeWith:'[/quote]'},
		{name:'Note', openWith:'[note="[![User(s)]!]"]', closeWith:'[/note]'},
		{name:'Out of Character', openWith:'[ooc]', closeWith:'[/ooc]'},/*
		{name:'Code', openWith:'[code]', closeWith:'[/code]'},
		{separator:'---------------' },
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{name:'Preview', className:"preview", call:'preview' }*/
		{name:'Spoiler', openWith:'[spoiler="[![Tag]!]"]', closeWith:'[/spoiler]'},
	]
};

window.onload = function () {
	var spoilers = document.getElementsByClassName('spoiler');
	for (var count = 0; count < spoilers.length; count++) {
		var element = spoilers[count].getElementsByClassName('tag');
		element[0].addEventListener('click', function () {
			var spoiler = this.parentNode,
				classes = spoiler.className.match(/\S+/g) || [],
				index = classes.indexOf('closed');

			if (index >= 0) {
				classes.splice(index, 1);
			} else {
				classes.push('closed');
			}
			spoiler.className = classes.join(' ');
		});
	}
};
