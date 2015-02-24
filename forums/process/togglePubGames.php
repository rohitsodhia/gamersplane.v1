<?
	$currentUser->updateUsermeta('showPubGames', (bool) $currentUser->getuserMeta?0:1);

	header("Location: {$_SESSION['lastURL']}");
?>