$(function() {

    var storage = window.localStorage;
    $('.announcements .openClose').on('click',function(){
        var pThis=$(this);
        pThis.toggleClass('openClose-open');
        var announements=pThis.closest('.announcements');
        /*
        if(announements.hasClass('annoucementClosed')){
            $('.announcementPost',announements).slideOpen();
        }
        else{
            $('.announcementPost',announements).slideClosed();
        }*/
        $('.announcementPost',announements).slideToggle();
        announements.toggleClass('annoucementClosed');

        var storageKey='announcement-'+pThis.data('announce')+'-'+pThis.data('threadid');
        if(pThis.hasClass('openClose-open')){
            storage.removeItem(storageKey);
        } else {
            storage.setItem(storageKey, pThis.data('threadid'));
        }
    });

    $('.announcements .openClose').each(function(){
        var pThis=$(this);
        var threadId=pThis.data('threadid');
        var storageKey='announcement-'+pThis.data('announce')+'-'+threadId;
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
