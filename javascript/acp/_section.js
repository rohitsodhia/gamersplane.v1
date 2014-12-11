$(function () {
	if ($('#page_acp_autocomplete').length) {
		$('#newItems').on('click', '.actions a', function (e) {
			e.preventDefault();

			var $itemRow = $(this).closest('.newItem'), postData = { uItemID: $itemRow.attr('id').split('_')[1], name: $itemRow.children('input').val() };
			if ($(this).hasClass('check')) postData['action'] = 'add';
			else if ($(this).hasClass('cross')) postData['action'] = 'reject';
			$.post('/acp/process/newItem/', postData, function (data) {
				$itemRow.remove();
			});
		});
		$('#addToSystem').on('click', '.actions a', function (e) {
			e.preventDefault();

			var $itemRow = $(this).closest('.item'), postData = { uItemID: $itemRow.attr('id').split('_')[1], name: $itemRow.children('input').val() };
			if ($(this).hasClass('check')) postData['action'] = 'add';
			else if ($(this).hasClass('cross')) postData['action'] = 'reject';
			$.post('/acp/process/addToSystem/', postData, function (data) {
				$itemRow.remove();
			});
		});
	}

	if ($('#page_acp_faqs').length) {
		$('div.faq').on('click', '.display a, .inputs a', function (e) {
			e.preventDefault();

			$link = $(this);
			$faq = $link.closest('.faq');

			if ($link.hasClass('edit')) $link.closest('.faq').addClass('editing');
			else if ($link.hasClass('save')) {
				$.post('/acp/process/editFAQ/', { mongoID: $faq.data('questionId'), question: $faq.find('input').val(), answer: $faq.find('textarea').val() }, function (data) {
					$link.closest('.faq').removeClass('editing').find('.display .answer').html(data);
				});
			} else if ($link.hasClass('cancel')) $link.closest('.faq').removeClass('editing');
			else if ($link.hasClass('delete')) {
				$.post('/acp/process/deleteFAQ/', { mongoID: $faq.data('questionId') }, function (data) {
					$faq.remove();
				});
			}
		}).on('click', '.controls a', function (e) {
			e.preventDefault();

			$current = $(this).closest('.faq');
			if ($(this).hasClass('upArrow')) {
				$swap = $current.prev();
				if ($swap.length) $current.insertBefore($swap);
			} else {
				$swap = $current.next();
				if ($swap.length) $current.insertAfter($swap);
			}
			$.post('/acp/process/swapFAQ/', { mongoID1: $current.data('questionId'), mongoID2: $swap.data('questionId') }, function () {
				;
			});
		});
	}

	if ($('#page_acp_music').length) {
		var $mainColumn = $('.mainColumn'), $editForm = $('#editMusicMaster');
		$editForm.ajaxForm({
			beforeSubmit: function (arr, $form) {
				var error = false;
				$form.find('input[type="text"]').each(function () {
					if ($(this).val().length == 0) error = true;
				});
				if (error) return false;
				error = true;
				$form.find('input[type="checkbox"]').each(function () {
					if ($(this).val() != 0 && $(this).attr('name').length) error = false;
				});
				if (error) return false;
			},
			success: function (data) {
				document.location.reload();
			}
		});
		$('.manageSong a').click(function (e) {
			e.preventDefault();

			var $link = $(this), action = $link.text().toLowerCase(), $li = $link.closest('li');
			if (action == 'delete') $link.hide().siblings('.confirmDelete').show();
			else if (action == 'deny') $link.parent().hide().siblings('.delete').show();
			else if (action == 'edit') {
				$link.closest('.songDetails').after($editForm);
				$editForm.find('#mongoID').val($li.data('id'));
				$editForm.find('#url').val($li.find('.song').attr('href'));
				$editForm.find('#title').val($li.find('.song').text());
				$editForm.find('input[type=radio]').prop('checked', false);
				if ($li.find('.song img').length) $editForm.find('#hasLyrics').prop('checked', true);
				else $editForm.find('#noLyrics').prop('checked', true);
				$editForm.find('.prettyRadio').each(syncRadio);
				genres = $li.find('.genres').text().split(',');
				for (i in genres) genres[i] = genres[i].trim();
				$editForm.find('#genres label').each(function () {
					if ($.inArray($(this).text(), genres) != -1) $(this).siblings('.prettyCheckbox').find('input').prop('checked', true);
					else $(this).siblings('.prettyCheckbox').find('input').prop('checked', false);
				});
				$editForm.find('.prettyCheckbox').each(syncRadio);
				$editForm.find('textarea').val($li.find('.notes').html());
			} else {
				if (action == 'confirm') action = 'delete';
				$.post('/acp/process/manageMusic/', { modal: true, mongoID: $link.closest('li').data('id'), action: action }, function (data) {
					if (data == 'Approve' || data == 'Unapprove') $link.text(data).closest('li').toggleClass('unapproved');
					else if (data == 'deleted') $link.closest('li').remove();
				});
			}
		});
	}

	if ($('#page_acp_users').length) {
		$('#controls a').click(function (e) {
			e.preventDefault();

			$.post('/acp/ajax/listUsers/', { show: this.id.substring(9, this.id.length) }, function (data) {
				$('div.mainColumn ul').html(data);
			});
		});
	}
});