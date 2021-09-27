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


});
