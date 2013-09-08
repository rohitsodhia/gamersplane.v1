<?
	$loggedIn = checkLogin(0);
?>
<? require_once(FILEROOT.'/header.php'); ?>
<? if (isset($_GET['resetSuccess'])) { ?>
		<div class="alertBox_success"><ul>
			<li>Password changed!</li>
		</ul></div>
<? } elseif (isset($_GET['redirect'])) { ?>
		<div class="alertBox_error">
			You must be logged in to view this page!
		</div>
<? } ?>
		<form method="post" action="<?=SITEROOT?>/login/process/login">
			<h1>Login</h1>
			
			<div class="alertBox_error<?=$_GET['failed']?'':' hideDiv'?>">
				Your login attempt failed. Please try again.<br>
				<br class="gap">
				Make sure you have registered an account and have activiated your account.
			</div>
			<div class="tr">
				<label class="textLabel">Username</label>
				<div class="textfield"><input id="username" type="text" name="username" maxlength="24" tabindex="3"></div>
			</div>
			<div class="tr">
				<label class="textLabel">Password</label>
				<div class="textfield"><input id="password" type="password" name="password" maxlength="16" tabindex="4"></div>
			</div>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="login" value="login" tabindex="5" class="btn_login"></button></div>
		</form>
		
		<p><a id="register" href="<?=SITEROOT?>/register">Don't have an account? Register for free!</a></a>
		<p><a id="requestReset" href="<?=SITEROOT?>/login/requestReset">Forgot your password?</a></a>
<? require_once(FILEROOT.'/footer.php'); ?>