$(function() {
    $('body').on('click','.postPoll .pollQuestion',function(){
        var pThis=$(this);
        var postPoll=pThis.closest('.postPoll');
        var postId=postPoll.addClass('thinking').data('postid');
        var vote=pThis.data('q');
        var isMulti=postPoll.hasClass('pollAllowMulti');
        var hasVote=pThis.hasClass('pollMyVote');

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
            data:{ postId: postId, vote:vote, addVote: addVote?1:0, isMulti:isMulti?1:0},
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
                                $('<i class="ra ra-gamers-plane"></i>').appendTo($('.pollQuestionResults',pQ));
                            }
                        }
                    });
                }
                postPoll.removeClass('thinking');
            }
        });
    });
});