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
		$('div.faq a').click(function (e) {
			e.preventDefault();

			$link = $(this);

			if ($link.hasClass('edit')) $link.closest('.faq').addClass('editing');
			else if ($link.hasClass('save')) {
				$faq = $link.closest('.faq');
				$.post('/acp/process/editFAQ/', { mongoID: $faq.data('questionId'), question: $faq.find('input').val(), answer: $faq.find('textarea').val() }, function (data) {

				});
			} else if ($link.hasClass('cancel')) $link.closest('.faq').removeClass('editing');
			else if ($link.hasClass('delete')) {

			}
		});
	}
});