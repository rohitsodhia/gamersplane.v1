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

//forums menu
	var newMenu=$('<li id="fm_forumthreads"><a class="menuLink">Forum</a><ul class="submenu"></li></li>').appendTo($('#fixedMenu_window .leftCol'));
	$('.menuLink',newMenu).on('click',function(){
		var submenu=$('.submenu',$(this).parent());
		$('#fixedMenu .submenu.fm_smOpen').not(submenu).slideUp(250).removeClass('fm_smOpen');
		submenu.slideToggle(250).toggleClass('fm_smOpen');
	});

	var forumsLink=$('#threadMenu #breadcrumbs a').last().attr('href');
	$.get( forumsLink, function( data ) {
		var dataObj=$(data);
		var forums=$('.forumList .tr',dataObj);
		var subMenu=$('.submenu',newMenu);
		if(forums.length>0)
		{
			$('h3',$('<li><h3></h3></li>').appendTo(subMenu)).text('Forums');
			for(var i=0;i<forums.length;i++)
			{
				$('<li></li>').html($('.name',forums[i]).html()).appendTo(subMenu);
			}
		}
		var threads=$('.threadList .tr',dataObj);
		if(threads.length>0)
		{
			$('h3',$('<li><h3></h3></li>').appendTo(subMenu)).text('Threads');
			for(var i=0;i<threads.length;i++)
			{
				var menuItem=$('<li><a></a></li>').appendTo(subMenu);
				var threadText=$('.td.threadInfo>a',threads[i]);
				$('a',menuItem).attr('href',threadText.attr('href')).text(threadText.text());
			}

		}

	});


});