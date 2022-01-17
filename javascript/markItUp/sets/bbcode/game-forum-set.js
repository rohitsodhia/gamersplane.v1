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

var notesdropDown = $('#playerList li').map(function () { return { name: $.trim($(this).text()),className:'mobileKeepOpen' }; }).get();
notesdropDown.push({ name: 'Add note', className: 'playerNoteAdd' });
notesdropDown.push({ name: 'Private', className: 'playerPrivateAdd' });
notesdropDown.push({ name: 'Mention', className: 'playerMention' });

mySettings = {
	previewParserPath: '', // path to your BBCode parser
	afterInsert:function(){
		$('textarea.markItUp').trigger('input').trigger('change');
	},
	markupSet: [
		{ name: 'Bold', key: 'B', openWith: '[b]', closeWith: '[/b]' },
		{ name: 'Italic', key: 'I', openWith: '[i]', closeWith: '[/i]' },
		{ name: 'Underline', key: 'U', openWith: '[u]', closeWith: '[/u]' },
		{ name: 'Strikethrough', openWith: '[s]', closeWith: '[/s]' },
		{ name: 'Line break', openWith: '\n[linebreak]\n' },
		{
			name: 'Color', openWith: '[color="[![Text color]!]"]', closeWith: '[/color]',
			dropMenu: [
				{ name: 'Red', openWith: '[color="red"]', closeWith: '[/color]' },
				{ name: 'Blue', openWith: '[color="blue"]', closeWith: '[/color]' },
				{ name: 'Green', openWith: '[color="green"]', closeWith: '[/color]' }
			]
		},
		{ separator: '---------------' },
		{
			name: 'Image', replaceWith: '[img][![Url]!][/img]',
			dropMenu: [
				{ name: 'By URL...', replaceWith: '[img][![Url]!][/img]' },
				{ name: 'Upload to Imgur...', closeWith: function (markItUp) { imgurUpload(markItUp); } },
				{ name: 'YouTube...', replaceWith: '[youtube][![YouTube share link]!][/youtube]' },
				{ name: 'OtFBM', replaceWith: '[map]\nhttps://otfbm.io\n\n--Map size, cell size, and panning\n/26x14/@c60/b5:10x6\n\n--Tokens\n/f7-Alice\n/h8-Bob\n\n--Background image\n?bg=https://i.imgur.com/jIpAjkT.jpg\n[/map]' },
				{ name: 'Zoom map...', replaceWith: '[zoommap="[![Url]!]"]\n[/zoommap]' }
			]
		},
		{ separator: '---------------' },
		{ name: 'Link', key:'K', openWith: '[url=[![Url]!]]', closeWith: '[/url]', placeHolder: 'Your text to link here...' },
		{ separator: '---------------' },

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
*/		{ name: 'Quotes', openWith: '[quote]', closeWith: '[/quote]' },
		{
			name: 'Note', className: 'playerNoteSelector', openWith: '[note="[![User(s)]!]"]', closeWith: '[/note]',
			dropMenu: notesdropDown
		},
		{ name: 'Out of Character', openWith: '[ooc]', closeWith: '[/ooc]' },/*
		{name:'Code', openWith:'[code]', closeWith:'[/code]'},
		{separator:'---------------' },
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{name:'Preview', className:"preview", call:'preview' }*/
		{ name: 'Spoiler', openWith: '[spoiler="[![Tag]!]"]', closeWith: '[/spoiler]' },
	]
};

mobileifyMenus(mySettings);

$(function () {
	$('body').on('click', '.markItUpButton10 ul li:not(.playerNoteAdd):not(.playerPrivateAdd):not(.playerMention):not(.otherNoteAdd)', function (e) {
		$(this).toggleClass('playerNoteSelected');
		e.stopPropagation();
		e.preventDefault();
	});

	$('body').on('click', '.markItUpButton10 ul li.playerNoteAdd,.markItUpButton10 ul li.playerPrivateAdd', function (e) {
		var selectedPlayers = $('.markItUpButton10 ul li.playerNoteSelected').map(function () { return $.trim($(this).text()); }).get().join();
		if($(this).hasClass('playerPrivateAdd')){
			$.markItUp({ openWith: '[private="' + selectedPlayers + '"]', closeWith: '[/private]' });
		} else {
			$.markItUp({ openWith: '[note="' + selectedPlayers + '"]', closeWith: '[/note]' });
		}
		$('.markItUpButton10 ul li.playerNoteSelected').removeClass('playerNoteSelected');
	});

	$('body').on('click', '.markItUpButton10 ul li.playerMention', function (e) {
		var selectedPlayers = $('.markItUpButton10 ul li.playerNoteSelected').map(function () { return '@'+$.trim($(this).text()); }).get().join()+' ';
		$.markItUp({ openWith: selectedPlayers , closeWith: ' ' });
		$('.markItUpButton10 ul li.playerNoteSelected').removeClass('playerNoteSelected');
	});
});