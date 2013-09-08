<?
	if (checkLogin(0)) { header('Location: '.SITEROOT.'/'); exit; }
	
	$activateHash = $pathOptions[1]; 
	$userCheck = $mysql->query("SELECT userID, username FROM users WHERE MD5(username) = '$activateHash' AND active = 0")
?>
<? require_once(FILEROOT.'/header.php'); ?>
<?
	if ($activateHash && $userCheck->rowCount()) {
		$userInfo = $userCheck->fetch();
		$mysql->query("UPDATE users SET active = 1 WHERE userID = {$userInfo['userID']}");
		$mysql->query("INSERT INTO forums_groupMemberships (userID, groupID) VALUES ({$userInfo['userID']}, 1)");
//		$mysql->query("INSERT INTO forums_readData (userID, readData) VALUES ({$userInfo['userID']}, '".sanatizeString('a:1:{i:0;}}')."')");
?>
		<h1>Account Activated!</h1>
		<p>Congratulations, <b><?=$userInfo['username']?></b>! Your account has been activiated.</p>
		<p><a href="<?=SITEROOT?>/login">Click here</a> to login.</p>
<? } elseif ($activateHash) { ?>
		<h1>Sorry...</h1>
		<p>Sorry, but the account you are trying to activate has already been activated or does not exist.</p>
		<p>Check to make sure you entered the correct URL.</p>
<? } else { ?>
		<h1 style="margin: 45px 0px;">Sorry, but the URL you've entered is incorrect.</h1>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>