<?
	if (isset($_POST['submit'])) {
		$errors = '?';
		$updates = '';
		$oldPass = $_POST['oldPass'];
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		if (!$currentUser->validate($oldPass)) $errors .= 'wrongPass=1&';
		if (strlen($password1) == 0 || strlen($password2) == 0 || $password1 == $password2) $errors .= 'passMismatch=1&';

		if (strlen($errors) > 1) header('Location: /ucp/cp'.$errors);
		else {
			$currentUser->updatePassword($password1);
			
			header('Location: /ucp/cp/?updated=1');
		}
	} else header('Location: /user');
?>