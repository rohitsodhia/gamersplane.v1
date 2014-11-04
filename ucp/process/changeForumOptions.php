<?
	if (isset($_POST['submit'])) {
		if (in_array($_POST['postSide'], array('l', 'r', 'c'))) $postSide = $_POST['postSide'];
		else $postSide = 'l';
		
		$currentUser->updateUsermeta('postSide', $postSide);
		
		header('Location: /ucp/cp/?updated=1');
	} else header('Location: /user/');
?>