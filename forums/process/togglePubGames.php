<?
	$currentUser->updateUsermeta('showPubGames', (bool) $currentUser->showPubGames?0:1);

	header("Location: {$_SESSION['lastURL']}");
?>