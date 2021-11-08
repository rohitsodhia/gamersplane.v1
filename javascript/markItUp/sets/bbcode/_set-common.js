
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
			$('#messageTextArea').focus();
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

	$('textarea#messageTextArea,textarea.markItUp').on('paste',function(ev){
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