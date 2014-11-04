<?
	$email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)?$_GET['email']:false;
	$user = new User($email);
	$g_validationStr = preg_match('/^[a-z0-9]{13}$/i', $_GET['validate'])?$_GET['validate']:false;
	$o_validationStr = substr(md5($email.substr($user->password, 3).$user->activatedOn), 4, 13);
?>
<?	require_once(FILEROOT.'/header.php'); ?>
<?	if ($g_validationStr == $o_validationStr) { ?>
<?		if (isset($_GET['passError']) || isset($_GET['invalid'])) { ?>
		<div class="alertBox_error"><ul>
<?
			if (isset($_GET['passError'])) echo "			<li>Password(s) incorrect or do not match</li>\n";
			if (isset($_GET['invalid'])) echo "			<li>Link or validation key incorrect</li>\n";
?>
		</ul></div>
<?		} ?>
		<h1 class="headerbar">Reset Password</h1>

		<form method="post" action="/login/process/resetPass/">
			<div class="tr">
				<label>New Password:</label>
				<input id="password1" type="password" name="pass1" maxlength="32">
				<div class="alert">
					<div id="passBlank" class="<?=(isset($errors['passBlank'])?'showDiv':'hideDiv')?>">Password cannot be blank</div>
					<div id="passShort" class="<?=(isset($errors['passShort'])?'showDiv':'hideDiv')?>">Password too short</div>
					<div id="passLong" class="<?=(isset($errors['passLong'])?'showDiv':'hideDiv')?>">Password too long</div>
				</div>
			</div>
			<div class="notice">Password must be between 6 and 32 characters long</div>
			<div class="tr">
				<label>Repeat New Password:</label>
				<input id="password2" type="password" name="pass2" maxlength="32">
				<div class="alert">
					<div id="passMismatch" class="<?=(isset($errors['passMismatch'])?'showDiv':'hideDiv')?>">Passwords don't match</div>
				</div>
			</div>
			<input type="hidden" name="email" value="<?=$email?>">
			<input type="hidden" name="validationStr" value="<?=$g_validationStr?>">
			<div class="alignCenter"><button type="submit" name="submit" value="submit" class="fancyButton">Submit</button></div>
		</form>
<?	} else { ?>
		<h1 class="headerbar">Reset Password</h1>

		<div class="hbMargined">
			<p>Looks like the link you clicked wasn't correct. Make sure you got the entirety of the link in your email.</p>
			<p>If you have, <a href="/contact/">Contact Us</a>.</p>
		</div>
<?	} ?>
<?	require_once(FILEROOT.'/footer.php'); ?>