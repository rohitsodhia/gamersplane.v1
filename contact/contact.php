<?
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
		<h1 class="headerbar">Contact Us</h1>
<? if ($pathOptions[0] == 'failed') { ?>
		<div class="alertBox_error">You left <?=($errors == 1)?'a field':'fields'?> blank! Maybe you just forgot to pick a browser?</div>
<? } ?>
		<div id="jsError" class="alertBox_error hideDiv">Something went wrong.<br>Did you fill out all the fields and enter a valid email?</div>
		<form method="post" action="/contact/process/">
			<p>All fields except "username" are required.</p>
			<div class="tr">
				<label>Name:</label>
				<div class="inputField"><input type="text" name="name" maxlength="50" value="<?=$name?>"></div>
			</div>
			<div class="tr">
				<label class="textLabel<?=$loggedIn?' loggedIn':''?>">Username:</label>
				<div class="inputField">
<?
	if ($loggedIn) {
		echo "\t\t\t\t\t{$currentUser->username}\n";
?>
					<input type="hidden" name="username" value="<?=$currentUser->username?>">
<? } else { ?>
					<input type="text" name="username" maxlength="50" value="<?=$username?>">
<? } ?>
				</div>
			</div>
			<div class="tr">
				<label>Email Address:</label>
				<div class="inputField"><input type="text" name="email" maxlength="100" value="<?=$info['email']?$info['email']:$email?>"></div>
			</div>
			<div class="tr">
				<label>Subject:</label>
				<div class="inputField"><input type="text" name="subject" maxlength="100" value="<?=$subject?>" class="long"></div>
			</div>
			<div class="tr">
				<label>Comment:</label>
				<div class="inputField"><textarea name="comment"><?=$comment?></textarea></div>
			</div>
			<div id="submitDiv" class="alignCenter"><button id="submit" type="submit" name="submit" class="fancyButton">Submit</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>