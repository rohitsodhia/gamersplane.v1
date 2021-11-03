<?
	$responsivePage=true;
	$formErrors->getErrors('resendActivation');
	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">Resend Activation Email</h1>
		<div class="hbMargined">
<?	if ($formErrors->checkError('alreadyActivated')) { ?>
		<ul class="alertBox_error">
			<li>Your account is already activated!</li>
		</ul>
<?	} elseif ($formErrors->checkError('noAccount')) { ?>
		<ul class="alertBox_error">
			<li>We couldn't find an inactive account with the email address you entered.</li>
		</ul>
<?	} elseif ($_GET['sent']) { ?>
		<ul class="alertBox_success">
			<li>An email has been sent to you with instructions on how to activate your account.</li>
		</ul>
<?	} ?>
			<p>Please make sure <strong>contact@gamersplane.com</strong> is whitelisted on your email account. If you do not recieve an email in your inbox, please check your spam folder.</p>
			<p>If you don't recieve an email after trying this form, please <a href="mailto:contact@gamersplane.com">email us</a> and we'll help you out. Please try this form at least once.</p>
			<form method="post" action="/register/process/resendActivation/">
				<div class="tr">
					<label for="email">Email:</label>
					<input id="email" type="text" name="email" value="<?=isset($_GET['email'])?$_GET['email']:''?>">
				</div>
				<div id="submitDiv"><button type="submit" name="resend" value="resend" class="fancyButton">Submit</button></div>
			</form>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>