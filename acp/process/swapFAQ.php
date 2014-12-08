<?
	$currentUser->checkACP('faqs');

	$formErrors->clearErrors();	

	$_id[1] = new MongoId($_POST['mongoID1']);
	$_id[2] = new MongoId($_POST['mongoID2']);
	if ($_id[1] && $_id[2]) {
		$result = $mongo->faqs->find(array('$or' => array(array('_id' => $_id[1]), array('_id' => $_id[2]))));
		foreach ($result as $iResult) $order[array_search((string) $iResult['_id'], $_id)] = (int) $iResult['order'];
		$mongo->faqs->update(array('_id' => $_id[1]), array('$set' => array('order' => $order[2])));
		$mongo->faqs->update(array('_id' => $_id[2]), array('$set' => array('order' => $order[1])));
	}
?>