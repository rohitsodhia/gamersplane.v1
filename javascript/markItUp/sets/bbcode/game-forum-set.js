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

var notesdropDown = $('#playerList li').map(function () { return { name: $.trim($(this).text()) }; }).get();
notesdropDown.push({ name: 'Add note', className: 'playerNoteAdd' });
notesdropDown.push({ name: 'Private', className: 'playerPrivateAdd' });

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
			]
		},
		{ separator: '---------------' },
		{ name: 'Link', openWith: '[url=[![Url]!]]', closeWith: '[/url]', placeHolder: 'Your text to link here...' },
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

var ImgurHelper=(function(){

	var uploadFiles=function(file){

		var imgurApi = 'https://api.imgur.com/3/image';
		var apiKey = '7de684608de179a';

		var settings = {
			url: imgurApi,
			type: 'POST',
			async: false, crossDomain: true, processData: false, contentType: false,
			headers: {
				Authorization: 'Client-ID ' + apiKey,
				Accept: 'application/json',
			},
			mimeType: 'multipart/form-data',
		};

		var formData = new FormData();
		formData.append('image', file);
		settings.data = formData;

		$.ajax(settings).done(function (response) {
			var responseObj = JSON.parse(response);
			$.markItUp({ replaceWith: '[img]' + responseObj.data.link+ '[/img]' });
		}).fail(function (errorReason) {
			if(errorReason && errorReason.responseText){
				var responseObj = JSON.parse(errorReason.responseText);
				alert(responseObj.data.error.message);
			}
			else{
				alert('Unable to upload file.');
			}


		});
	};

	return {
        uploadFiles: uploadFiles
    };
})();


var imgurUpload = function (miu) {
	$('#miuImageUpload').remove();
	var imgFileSelector = $('<input type="file" id="miuImageUpload" accept="image/png, image/gif, image/jpeg" style="display:none;"/>').appendTo($(document.body));

	imgFileSelector.on('change', function () {

		var pThis = $(this);

		var files = pThis.get(0).files;

		ImgurHelper.uploadFiles(files[0]);
	});

	imgFileSelector.click();

};


$(function () {
	$('body').on('click', '.markItUpButton10 ul li:not(.playerNoteAdd):not(.playerPrivateAdd)', function (e) {
		$(this).toggleClass('playerNoteSelected');
		e.stopPropagation();
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

	$('textarea#messageTextArea').on('paste',function(ev){
		var clipboard=(ev.originalEvent.clipboardData || window.clipboardData);

		if(clipboard && clipboard.items){
			for(var i=0;i<clipboard.items.length;i++){
				var item= clipboard.items[i];
				if (item.type.indexOf("image") === 0){
					var blob = item.getAsFile();
					ImgurHelper.uploadFiles(blob);

					ev.preventDefault();
				}
			}
		}

	});

});