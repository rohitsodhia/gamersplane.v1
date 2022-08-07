<?
	if (isset($_POST['resend'])) {
		$formErrors->clearErrors();
		if (strlen($_POST['email']) == 0)
			$formErrors->addError('noEmail');
		$user = $mysql->prepare("SELECT username, activatedOn FROM users WHERE LOWER(email) = :email");
		$user->execute(array(':email' => strtolower($_POST['email'])));
		if ($user->rowCount() == 0)
			$formErrors->addError('noAccount');
		else {
			list($username, $activatedOn) = $user->fetch(PDO::FETCH_NUM);
			if ($activatedOn != null)
				$formErrors->addError('alreadyActivated');
		}
		if ($formErrors->errorsExist()) {
			$formErrors->setErrors('resendActivation');
			header('Location: /register/resendActivation/?email='.$_POST['email']);
		} else {
			sendActivationEmail($_POST['email'], $username);
			header('Location: /register/resendActivation/?sent=1');
		}
	} else
		header('Location: /register/');
?>