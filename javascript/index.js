$(function() {

    var storage = window.localStorage;
    $('.announcements .openClose').on('click',function(){
        var pThis=$(this);
        pThis.toggleClass('openClose-open');
        pThis.closest('.announcements').toggleClass('annoucementClosed');

        var storageKey='announcement-'+pThis.data('announce');
        if(pThis.hasClass('openClose-open')){
            storage.removeItem(storageKey);
        } else {
            storage.setItem(storageKey, pThis.data('threadid'));
        }
    });

    $('.announcements .openClose').each(function(){
        var pThis=$(this);
        var storageKey='announcement-'+pThis.data('announce');
        var threadId=pThis.data('threadid');
        if(storage.getItem(storageKey)==threadId){
            pThis.click();
        }

    });

    $('.notifyMention a').on('click',function(){
        var postId=$(this).data('postid');
		$.ajax({
			type: 'post',
			url: API_HOST +'/users/removeMention',
			xhrFields: {
				withCredentials: true
			},
			data:{ postID: postId}
		});
    });

});
