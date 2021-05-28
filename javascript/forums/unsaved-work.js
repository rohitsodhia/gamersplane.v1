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