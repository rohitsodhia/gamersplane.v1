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
		{ name: 'Bold', key: 'B', openWith: '[b]', closeWith: '[/b]', className:'miuBtnBold' },
		{ name: 'Italic', key: 'I', openWith: '[i]', closeWith: '[/i]', className:'miuBtnItalic' },
		{ name: 'Underline', key: 'U', openWith: '[u]', closeWith: '[/u]', className:'miuBtnUnderline' },
		{ name: 'Strikethrough', openWith: '[s]', closeWith: '[/s]', className:'miuBtnStrikethrough' },
		{ name: 'Line break', openWith: '\n[linebreak]\n' , className:'miuBtnLinebreak'},
		{ name: 'Format', className:'miuBtnFormat'},
		{
			name: 'Color', openWith: '[color="[![Text color]!]"]', closeWith: '[/color]', className:'miuBtnColor',
			dropMenu: [
				{ name: 'Red', openWith: '[color="red"]', closeWith: '[/color]' },
				{ name: 'Blue', openWith: '[color="blue"]', closeWith: '[/color]' },
				{ name: 'Green', openWith: '[color="green"]', closeWith: '[/color]' }
			]
		},
		{ separator: '---------------' },
		{
			name: 'Image', replaceWith: '[img][![Url]!][/img]', className:'miuBtnImage',
			dropMenu: [
				{ name: 'By URL...', replaceWith: '[img][![Url]!][/img]' },
				{ name: 'Upload to Imgur...', closeWith: function (markItUp) { imgurUpload(markItUp); } },
				{ name: 'YouTube...', replaceWith: '[youtube][![YouTube share link]!][/youtube]' },
				{ name: 'OtFBM', replaceWith: '[map]\nhttps://otfbm.io\n\n--Map size, cell size, and panning\n/26x14/@c60/b5:10x6\n\n--Tokens\n/f7-Alice\n/h8-Bob\n\n--Background image\n?bg=https://i.imgur.com/jIpAjkT.jpg\n[/map]' },
				{ name: 'Zoom map...', replaceWith: '[zoommap="[![Url]!]"]\n[/zoommap]' }
			]
		},
		{ name: 'Table', className:'miuBtnTable'},
		{ name: 'Poll', className:'miuBtnPoll'},
		{ separator: '---------------' },
		{ name: 'Link', key:'K', openWith: '[url=[![Url]!]]', closeWith: '[/url]', placeHolder: 'Your text to link here...' , className:'miuBtnLink'},
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
*/		{ name: 'Quotes', openWith: '[quote]', closeWith: '[/quote]' , className:'miuBtnQuote'},
		{
			name: 'Note', className: 'playerNoteSelector miuBtnNote', openWith: '[note="[![User(s)]!]"]', closeWith: '[/note]',
			dropMenu: notesdropDown
		},
		{ name: 'Out of Character', openWith: '[ooc]', closeWith: '[/ooc]' , className:'miuBtnOoc'},/*
		{name:'Code', openWith:'[code]', closeWith:'[/code]'},
		{separator:'---------------' },
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{name:'Preview', className:"preview", call:'preview' }*/
		{ name: 'Spoiler', openWith: '[spoiler="[![Tag]!]"]', closeWith: '[/spoiler]' , className:'miuBtnSpoiler'},
	]
};

mobileifyMenus(mySettings);

$(function () {
	$('body').on('click', '.playerNoteSelector ul li:not(.playerNoteAdd):not(.playerPrivateAdd):not(.playerMention):not(.otherNoteAdd)', function (e) {
		$(this).toggleClass('playerNoteSelected');
		e.stopPropagation();
		e.preventDefault();
	});

	$('body').on('click', '.playerNoteSelector ul li.playerNoteAdd,.playerNoteSelector ul li.playerPrivateAdd', function (e) {
		var selectedPlayers = $('.playerNoteSelector ul li.playerNoteSelected').map(function () { return $.trim($(this).text()); }).get().join();
		if($(this).hasClass('playerPrivateAdd')){
			$.markItUp({ openWith: '[private="' + selectedPlayers + '"]', closeWith: '[/private]' });
		} else {
			$.markItUp({ openWith: '[note="' + selectedPlayers + '"]', closeWith: '[/note]' });
		}
		$('.playerNoteSelector ul li.playerNoteSelected').removeClass('playerNoteSelected');
	});

	$('body').on('click', '.playerNoteSelector ul li.playerMention', function (e) {
		var selectedPlayers = $('.playerNoteSelector ul li.playerNoteSelected').map(function () { return '@'+$.trim($(this).text()); }).get().join()+' ';
		$.markItUp({ openWith: selectedPlayers , closeWith: ' ' });
		$('.playerNoteSelector ul li.playerNoteSelected').removeClass('playerNoteSelected');
	});
});