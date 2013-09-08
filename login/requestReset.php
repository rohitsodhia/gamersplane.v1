<?
	$loggedIn = checkLogin(0);
?>
<? require_once(FILEROOT.'/header.php'); ?>
<? if (isset($_GET['invalidEmail'])) { ?>
		<div class="alertBox_error"><ul>
			<li>Email not found in our system</li>
		</ul></div>

<? } ?>
		<h1 class="headerbar">Reset Password</h1>
<? if (isset($_GET['success'])) { ?>
		<p>You should receieve an email with more instructions shortly.</p>
		<p>If you don't see an email from us, double check your spam folders and make sure you have <span style="text-decoration: underline">contact@gamersplane.com</span> on your whitelist.</p>
<? } else { ?>
		<p>Forgot your password? Just reset it!</p>
		<p>Enter the email address associated with your account below, and we'll help you get a new password.</p>
		<form method="post" action="<?=SITEROOT?>/login/process/requestReset">
<?=MODAL?'			<input type="hidden" name="modal" value="1">'."\n":''?>
			<input type="text" name="email" maxlength="100">
			<button type="submit" name="submit" value="submit" class="fancyButton">Submit</button>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>