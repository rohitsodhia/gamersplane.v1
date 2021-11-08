$(function() {
	$('.postAsChar .userAvatar').each(function () {
		var $img = $(this).find('img');
		$img.load(function () {
			console.log($img);
			$(this).parent().css({'top': '-' + ($img.height() / 2) + 'px', 'right': '-' + ($img.width() / 2) + 'px' });
		});
	});

	$('#messageTextArea').markItUp(mySettings);

	$('.deletePost').colorbox();

	$('#forumSub').click(function (e) {
		e.preventDefault();

		$link = $(this);
		$.get($(this).attr('href'), {}, function (data) {
			if ($link.text().substring(0, 3) == 'Uns')
				$link.text('Subscribe to ' + $link.text().split(' ')[2]);
			else
				$link.text('Unsubscribe from ' + $link.text().split(' ')[2]);
		});
	});

	$('.keepUnread').click(function(){
		var pThis=$(this);
		var threadId=pThis.data('threadid');
		$.ajax({
			type: 'post',
			url: API_HOST +'/forums/setLastPostUnread',
			xhrFields: {
				withCredentials: true
			},
			data:{ threadID: threadId},
			success:function (data) {
				pThis.remove();
			}
		});
	});

	$('.quotePost').click(function(){

		var pThis=$(this);
		var postId=pThis.data('postid');
		$.ajax({
			type: 'post',
			url: API_HOST +'/forums/getPostQuote',
			xhrFields: {
				withCredentials: true
			},
			data:{ postID: postId},
			success:function (data) {
				$('#messageTextArea').focus();
				$.markItUp({ replaceWith: data });
				$("#messageTextArea")[0].scrollIntoView();
			}
		});

	});

	$('#previewPost').click(function(){
		$('.postPreview .post').html('<div class="previewing">Getting preview</div>');
		$('.postPreview').show();
		$.ajax({
			type: 'post',
			url: API_HOST +'/forums/getPostPreview',
			xhrFields: {
				withCredentials: true
			},
			data:{ postText: $('#messageTextArea').val()},
			success:function (data) {
				$('.postPreview .post').html(data).darkModeColorize();
				$(".postPreview")[0].scrollIntoView();
			}
		});
	});

	$('#backfill').on('click',function(){
		var pThis=$(this);
		var basePage = window.location.href.split('?')[0];
		var prevPages=$('.paginateDiv a.page').prevAll('a').filter(function( index ) {return !isNaN($(this).text());});

		var startScroll = $(window).scrollTop();
		var startHeight=$(document ).height()

		$.get(basePage + '?page=' + $(prevPages[0]).text(), function (data) {
			var block=$('.postBlock', $(data));
			(block.clone().insertAfter(pThis)).addClass('postBlockFound').darkModeColorize();
			var newHeight=$(document).height()

			$(window).scrollTop(startScroll+(newHeight-startHeight));

			pThis.remove();
		});
	});

});