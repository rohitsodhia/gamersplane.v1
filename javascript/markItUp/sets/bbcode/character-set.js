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
		{
			name: 'Image', replaceWith: '[img][![Url]!][/img]',
			dropMenu: [
				{ name: 'By URL...', replaceWith: '[img][![Url]!][/img]' },
				{ name: 'Upload to Imgur...', closeWith: function (markItUp) { imgurUpload(markItUp); } }
			]
		},
		{name:'Link',  key:'K', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...'},
		{separator:'---------------' },
		{name:'Note', openWith:'[note="[![User(s)]!]"]', closeWith:'[/note]'},
		{name:'Spoiler', openWith:'[spoiler="[![Tag]!]"]', closeWith:'[/spoiler]'},
		{name:'Code',
        dropMenu :[
			{name:'Snippet', openWith:'[snippet="[![Snippet title]!]"]', closeWith:'[/snippet]' },
			{name:'Abilities', openWith:'[abilities="Abilities"]\n# Example heading 1\nExample ability 1 description\nSupports multiline and BBCode\n# Example heading 2\nExample ability 2 description\n\n', closeWith:'[/abilities]' },
			{name:'NPCs', openWith:'[npcs="NPC list"]\nExample NPC | https://gamersplane.com/ucp/avatars/avatar.png\n', closeWith:'[/npcs]' },
			{name:'Table', openWith:'[table]\n', closeWith:'\n[/table]' },
			{name:'Roll Table', openWith:'[table="rolls"]\nName | Roll\ne.g. Percentile | 1d100', closeWith:'\n[/table]' },
			{name:'Two columns', openWith:'[2column]\n[col]\n', closeWith:'\n[/col]\n[col]\n[/col]\n[/2column]' },
		]},
	]
};
