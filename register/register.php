<?php
	$responsivePage=true;
	if ($loggedIn) { header('Location: /'); exit; }
	$addExternalJSFiles[] = 'https://www.google.com/recaptcha/api.js';
	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">Registration</h1>

		<form method="post" action="/register/process/register/">
<?php
	$errors = [];
	if ($formErrors->getErrors('registration')) {
		$errors = $formErrors->getErrors('registration');
?>
			<div class="alertBox_error">
				There was a problem with your registration. Please see the errors below and try again.
			</div>
<?php	} ?>
			<div id="resendActivation" class="tr">If you've previously registered but never received your activation mail, try <a href="/register/resendActivation/">resending your activation email</a>.</div>
			<div class="tr inputTR">
				<label class="textLabel">Username</label>
				<input id="username" type="text" name="username" maxlength="24" value="<?=$username?>" tabindex="<?=tabOrder()?>" class="textfield">
				<div class="alert">
					<div id="userShort" class="<?=(in_array('userShort', $errors)?'showDiv':'hideDiv')?>">Username must be more then 4 characters long</div>
					<div id="userLong" class="<?=(in_array('userLong', $errors)?'showDiv':'hideDiv')?>">Username can only be up to 24 characters</div>
					<div id="userInvalid" class="<?=(in_array('userInvalid', $errors)?'showDiv':'hideDiv')?>">Username Invalid</div>
					<div id="userTaken" class="<?=(in_array('userTaken', $errors)?'showDiv':'hideDiv')?>">Username Taken</div>
				</div>
			</div>
			<div>
				<div class="noFloat notice">Username may be letters, numbers, underscores (_) and periods (.), up to 24 characters</div>
				<div class="noFloat notice">Keep usernames PG-13. GamersPlane will act if we find your username unacceptable.</div>
			</div>

			<div class="tr inputTR">
				<label class="textLabel">Password</label>
				<input id="password1" type="password" name="password1" maxlength="32" tabindex="<?=tabOrder()?>" class="textfield">
				<div class="alert">
					<div id="passBlank" class="<?=(in_array('passBlank', $errors)?'showDiv':'hideDiv')?>">Password cannot be blank</div>
					<div id="passShort" class="<?=(in_array('passShort', $errors)?'showDiv':'hideDiv')?>">Password too short</div>
					<div id="passLong" class="<?=(in_array('passLong', $errors)?'showDiv':'hideDiv')?>">Password too long</div>
				</div>
			</div>
			<div>
				<div class="noFloat notice">Password must be between 6-32 characters</div>
			</div>
			<div class="tr inputTR">
				<label class="textLabel">Repeat Password</label>
				<input id="password2" type="password" name="password2" maxlength="32" tabindex="<?=tabOrder()?>" class="textfield">
				<div class="alert">
					<div id="passMismatch" class="<?=(in_array('passMismatch', $errors)?'showDiv':'hideDiv')?>">Passwords don't match</div>
				</div>
			</div>

			<div class="tr inputTR">
				<label class="textLabel">Email Address</label>
				<input id="email" type="text" name="email" maxlength="100" value="<?=$email?>" tabindex="<?=tabOrder()?>" class="textfield">
				<div class="alert">
					<div id="emailBlank" class="<?=(in_array('emailBlank', $errors)?'showDiv':'hideDiv')?>">Email cannot be blank</div>
					<div id="emailInvalid" class="<?=(in_array('emailInvalid', $errors)?'showDiv':'hideDiv')?>">Email Invalid</div>
					<div id="emailTaken" class="<?=(in_array('emailTaken', $errors)?'showDiv':'hideDiv')?>">Email Taken</div>
				</div>
			</div>

			<div class="tr inputTR">
				<label class="textLabel">Where did you hear about us?</label>
				<input id="hear" type="text" name="hear" maxlength="100" value="<?=$hear?>" tabindex="<?=tabOrder()?>" class="textfield">
				<div class="alert"></div>
			</div>

<?php	if (strtolower($_SERVER['HTTP_HOST']) == getenv('APP_URL')) { ?>
			<div id="recaptchaDiv" class="tr">
				<h2>Prove to me you're real!</h2>
				<div class="g-recaptcha" data-sitekey="6LcT8gsTAAAAALlRVGdtM9iansESdnIdeCUIwoqG"></div>
				<div class="alert <?=(in_array('captchaFailed', $errors) ? 'showDiv' : 'hideDiv')?>">reCaptch failed!</div>
			</div>
<?php	} ?>

			<input type="hidden" name="gender" value="Pick One">

			<div id="submitDiv">
				<button id="submit" type="submit" name="submit" tabindex="<?=tabOrder(2)?>" class="fancyButton">Submit</button>
			</div>
		</form>
<?php	require_once(FILEROOT . '/footer.php'); ?>
