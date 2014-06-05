<?
	$loggedIn = checkLogin(0);
	
	$validationStr = preg_match('/^[a-z0-9]*$/i', $_GET['validate'])?$_GET['validate']:FALSE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
<? if (isset($_GET['passError']) || isset($_GET['invalid'])) { ?>
		<div class="alertBox_error"><ul>
<?
		if (isset($_GET['passError'])) echo "			<li>Password(s) incorrect or do not match</li>\n";
		if (isset($_GET['invalid'])) echo "			<li>Link or validation key incorrect</li>\n";
?>
		</ul></div>
<? } ?>
		<h1 class="headerbar">Reset Password</h1>
		
		<form method="post" action="/login/process/resetPass">
			<div class="tr">
				<label class="textLabel">Username:</label>
				<input type="text" name="username" maxlength="50">
			</div>
			<div class="tr">
				<label class="textLabel">Validation key:</label>
				<input type="text" name="key" maxlength="10">
			</div>
			<div class="tr">
				<label class="textLabel">New Password:</label>
				<input id="password1" type="password" name="pass1" maxlength="32">
				<div class="alert">
					<div id="passBlank" class="<?=(isset($errors['passBlank'])?'showDiv':'hideDiv')?>">Password cannot be blank</div>
					<div id="passShort" class="<?=(isset($errors['passShort'])?'showDiv':'hideDiv')?>">Password too short</div>
					<div id="passLong" class="<?=(isset($errors['passLong'])?'showDiv':'hideDiv')?>">Password too long</div>
				</div>
			</div>
			<div class="notice">Password must be between 6 and 32 characters long</div>
			<div class="tr">
				<label class="textLabel">Repeat New Password:</label>
				<input id="password2" type="password" name="pass2" maxlength="32">
				<div class="alert">
					<div id="passMismatch" class="<?=(isset($errors['passMismatch'])?'showDiv':'hideDiv')?>">Passwords don't match</div>
				</div>
			</div>
			<input type="hidden" name="validationStr" value="<?=$validationStr?>">
			<div class="alignCenter"><button type="submit" name="submit" value="submit" class="fancyButton">Submit</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>