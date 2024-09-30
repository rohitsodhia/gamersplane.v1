<?
	$currentUser->checkACP('faqs');

	$formErrors->clearErrors();

	$_id = new MongoId($_POST['mongoID']);
	if ($_id) {
	}
?>
