<?php
	if ($loggedIn) { header('Location: /'); exit; }

	$activateHash = $pathOptions[1];
	$userCheck = $mysql->prepare("SELECT userID FROM users WHERE MD5(username) = ? AND activatedOn IS NULL");
	$userCheck->execute(array($activateHash));

	if ($activateHash && $userCheck->rowCount()) {
		$userID = $userCheck->fetchColumn();
		$currentUser = new User($userID);
		$mysql->query("UPDATE users SET activatedOn = NOW() WHERE userID = {$userID}");
		$mysql->query("INSERT INTO forums_groupMemberships (userID, groupID) VALUES ({$userID}, 1)");
		$currentUser->updateUsermeta('enableFilter', 1, true);
		$currentUser->updateUsermeta('showAvatars', 1, true);
		$currentUser->updateUsermeta('pmMail', 1);
		$currentUser->updateUsermeta('newGameMail', 1);
		$currentUser->updateUsermeta('gmMail', 1);
		$currentUser->updateUsermeta('postSide', 'r', true);
		$currentUser->updateUsermeta('showPubGames', 1, true);
		$currentUser->updateUsermeta('showPubGames', 1, true);

		$mysql->query('INSERT INTO loginRecords (userID, attemptStamp, ipAddress, successful) VALUES (' . $userID . ', NOW(), "' . $_SERVER['REMOTE_ADDR'] . '", 1)');

		$currentUser->generateLoginCookie();
		$loggedIn = true;
	}

	require_once(FILEROOT . '/header.php');

	if (isset($currentUser) && $currentUser != null) {
?>
		<h1 class="headerbar">Account Activated!</h1>
		<p>Congratulations, <b><?=$currentUser->username?></b>! Your account has been activiated.</p>
		<p>We recommend you check out the <a href="/faqs/">FAQs</a> and our <a href="http://gamersplane.com/forums/thread/2461/">New Player Guide</a> to get an idea of what you can do to get started. You can also head straight to make a new <a href="/characters/my/">character</a> or find a <a href="/games/list/">game</a>, and be sure to stop by the <a href="/forums/">forums</a> and <a href="/forums/14/">introduce yourself</a>!</p>
<?php	} else { ?>
		<h1 class="headerbar">Sorry...</h1>
		<p>Sorry, but the account you are trying to activate has already been activated or does not exist.</p>
		<p>Check to make sure you entered the correct URL.</p>
<?php
	}
	require_once(FILEROOT . '/footer.php');
?>
