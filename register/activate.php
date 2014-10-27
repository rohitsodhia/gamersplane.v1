<?
	if (checkLogin(0)) { header('Location: /'); exit; }
	
	$activateHash = $pathOptions[1]; 
	$userCheck = $mysql->prepare("SELECT userID, username, timezone, joinDate FROM users WHERE MD5(username) = ? AND activatedOn IS NULL");
	$userCheck->execute(array($activateHash));
?>
<? require_once(FILEROOT.'/header.php'); ?>
<?
	if ($activateHash && $userCheck->rowCount()) {
		$userInfo = $userCheck->fetch();
		$mysql->query("UPDATE users SET activatedOn = NOW() WHERE userID = {$userInfo['userID']}");
		$mysql->query("INSERT INTO forums_groupMemberships (userID, groupID) VALUES ({$userInfo['userID']}, 1)");

		$mysql->query('INSERT INTO loginRecords (userID, attemptStamp, ipAddress, successful) VALUES ('.$userInfo['userID'].', NOW(), "'.$_SERVER['REMOTE_ADDR'].'", 1)');
	
		$_SESSION['userID'] = $userInfo['userID'];
		$_SESSION['username'] = $userInfo['username'];
		$_SESSION['timezone'] = $userInfo['timezone'];
		
		setcookie('loginHash', md5(SVAR.$userInfo['username'].$userInfo['joinDate']), time() + (60 * 60 * 24 * 7), COOKIE_ROOT);
?>
		<h1 class="headerbar">Account Activated!</h1>
		<p>Congratulations, <b><?=$userInfo['username']?></b>! Your account has been activiated.</p>
		<p>We recommend you check out the <a href="/faqs/">FAQs</a> to get an idea of what you can do to get started. You can also head straight to make a new <a href="/characters/my/">character</a> or find a <a href="/games/list/">game</a>, and be sure to stop by the <a href="/forums/">forums</a> and <a href="/forums/14/">introduce yourself</a>!</p>
<? } else { ?>
		<h1 class="headerbar">Sorry...</h1>
		<p>Sorry, but the account you are trying to activate has already been activated or does not exist.</p>
		<p>Check to make sure you entered the correct URL.</p>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>