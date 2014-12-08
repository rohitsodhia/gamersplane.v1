<?
	if (isset($_POST['submit'])) {
		if (isset($_POST['userID']) && intval($_POST['userID']) && $currentUser->checkACP('users')) {
			$user = new User(intval($_POST['userID']));
			if (!$user->userID) { header('Location: /ucp/'); exit; }
		} else $user = $currentUser;

		if (in_array($_POST['postSide'], array('l', 'r', 'c'))) $postSide = $_POST['postSide'];
		else $postSide = 'l';
		
		$user->updateUsermeta('postSide', $postSide);
		
		header('Location: /ucp/cp/?updated=1');
	} else header('Location: /user/');
?>