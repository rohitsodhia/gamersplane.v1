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
	afterInsert:function(){
		$('textarea.markItUp').trigger('input').trigger('change');
	},
	markupSet: [
		{name:'Bold', key:'B', openWith:'[b]', closeWith:'[/b]', className:'miuBtnBold'},
		{name:'Italic', key:'I', openWith:'[i]', closeWith:'[/i]', className:'miuBtnItalic'},
		{name:'Underline', key:'U', openWith:'[u]', closeWith:'[/u]', className:'miuBtnUnderline'},
		{name:'Strikethrough', openWith:'[s]', closeWith:'[/s]', className:'miuBtnStrikethrough'},
		{name:'Line break', openWith:'\n[linebreak]\n', className:'miuBtnLinebreak'},
		{name:'Color', openWith:'[color="[![Text color]!]"]', closeWith:'[/color]', className:'miuBtnColor',
		dropMenu :[
			{name:'Red', openWith:'[color="red"]', closeWith:'[/color]' },
			{name:'Blue', openWith:'[color="blue"]', closeWith:'[/color]' },
			{name:'Green', openWith:'[color="green"]', closeWith:'[/color]' }
		]},
		{separator:'---------------' },
		{
			name: 'Image', replaceWith: '[img][![Url]!][/img]', className:'miuBtnImage',
			dropMenu: [
				{ name: 'By URL...', replaceWith: '[img][![Url]!][/img]' },
				{ name: 'Upload to Imgur...', closeWith: function (markItUp) { imgurUpload(markItUp); } },
				{ name: 'YouTube...', replaceWith: '[youtube][![YouTube share link]!][/youtube]' }
			]
		},
		{name:'Link', key:'K', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...', className:'miuBtnLink'},
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
*/		{name:'Quotes', openWith:'[quote]', closeWith:'[/quote]', className:'miuBtnQuote'},
		{name:'Note', openWith:'[note="[![User(s)]!]"]', closeWith:'[/note]', className:'miuBtnNote'},
		{name:'Out of Character', openWith:'[ooc]', closeWith:'[/ooc]', className:'miuBtnOoc'},/*
		{name:'Code', openWith:'[code]', closeWith:'[/code]'},
		{separator:'---------------' },
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{name:'Preview', className:"preview", call:'preview' }*/
		{name:'Spoiler', openWith:'[spoiler="[![Tag]!]"]', closeWith:'[/spoiler]', className:'miuBtnSpoiler'},
	]
};

mobileifyMenus(mySettings);