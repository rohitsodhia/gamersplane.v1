<? $responsivePage=true;
require_once(FILEROOT.'/header.php');?>
<?	if ($_GET['passWipe']) { ?>
		<div class="alertBox_info">
			To upgrade security, I've had to reset everyone's password. You can check the <a href="/forums/3/">Announcements</a> forum for more details, but to log in again, please click login at the top right, and click <u>Forgot your Password?</u>. Follow the steps and you'll be good to go!
		</div>
<?	} ?>
		<div class="alertBox_success<?=!isset($_GET['resetSuccess'])?' hideDiv':''?>"><ul>
			<li>Password changed!</li>
		</ul></div>
		<div class="alertBox_error<?=!isset($_GET['redirect'])?' hideDiv':''?>">
			You must be logged in to view this page!
		</div>
		<div id="loginFailed" class="alertBox_error<?=!isset($_GET['failed'])?' hideDiv':''?>">
			Your login attempt failed. Please try again.<br>
			<br class="gap">
			Make sure you have registered an account and have activated your account. If you'd like us to resend you an activation email, <a href="/register/resendActivation/">click here</a>.
		</div>

		<div id="formWrapper">
			<h1 class="headerbar">Login</h1>

			<form method="post" action="/login/process/login/" class="hbMargined">
				<div class="tr">
					<label>Username/Email</label>
					<input id="user" type="text" name="user" tabindex="3">
				</div>
				<div class="tr">
					<label>Password</label>
					<input id="password" type="password" name="password" maxlength="32" tabindex="4">
				</div>
				<div id="submitDiv" class="alignCenter"><button type="submit" name="login" value="login" tabindex="5">Login</button></div>
			</form>
		</div>

		<p><a id="register" href="/register/">Don't have an account? Register for free!</a></p>
		<p><a id="requestReset" href="/login/requestReset/" class="inFrame">Forgot your password?</a></p>
<? require_once(FILEROOT.'/footer.php'); ?>
