<?
	if (checkLogin(0)) { header('Location: /'); exit; }
	
	$activateHash = $pathOptions[1]; 
	$userCheck = $mysql->prepare("SELECT userID, username FROM users WHERE MD5(username) = ? AND active = 0");
	$userCheck->execute(array($activateHash));
?>
<? require_once(FILEROOT.'/header.php'); ?>
<?
	if ($activateHash && $userCheck->rowCount()) {
		$userInfo = $userCheck->fetch();
		$mysql->query("UPDATE users SET active = 1 WHERE userID = {$userInfo['userID']}");
		$mysql->query("INSERT INTO forums_groupMemberships (userID, groupID) VALUES ({$userInfo['userID']}, 1)");
?>
		<h1 class="headerbar">Account Activated!</h1>
		<p>Congratulations, <b><?=$userInfo['username']?></b>! Your account has been activiated.</p>
		<p><a href="/login" class="loginLink">Click here</a> to login.</p>
<? } else { ?>
		<h1 class="headerbar">Sorry...</h1>
		<p>Sorry, but the account you are trying to activate has already been activated or does not exist.</p>
		<p>Check to make sure you entered the correct URL.</p>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>