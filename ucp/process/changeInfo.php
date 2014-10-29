<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	
	if (isset($_POST['submit'])) {
		$errors = '?';
		$updates = '';
		$oldPass = $_POST['oldPass'];
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		if (strlen($oldPass) && strlen($password1) && strlen($password2) && $password1 == $password2) {
			$oldPass = hash('sha256', PVAR.$oldPass);
			$password1 = hash('sha256', PVAR.$password1);
			$password2 = hash('sha256', PVAR.$password2);
			$userCheck = $mysql->query('SELECT userID FROM users WHERE userID = '.$userID.' AND password = "'.$oldPass.'"');
			if ($userCheck->rowCount()) $updates = 'password = "'.$password1.'"';
			else $errors .= 'wrongPass=1&';
		} elseif ($password1 != $password2) $errors .= 'passMismatch=1&';
		
		if (strlen($errors) > 1) {
			header('Location: /ucp/cp'.$errors);
		} else {
			unset($_SESSION['errors']);
			unset($_SESSION['errorTime']);
			
			$mysql->query('UPDATE users SET '.$updates.' WHERE userID = '.$userID);
			$_SESSION['timezone'] = $timezone;
			
			header('Location: /ucp/cp/?updated=1');
		}
	} else header('Location: /user');
?>