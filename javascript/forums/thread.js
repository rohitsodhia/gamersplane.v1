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

	$('body').on('click','.quotePost',function(){

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
		var selOpt=$('select[name="postAs"] option:selected');
		$.ajax({
			type: 'post',
			url: API_HOST +'/forums/getPostPreview',
			xhrFields: {
				withCredentials: true
			},
			data:{ postText: $('#messageTextArea').val(), postAsId: selOpt.val(), postAsName: selOpt.text()},
			success:function (data) {
				$('.postPreview .post').html(data.post).darkModeColorize();

				if(data.avatar){
					$('img',$('.postPreview .posterDetails .avatar').show()).attr('src',data.avatar);
				}
				else{
					$('.postPreview .posterDetails .avatar').hide();
				}

				if(data.npcPoster){
					$('.postPreview').addClass('withSingleNpc');
				}else{
					$('.postPreview').removeClass('withSingleNpc');
				}

				$('.postPreview .charName').text(data.name);
				$(".postPreview")[0].scrollIntoView();
				var startScroll = $(window).scrollTop()-100;
				if(startScroll>0){
					$(window).scrollTop(startScroll);
				}
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
			var block=$('.postBlock:not(.postPreview)', $(data));
			(block.clone().insertAfter(pThis)).addClass('postBlockFound').darkModeColorize();
			var newHeight=$(document).height()

			$(window).scrollTop(startScroll+(newHeight-startHeight));

			pThis.remove();
		});
	});

//forums menu
	var newMenu=$('<li id="fm_forumthreads"><a class="menuLink">Threads</a><ul class="submenu"></li></li>').appendTo($('#fixedMenu_window .leftCol'));
	$('.menuLink',newMenu).on('click',function(){
		var submenu=$('.submenu',$(this).parent());
		$('#fixedMenu .submenu.fm_smOpen').not(submenu).slideUp(250).removeClass('fm_smOpen');
		submenu.slideToggle(250).toggleClass('fm_smOpen');
	});



	var loadForumLinks=function(forumLink){
		if(forumLink.length>0){
			var forumUrl=forumLink.attr('href');
			if(forumUrl.endsWith('/0')||forumUrl.endsWith('/2')){
				loadForumLinks(forumLink.next('a'));
			}
			else{
				$.get( forumUrl, function( data ) {
					var dataObj=$(data);
					var forums=$('.forumList .tr',dataObj);
					var subMenu=$('.submenu',newMenu);
					if(forums.length>0)
					{
						$('h3',$('<li><h3></h3></li>').appendTo(subMenu)).text('Forums: '+forumLink.text());
						for(var i=0;i<forums.length;i++)
						{
							$('<li></li>').html($('.name',forums[i]).html()).appendTo(subMenu);
						}
					}
					var threads=$('.threadList .tr',dataObj);
					if(threads.length>0)
					{
						$('h3',$('<li><h3></h3></li>').appendTo(subMenu)).text('Threads: '+forumLink.text());
						for(var i=0;i<threads.length;i++)
						{
							var menuItem=$('<li><a></a></li>').appendTo(subMenu);
							var threadText=$('.td.threadInfo>a',threads[i]);
							$('a',menuItem).attr('href',threadText.attr('href')).text(threadText.text());
						}
					}
					loadForumLinks(forumLink.next('a'));
				});
			}

		}
	};

	loadForumLinks($('#threadMenu #breadcrumbs a').first());
});