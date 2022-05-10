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
		{ name: 'Format', className:'miuBtnFormat'},
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
				{ name: 'Upload to Imgur...', closeWith: function (markItUp) { imgurUpload(markItUp); } }
			]
		},
		{ name: 'Table', className:'miuBtnTable'},
		{name:'Link',  key:'K', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...', className:'miuBtnLink'},
		{separator:'---------------' , className:'mobileSeparator'},
		{name:'Note', openWith:'[note="[![User(s)]!]"]', closeWith:'[/note]', className:'miuBtnNote'},
		{name:'Spoiler', openWith:'[spoiler="[![Tag]!]"]', closeWith:'[/spoiler]', className:'miuBtnSpoiler'},
		{name:'Code', className:'miuBtnCode',
        dropMenu :[
			{name:'Snippet', openWith:'[snippet="[![Snippet title]!]"]', closeWith:'[/snippet]' },
			{name:'Abilities', openWith:'[abilities="Abilities"]\n# Heading\n\n', closeWith:'[/abilities]' },
			{name:'NPCs', openWith:'[npcs="NPC list"]\nExample NPC | https://gamersplane.com/ucp/avatars/avatar.png\n', closeWith:'[/npcs]' },
			{name:'Two columns', openWith:'[2column]\n[col]\n', closeWith:'\n[/col]\n[col]\n[/col]\n[/2column]' },
			{name:'Form field', key:'M', openWith:'[_=', closeWith:']' },
		]},
	]
};

mobileifyMenus(mySettings);