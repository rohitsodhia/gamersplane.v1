$(function () {
    $(document).on('gp.characterloaded',function(ev,params){
        if(!params.notes){
            $.get( '/forums/thread/21532/?pageSize=10000', function( data ) {
                var templateList=$('#templateList');
                $('.post .spoiler.snippet', $(data)).each(function(){
                    var spoiler=$(this);
                    var snippetTitle=$('.snippetName',spoiler).text();
                    var snippetbbcode=$('.snippetBBCode',spoiler).text();
                    $('<option></option>').text(snippetTitle).data('bbcode',snippetbbcode).appendTo(templateList);
                    $('#loadTemplate').show();
                });
                var options = templateList.find('option');
                options.sort(function(a, b) { return $(a).text() > $(b).text() ? 1 : -1; });
                templateList.append(options);
                $('option:first',templateList).prop("selected", true);
            });

            $('#loadTemplate').on('change', function (ev) {
                var loadTemplate=true;
                if($.trim($('textarea.markItUp').val()).length>0){
                    loadTemplate=confirm("This will overwrite the contents with the new template.  Are you sure?");
                }
                if(loadTemplate){
                    var pThis=$(this);
                    var selectedOption=pThis.find(":selected");
                    var bbcode=selectedOption.data('bbcode');
                    $('textarea.markItUp').focus().val(bbcode).change();
                }
                $('option:first',pThis).prop("selected", true);
            });
        }
    });

    $('.customTabs li').on('click',function(){
        var pThis=$(this);
        $('.customTabs li').removeClass('tabSel');
        pThis.addClass('tabSel');
        $('.editTab').hide();
        var showSel=pThis.data('showsel');
        var showEle=$(showSel).show();
        if(showSel=='#tabPreview'){
            showEle.removeClass('calculationsInitialised').html('<p>Loading....</p>');
            $.ajax({type: 'post',url: API_HOST +'/forums/getPostPreview',xhrFields: {withCredentials: true},
                data:{ postText: $('textarea.markItUpEditor').val(), postAsId: 0, postAsName: ''},
                success:function (data) {
                    showEle.html(data.post).darkModeColorize().zoommap().updateCalculations();
                }
            });
        }
    });
});