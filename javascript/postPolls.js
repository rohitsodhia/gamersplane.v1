$(function() {
    $('body').on('click','.postPoll .pollQuestion',function(){
        var pThis=$(this);
        var postPoll=pThis.closest('.postPoll');
        var postId=postPoll.addClass('thinking').data('postid');
        var vote=pThis.data('q');
        var isMulti=postPoll.hasClass('pollAllowMulti');
        var hasVote=pThis.hasClass('pollMyVote');
        var isPublic=postPoll.hasClass('pollPublic');


        var addVote=true;
        if(hasVote){
            addVote=false;
        }

        $.ajax({
            type: 'post',
            url: API_HOST +'/forums/pollVote',
            xhrFields: {
                withCredentials: true
            },
            data:{ postId: postId, vote:vote, addVote: addVote?1:0, isMulti:isMulti?1:0, isPublic:isPublic?1:0},
            success:function (data) {
                if(data){
                    $('.pollQuestionResults',postPoll).html('');
                    $('.pollMyVote',postPoll).removeClass('pollMyVote');

                    $('.pollQuestion',postPoll).each(function(){
                        var pQ=$(this);
                        var pThisAnswer=data.votes[pQ.data('q')];
                        if(pThisAnswer){
                            if(pThisAnswer.me){
                                pQ.addClass('pollMyVote');
                            }
                            for(var i=0;i<pThisAnswer.votes;i++){
                                $('.pollQuestionResults',pQ).html(pThisAnswer.html);
                            }
                        }
                    });
                }
                postPoll.removeClass('thinking');
            }
        });
    });

    $('body').on('click','.ffgTokens div',function(){
        var pThis=$(this);
        var destiny=pThis.closest('.ffgDestiny');
        var postId=destiny.addClass('thinking').data('postid');
        var totalFlips=destiny.attr('data-totalflips');
        var tokens=destiny.data('tokens');
        var isDark=pThis.hasClass('darkToken');

        $.ajax({
            type: 'post',
            url: API_HOST +'/forums/ffgFlip',
            xhrFields: {
                withCredentials: true
            },
            data:{ postId: postId, toDark:isDark?0:1, totalFlips: totalFlips, tokens: tokens},
            success:function (data) {
                var newHtml=$(data.html);
                destiny.html(newHtml.html());
                $('.ffgHistoryList',destiny).show();
                $('.ffgHistoryToggle',destiny).hide();
                destiny.attr('data-totalFlips',newHtml.attr('data-totalFlips'));
                destiny.removeClass('thinking');
                if(!data.success){
                    $('<div class="updateError">The list has changed. Do you still want to flip a token?</div>').prependTo(destiny);
                }
            }
        });
    });

    $('body').on('click','.ffgHistoryToggle',function(){
        $('.ffgHistoryList',$(this).hide().parent()).show();
    });

});