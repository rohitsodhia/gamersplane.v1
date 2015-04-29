<?
	if ($loggedIn) { header('Location: /'); exit; }
	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">Registration</h1>

		<form method="post" action="/register/process/register/">
<?	if ($formErrors->getErrors('addFAQ')) { ?>
			<div class="alertBox_error">
				There was a problem with your registration. Please see the errors below and try again.
			</div>
<? } ?>
			<div class="tr inputTR">
				<label class="textLabel">Username</label>
				<input id="username" type="text" name="username" maxlength="24" value="<?=$username?>" tabindex="<?=tabOrder()?>" class="textfield">
				<div class="alert">
					<div id="userShort" class="<?=(isset($errors['userShort'])?'showDiv':'hideDiv')?>">Username must be more then 4 characters long</div>
					<div id="userLong" class="<?=(isset($errors['userLong'])?'showDiv':'hideDiv')?>">Username can only be up to 24 characters</div>
					<div id="userInvalid" class="<?=(isset($errors['userInvalid'])?'showDiv':'hideDiv')?>">Username Invalid</div>
					<div id="userTaken" class="<?=(isset($errors['userTaken'])?'showDiv':'hideDiv')?>">Username Taken</div>
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
					<div id="passBlank" class="<?=(isset($errors['passBlank'])?'showDiv':'hideDiv')?>">Password cannot be blank</div>
					<div id="passShort" class="<?=(isset($errors['passShort'])?'showDiv':'hideDiv')?>">Password too short</div>
					<div id="passLong" class="<?=(isset($errors['passLong'])?'showDiv':'hideDiv')?>">Password too long</div>
				</div>
			</div>
			<div>
				<div class="noFloat notice">Password must be between 6-32 characters</div>
			</div>
			<div class="tr inputTR">
				<label class="textLabel">Repeat Password</label>
				<input id="password2" type="password" name="password2" maxlength="32" tabindex="<?=tabOrder()?>" class="textfield">
				<div class="alert">
					<div id="passMismatch" class="<?=(isset($errors['passMismatch'])?'showDiv':'hideDiv')?>">Passwords don't match</div>
				</div>
			</div>
			
			<div class="tr inputTR">
				<label class="textLabel">Email Address</label>
				<input id="email" type="text" name="email" maxlength="100" value="<?=$email?>" tabindex="<?=tabOrder()?>" class="textfield">
				<div class="alert">
					<div id="emailBlank" class="<?=(isset($errors['emailBlank'])?'showDiv':'hideDiv')?>">Email cannot be blank</div>
					<div id="emailInvalid" class="<?=(isset($errors['emailInvalid'])?'showDiv':'hideDiv')?>">Email Invalid</div>
					<div id="emailTaken" class="<?=(isset($errors['emailTaken'])?'showDiv':'hideDiv')?>">Email Taken</div>
				</div>
			</div>
			
			<div class="tr inputTR">
				<label class="textLabel">Where did you hear about us?</label>
				<input id="hear" type="text" name="hear" maxlength="100" value="<?=$hear?>" tabindex="<?=tabOrder()?>" class="textfield">
				<div class="alert"></div>
			</div>
			
			<div id="recaptchaDiv" class="tr">
				<h2>Prove to me you're real!</h2>
<?
		require_once(FILEROOT.'/register/recaptcha/recaptchalib.php');
		$publickey = '6LeuZgcAAAAAAOJpyjwOnxRuvSnMzUxSyKV-CNg5 ';
		echo recaptcha_get_html($publickey);
		echo "\n";
?>
				<div class="alert <?=(isset($errors['captchaFailed'])?'showDiv':'hideDiv')?>">reCaptch failed!</div>
			</div>
			
			<input type="hidden" name="gender" value="Pick One">
			
			<div id="submitDiv">
				<button id="submit" type="submit" name="submit" tabindex="<?=tabOrder(2)?>" class="fancyButton">Submit</button>
			</div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>