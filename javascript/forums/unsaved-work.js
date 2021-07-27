var _isSubmitting=false;
$('#submitDiv button').on('click',function(){
    _isSubmitting=true;
})

window.onbeforeunload = function ()
{
    var txtArea=$('#messageTextArea');
    if (!_isSubmitting && txtArea.length>0 && $.trim(txtArea.val()).length>0)
    {
        return "You haven't submitted your post. Click OK to continue without saving or Cancel to go back and save your post.";
    }
};

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
});
