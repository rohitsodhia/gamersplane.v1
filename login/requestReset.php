<?
	$loggedIn = checkLogin(0);
?>
<? require_once(FILEROOT.'/header.php'); ?>
<? if (isset($_GET['invalidEmail'])) { ?>
		<div class="alertBox_error"><ul>
			<li>Email not found in our system</li>
		</ul></div>
<? } ?>
		<h1>Reset Password</h1>
		<p>Forgot your password? Just reset it!</p>
		<p>Enter the email address associated with your account below, and we'll email you a new, temporary password.<p>
		<form method="post" action="<?=SITEROOT?>/login/process/requestReset">
			<input type="text" name="email" maxlength="100">
			<button type="submit" name="submit" value="submit" class="btn_submit"></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>