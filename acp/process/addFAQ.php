<?
	$currentUser->checkACP('faqs');

	$formErrors->clearErrors();
	if (!in_array($_POST['category'], $faqsCategories)) $formErrors->addError('noCategory');
	if (!strlen($_POST['question'])) $formErrors->addError('noQuestion');
	if (!strlen($_POST['answer'])) $formErrors->addError('noAnswer');

	if ($formErrors->errorsExist()) $formErrors->setErrors('addFAQ');
	elseif (isset($_POST['add'])) {
		$mongo->faqs->insert(array('category' => $_POST['category'], 'question' => $_POST['question'], 'answer' => $_POST['answer'], 'order' => $mongo->faqs->count(array('category' => $_POST['category'])) + 1));
	}
	header('Location: /acp/faqs/'); exit;
?>