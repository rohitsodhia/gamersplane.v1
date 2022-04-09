var imgurUpload = function (miu) {
	$('#miuImageUpload').remove();
	var imgFileSelector = $('<input type="file" id="miuImageUpload" accept="image/png, image/gif, image/jpeg" style="display:none;"/>').appendTo($(document.body));

	imgFileSelector.on('change', function () {
		var pThis = $(this);
		var files = pThis.get(0).files;
		ImgurHelper.uploadFiles(files[0],function(link){$.markItUp({ replaceWith: '[img]' + link+ '[/img]' });});
	});

	imgFileSelector.click();

};

$(function () {
	$('textarea#messageTextArea,textarea.markItUp').on('paste',function(ev){
		ImgurHelper.uploadFromClipboard(ev, function(link){$.markItUp({ replaceWith: '[img]' + link+ '[/img]' });});
	});
});

var mobileifyMenus=function(settings){
	if($('#mainMenuTools:visible').length==0){
		settings.isMobile=true;
		for(var i=0;i<settings.markupSet.length;i++){
			var menuOpt=settings.markupSet[i];
			if(menuOpt.name=='Color'){
				menuOpt.dropMenu.push({name:'Color...',openWith: '[color="[![Text color]!]"]', closeWith: '[/color]'});
				menuOpt.openWith=null;
				menuOpt.closeWith=null;
			}
			else if(menuOpt.name=='Image'){
				menuOpt.replaceWith=null;
			}
			else if(menuOpt.name=='Note' && menuOpt.dropMenu){
				menuOpt.dropMenu.push({name:'Note...',openWith:'[note="[![User(s)]!]"]', closeWith:'[/note]', className: 'otherNoteAdd'});
				menuOpt.openWith=null;
				menuOpt.closeWith=null;
			}
		}
	}

};