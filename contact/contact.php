<?
	$loggedIn = checkLogin(0);
	
	if ($_SESSION['errors']) {
		if (preg_match('/contact\/.*$/', $_SESSION['lastURL'])) {
			$errors = sizeof($_SESSION['errors']);
			foreach ($_SESSION['errorVals'] as $key => $value) { $$key = $value; }
		}
		if (!preg_match('/contact\/.*$/', $_SESSION['lastURL']) || time() > $_SESSION['errorTime']) {
			unset($_SESSION['errors']);
			unset($_SESSION['errorVals']);
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Contact Us</h1>
<? if ($pathOptions[0] == 'failed') { ?>
		<div class="alertBox_error">You left <?=($errors == 1)?'a field':'fields'?> blank! Maybe you just forgot to pick a browser?</div>
<? } ?>
		<form method="post" action="<?=SITEROOT?>/contact/process/">
			<p>All fields except "username" are required.</p>
			<div class="tr">
				<label class="textLabel">Name:</label>
				<div class="inputField"><input type="text" name="name" maxlength="50" value="<?=$name?>"></div>
			</div>
			<div class="tr">
				<label class="textLabel<?=$loggedIn?' loggedIn':''?>">Username:</label>
				<div class="inputField">
<?
	if ($loggedIn) {
		echo "\t\t\t\t\t".$_SESSION['username']."\n";
?>
					<input type="hidden" name="username" value="<?=$_SESSION['username']?>">
<? } else { ?>
					<input type="text" name="username" maxlength="50" value="<?=$username?>">
<? } ?>
				</div>
			</div>
			<div class="tr">
				<label class="textLabel">Email Address:</label>
				<div class="inputField"><input type="text" name="email" maxlength="100" value="<?=$info['email']?$info['email']:$email?>"></div>
			</div>
			<div class="tr">
				<label class="textLabel">Browser:</label>
				<div class="inputField"><select name="browser">
					<option<?=($browser == 'Select One')?' selected="selected"':''?>>Select One</option>
					<option value="Internet Explorer 6"<?=($browser == 'Internet Explorer 6')?' selected="selected"':''?>>Internet Explorer 6</option>
					<option value="Internet Explorer 7"<?=($browser == 'Internet Explorer 7')?' selected="selected"':''?>>Internet Explorer 7</option>
					<option value="Internet Explorer 8"<?=($browser == 'Internet Explorer 8')?' selected="selected"':''?>>Internet Explorer 8</option>
					<option value="Firefox 2"<?=($browser == 'Firefox 2')?' selected="selected"':''?>>Firefox 2</option>
					<option value="Firefox 3"<?=($browser == 'Firefox 3')?' selected="selected"':''?>>Firefox 3</option>
					<option value="Google Chrome"<?=($browser == 'Google Chrome')?' selected="selected"':''?>>Google Chrome</option>
					<option value="Other"<?=($browser == 'Other')?' selected="selected"':''?>>Other (Please list in comments)</option>
				</select></div>
			</div>
			<div id="useJSDiv">
				<input id="useJS" type="checkbox" name="javascript"<? if ($errors) { echo ' class="errors"'; } if ($javascript) { echo ' checked="checked"'; } ?> value="1"> I use Javascript
			</div>
			<div class="tr">
				<label class="textLabel">Subject:</label>
				<div class="inputField"><input type="text" name="subject" maxlength="100" value="<?=$subject?>" class="long"></div>
			</div>
			<div class="tr">
				<label class="textLabel">Comments/Problem:</label>
				<div class="inputField"><textarea name="comment"><?=$comment?></textarea></div>
			</div>
			<div id="submitDiv"><button id="submit" type="submit" name="submit" class="btn_submit"></button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>