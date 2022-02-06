$(function() {

    var storage = window.localStorage;
    $('.announcements .openClose').on('click',function(){
        var pThis=$(this);
        pThis.toggleClass('openClose-open');
        var announements=pThis.closest('.announcements');
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

    $('.notifyThread a').on('click',function(event){
        var navigateTo=$(this).attr('href');
        event.preventDefault();
        var postId=$(this).data('postid');
		$.ajax({
			type: 'post',
			url: API_HOST +'/users/removeThreadNotification',
			xhrFields: {
				withCredentials: true
			},
			data:{ postID: postId}
		}).always(function() {
            location.href = navigateTo;
        });
    });

    $('#clearMentions').on('click',function(event){
		$.ajax({
			type: 'post',
			url: API_HOST +'/users/removeAllThreadNotifications',
			xhrFields: {
				withCredentials: true
			}
		}).always(function() {
            $('.notifyThread').remove();
            $('#clearMentions').remove();
            if($('#notificationMsgs .notify').length==0){
                $('#notifications').remove();
            }
        });
    });

});
