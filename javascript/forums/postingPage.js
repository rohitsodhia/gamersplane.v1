$(function() {

    var updateCharLink=function(postAs){
        var charPost=$('#fm_characters .charid-'+postAs.val());
        var charSheetLink=$('#charSheetLink').html('');
        if(charPost.length>0){
            charPost=charPost.eq(0);
            $('<a target="_blank"></a>').text(charPost.text()).attr('href',charPost.attr('href')).appendTo(charSheetLink);
        }
    };

    $('select[name="postAs"]').on('change',function(){
        updateCharLink($(this));
    });

    updateCharLink($('select[name="postAs"]'));

    $('#fm_characters .ra-quill-ink').css({visibility:'visible'}).on('click',function(){
        var text=$('a',$(this).closest('p')).text();
        $('#messageTextArea').focus();
        $.markItUp({ replaceWith: text });
    });
});
